@extends('layouts.app')

@section('title')
    <title>TCX Composer - Workouts</title>
@endsection

@section('content')
    <div class="container">
        <h2 class="page-header">Workouts</h2>
        @if (Auth::guest())
            <div class="alert alert-warning alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <strong>Not logged.</strong> Without login you will lose access to
                your workouts upload and processing history after this session expires. Don't lose your session cookie
                ;)
            </div>
        @endif
        <div><a class="btn btn-primary pull-right" href="{{ url('/workouts/upload') }}" role="button">Upload
                training</a></div>
        <h3 class="page-header">Uploaded trainings</h3>
        <table class="table table-hover">
            <thead>
            <th class="text-left">Name</th>
            <th class="text-left">Type</th>
            <th class="text-left">Start Time</th>
            <th class="text-right">Duration</th>
            <th class="text-right">Calories</th>
            <th class="text-right">Distance</th>
            <th class="text-right">Speed (km/h)</th>
            <th class="text-right">Pace (min/km)</th>
            <th class="text-right">Average Heart Rate</th>
            <th class="text-right">Maximum Heart Rate</th>
            </thead>
            <tbody>
            @foreach($workouts as $workout)
                <tr>
                    <td class="text-left"><a href="<?php echo url('/workouts/' . $workout->id) ?>">{{ $workout->name }}</a>
                    </td>
                    <td class="text-left">{{ $workout->type }}</td>
                    <td class="text-left">{{ $workout->startTime }}</td>
                    <td class="text-right">{{ gmdate("H:i:s", $workout->duration) }}</td>
                    <td class="text-right">{{ $workout->calories }}</td>
                    <td class="text-right">{{ round($workout->distance, 2) }} m</td>
                    <td class="text-right">{{ $workout->speed }}</td>
                    <td class="text-right"></td>
                    <td class="text-right">{{ $workout->averageHeartRate }}</td>
                    <td class="text-right">{{ $workout->maximumHeartRate }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
