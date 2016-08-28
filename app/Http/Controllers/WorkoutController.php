<?php

namespace App\Http\Controllers;

use App\Workout;
use Illuminate\Http\Request;

use App\Http\Requests;

class WorkoutController extends Controller
{
    public function workouts()
    {
        $data = array(
            'path' => '/workouts',
            'workouts' => Workout::all()
        );
        return view('workouts', $data);
    }

    public function show($id)
    {
        return view('workouts.show', ['workout' => Workout::findOrFail($id)]);
    }
}
