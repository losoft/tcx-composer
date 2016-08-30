<?php

namespace App\Http\Controllers;

use App\Workout;
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
        $data = array(
            'workouts' => $workouts
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

        $workout = WorkoutController::workoutFromRequest($request);
        $report = WorkoutController::validateWorkout($workout);
        if ($report->valid) {
            $workout->save();

            $data->status = 'SUCCESS';
            $data->workout = $workout;
            return view('workouts-uploaded', (array) $data);
        }
        $data->validation = $report->validation;
        $data->status = 'FAILED';
        return view('workouts-upload', (array) $data);
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
        $report->validation .= WorkoutController::validateName($workout->name);
        $report->validation .= (!empty($report->validation) ? ' ' : '')
            . WorkoutController::validateTrack($workout->track);
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
        } catch(\Exception $e){
            return 'Training data is corrupted or has unsupported format.';
        }
        return '';
    }
}
