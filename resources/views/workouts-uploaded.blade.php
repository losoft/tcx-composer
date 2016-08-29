@extends('layouts.app')

@section('title')
    <title>TCX Composer - Workouts - Uploaded</title>
@endsection

@section('content')
    <div class="container">
        @if($status == 'SUCCESS')
            <h2 class="page-header">Workout upload was successful</h2>
        @endif
        @if($status == 'FAILED')
            <h2 class="page-header">Workout upload has failed</h2>
        @endif

        <?php print_r($workout); ?>
    </div>
@endsection
