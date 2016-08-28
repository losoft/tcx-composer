<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class MainController extends Controller
{
    public function welcome()
    {
        $data = array(
            'path' => '/welcome'
        );
        return view('welcome', $data);
    }
}
