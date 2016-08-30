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
            <tbody>
            @foreach($workouts as $workout)
                <tr>
                    <td>{{ $workout->name }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
