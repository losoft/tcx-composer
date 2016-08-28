@extends('main')

@section('page-title')
    <title>TCX Composer - Workouts - Uploaded</title>
@endsection

@section('page-content')
    <div class="page-content">
        @if($status == 'SUCCESS')
            <h1 class="page-header" id="news">Workout upload was successful</h1>
        @endif
        @if($status == 'FAILED')
            <h1 class="page-header" id="news">Workout upload has failed</h1>
        @endif

        <?php print_r($workout); ?>
    </div>
@endsection
