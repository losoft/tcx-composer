@extends('layouts.app')

@section('title')
    <title>TCX Composer - Workouts</title>
@endsection

@section('content')
    <div class="container">
        <h2 class="page-header">Workouts</h2>
        <div class="alert alert-warning alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            <strong>Not logged.</strong> Without login you will lose access to
            your workouts upload and processing history after this session expires. Don't lose your session cookie ;)
        </div>
        <div><a class="btn btn-primary pull-right" href="{{ url('/workouts/upload') }}" role="button">Upload training</a></div>
        <h3 class="page-header">Uploaded trainings</h3>
        @foreach($workouts as $workout)
            <div class="panel panel-default">
                <nav class="navbar navbar-default">
                    <div class="container-fluid">
                        <div class="navbar-header">
                            <a href="#" class="navbar-brand">{{$workout->name}}</a></div>
                        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-5"><p
                                    class="navbar-text navbar-right">{{$workout->created_at}}</p></div>
                    </div>
                </nav>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="panel-body">
                            <p>{{ $workout->id }}</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="panel-body">
                            <p>{{ $workout->name }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
