@extends('main')

@section('page-title')
    <title>TCX Composer - Workouts</title>
@endsection

@section('page-content')
    <div class="page-content">
        <h1 class="page-header" id="news">Workouts</h1>
        <div class="alert alert-warning alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            <span class="glyphicon glyphicon-warning-sign" aria-hidden="false"></span>
            <strong>Not logged.</strong> Without login you will lose access to
            your workouts upload and processing history after this session expires. Don't lose your session cookie ;)
        </div>
        <div><a class="btn btn-primary pull-right" href="{{ url('/workouts/upload') }}" role="button">Upload training</a></div>
        <h2 class="page-header" id="news">Uploaded trainings</h2>
        @foreach($workouts as $workout)
            <div class="panel panel-default">
                <nav class="navbar navbar-default">
                    <div class="container-fluid">
                        <div class="navbar-header">
                            <a href="#" class="navbar-brand">{{$workout->name}}</a></div>
                        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-5"><p
                                    class="navbar-text navbar-right">{{$workout->timestamp}}</p></div>
                    </div>
                </nav>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="panel-body">
                            <p>{{ $workout }}</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="panel-body">
                            <p>{{ $workout }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
