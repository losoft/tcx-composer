<?php

namespace App\Http\Controllers;

use App\TrackPoint;
use App\Workout;
use App\WorkoutDetails;
use App\WorkoutSummary;
use DateTime;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
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
        $workout = Workout::where([
            ['id', '=', $id],
            ['user', '=', Auth::user()->id]
        ])->first();
        if ($workout == null) {
            return Response::view('error-404', array(), 404);
        }
        $workoutDetails = self::workoutDetails($workout);
        $data = array(
            'workout' => $workoutDetails
        );
        return view('workouts-show', $data);
    }

    /**
     * @param $request
     * @return Workout
     */
    public static function workoutFromRequest($request)
    {
        $workout = new Workout();
        $workout->id = Uuid::uuid4()->toString();
        $workout->name = $request->get('name');
        $workout->track = $request->exists('tcx') && $request->file('tcx')->isValid()
            ? file_get_contents($request->file('tcx')->getRealPath()) : '';
        $workout->user = Auth::user()->id;
        return $workout;
    }

    /**
     * @param $workout
     * @return object
     */
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

    /**
     * @param $name
     * @return string
     */
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

    /**
     * @param $track
     * @return string
     */
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

        $result = new WorkoutSummary($workout->id, $workout->name, $type, $startTime, $duration, $calories,
            $distance, $speed, $pace, $averageHeartRate, $maximumHeartRate);

        return $result;
    }

    /**
     * @param $workout
     * @return WorkoutDetails
     */
    public static function workoutDetails($workout)
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

        $result = new WorkoutDetails($workout->id, $workout->name, $type, $startTime, $duration, $calories,
            $distance, $speed, $pace, $averageHeartRate, $maximumHeartRate);

        $xmlTrackPoints = $xml->Activities->Activity->Lap->Track->children();
        foreach ($xmlTrackPoints as $xmlTrackPoint) {
            $trackPoint = self::workoutTrackPoint($xmlTrackPoint);
            $result->add($trackPoint);
        }

        return $result;
    }

    private static function workoutTrackPoint($xmlTrackPoint)
    {
        $time = (string)$xmlTrackPoint->Time;
        $distanceMeters = (float)$xmlTrackPoint->DistanceMeters;
        $heartRateBpm = (int)$xmlTrackPoint->HeartRateBpm->Value;
        $latitudeDegrees = (float)$xmlTrackPoint->Position->LatitudeDegrees;
        $longitudeDegrees = (float)$xmlTrackPoint->Position->LongitudeDegrees;
        $altitudeMeters = (int)$xmlTrackPoint->AltitudeMeters;
        $sensorState = (string)$xmlTrackPoint->SensorState;

        return new TrackPoint($time, $distanceMeters, $heartRateBpm, $latitudeDegrees,
            $longitudeDegrees, $altitudeMeters, $sensorState);
    }

    const ISO8601U = 'Y-m-d\TH:i:s.uO';

    /**
     * @param $iso8601DateTime
     * @return false|string
     */
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
