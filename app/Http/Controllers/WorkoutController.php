<?php

namespace App\Http\Controllers;

use App\Workout;
use App\WorkoutSummary;
use DateTime;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class WorkoutController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function workouts()
    {
        $workouts = Workout::where('user', '=', Auth::user()->id)->get();
        $workoutsSummary = array();
        $i = 0;
        foreach ($workouts as $workout) {
            $workoutsSummary[] = self::workoutSummary($workout);
        }
        $data = array(
            'workouts' => $workoutsSummary
        );
        return view('workouts', $data);
    }

    public function upload()
    {
        $data = array(
            'status' => 'UNKNOWN',
            'name' => ''
        );
        return view('workouts-upload', $data);
    }

    public function uploaded(Request $request)
    {
        $data = (object)array(
            'status' => 'UNKNOWN',
            'name' => $request->get('name'),
            'tcx' => $request->exists('tcx') ? $request->file('tcx')->getFilename() : ''
        );

        $workout = self::workoutFromRequest($request);
        $report = self::validateWorkout($workout);
        if ($report->valid) {
            $workout->save();

            $data->status = 'SUCCESS';
            $data->workout = self::workoutSummary($workout);
            return view('workouts-uploaded', (array)$data);
        }
        $data->validation = $report->validation;
        $data->status = 'FAILED';
        return view('workouts-upload', (array)$data);
    }

    public function show($id)
    {
        return view('workouts.show', ['workout' => Workout::findOrFail($id)]);
    }

    public static function workoutFromRequest($request)
    {
        $workout = new Workout();
        $workout->id = Uuid::uuid4();
        $workout->name = $request->get('name');
        $workout->track = $request->exists('tcx') && $request->file('tcx')->isValid()
            ? file_get_contents($request->file('tcx')->getRealPath()) : '';
        $workout->user = Auth::user()->id;
        return $workout;
    }

    public static function validateWorkout($workout)
    {
        $report = (object)array(
            'validation' => '',
            'valid' => false
        );
        $report->validation .= self::validateName($workout->name);
        $report->validation .= (!empty($report->validation) ? ' ' : '')
            . self::validateTrack($workout->track);
        $report->valid = empty($report->validation) ? true : false;
        return $report;
    }

    public static function validateName($name)
    {
        if (empty($name)) {
            return 'Name can not be empty.';
        }
        if (strlen($name) < 2) {
            return 'Name value is to short.';
        }
        if (strlen($name) > 255) {
            return 'Name value is to long.';
        }
        return '';
    }

    public static function validateTrack($track)
    {
        if (empty($track)) {
            return 'Training data can not be empty.';
        }
        try {
            $xml = SimpleXML_Load_String($track);
            if ($xml->getName() != 'TrainingCenterDatabase') {
                return 'Training data has unsupported format.';
            }
            $trackpoints = $xml->Activities->Activity->Lap->Track->children();
            if ($trackpoints->count() == 0) {
                return 'Training data does not contain any trackpoints.';
            }
            // TODO: read summary data and check if exists
        } catch (\Exception $e) {
            return 'Training data is corrupted or has unsupported format.';
        }
        return '';
    }

    /**
     * @param $workout
     * @return WorkoutSummary
     */
    public static function workoutSummary($workout)
    {
        $xml = SimpleXML_Load_String($workout->track);
        $type = (string)$xml->Activities->Activity['Sport'];
        $startTime = self::normalizeISO8601((string)$xml->Activities->Activity->Lap['StartTime']);
        $duration = (float)$xml->Activities->Activity->Lap->TotalTimeSeconds;
        $distance = (float)$xml->Activities->Activity->Lap->DistanceMeters;
        // TODO: get the speed and pace from track data
        $speed = 0;
        $pace = 0;
        $averageHeartRate = (int)$xml->Activities->Activity->Lap->AverageHeartRateBpm->Value;
        $maximumHeartRate = (int)$xml->Activities->Activity->Lap->MaximumHeartRateBpm->Value;
        $calories = (int)$xml->Activities->Activity->Lap->Calories;
        $summary = new WorkoutSummary($workout->name, $type, $startTime, $duration, $calories, $distance, $speed, $pace,
            $averageHeartRate, $maximumHeartRate);
        return $summary;
    }

    const ISO8601U = 'Y-m-d\TH:i:s.uO';

    private static function normalizeISO8601($iso8601DateTime)
    {
        // FIXME: check the time-zone and setup correct offset
        $dateTime = DateTime::createFromFormat(self::ISO8601U, $iso8601DateTime);
        if ($dateTime !== false) {
            return gmdate('Y-m-d\TH:i:s\Z', $dateTime->getTimestamp());
        }
        return $iso8601DateTime;
    }
}
