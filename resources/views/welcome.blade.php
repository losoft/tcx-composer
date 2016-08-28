@extends('main')

@section('head')
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <style>
        .welcome {
            padding-top: 100px;
            text-align: center;
        }

        .welcome a {
            color: #636b6f;
            font-family: 'Raleway';
            padding: 0 25px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .title {
            color: #636b6f;
            font-family: 'Raleway';
            font-size: 84px;
            font-weight: 100;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
@endsection

@section('page-title')
    <title>TCX Composer</title>
@endsection

@section('page-content')
    <div class="page-content">
        @if (Route::has('login'))
            <div class="top-right links">
                <a href="{{ url('/login') }}">Login</a>
                <a href="{{ url('/register') }}">Register</a>
            </div>
        @endif

        <div class="welcome">
            <div class="title m-b-md">
                TCX Composer
            </div>

            <div class="row">
                <div class="col-lg-3">
                    <div class="panel-body">
                        <a class="welcome" href="/workouts">Start using</a>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel-body">
                        <a class="welcome" href="/news">News</a>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel-body">
                        <a class="welcome" href="/docs">Documentation</a>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel-body">
                        <a class="welcome" href="https://github.com/losoft/tcx-composer">GitHub</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
