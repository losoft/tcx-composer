<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class NewsController extends Controller
{
    public function news()
    {
        $data = array(
        );
        return view('news', $data);
    }
}
