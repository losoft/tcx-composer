<?php

namespace App\Http\Controllers;

use App\Workout;
use Illuminate\Http\Request;

use App\Http\Requests;
use Ramsey\Uuid\Uuid;

class WorkoutController extends Controller
{
    public $timestamps = false;

    public function workouts()
    {
        $data = array(
            'workouts' => Workout::all()
        );
        return view('workouts', $data);
    }

    public function upload()
    {
        $data = array(
        );
        return view('workouts-upload', $data);
    }

    public function uploaded(Request $request)
    {
        $workout = new Workout();
        $workout->id = Uuid::uuid4();
        $workout->name = $request->get('name');
        $workout->track = file_get_contents($request->file('tcx')->getRealPath());

        $status = 'FAILED';
        if (WorkoutController::validateWorkout($workout) == true) {
            $status = 'SUCCESS';
            $workout->save();
        }

        $data = array(
            'status' => $status,
            'workout' => $workout,
        );
        return view('workouts-uploaded', $data);
    }

    public function show($id)
    {
        return view('workouts.show', ['workout' => Workout::findOrFail($id)]);
    }

    public static function validateWorkout($workout) {
        // TODO: tcx validation
        return true;
    }
}
