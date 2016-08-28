<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class MergeController extends Controller
{
    public function merge()
    {
        $data = array(
        );
        return view('merge', $data);
    }
}
