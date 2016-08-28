<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class NewsController extends Controller
{
    public function news()
    {
        $data = array(
            'path' => '/news'
        );
        return view('news', $data);
    }
}
