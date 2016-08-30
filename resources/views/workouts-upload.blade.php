@extends('layouts.app')

@section('title')
    <title>TCX Composer - Workouts - Upload</title>
@endsection

@section('content')
    <div class="container">
        <h2 class="page-header">Upload a new workout file</h2>
        @if($status == 'FAILED')
            <div class="alert alert-danger" role="alert">
                <strong>Please correct your data: </strong> {{ $validation }}
            </div>
        @endif
        {!! Form::open(array('url' => url('/workouts/upload'), 'method' => 'post', 'files' => true, 'class' => 'form-horizontal')) !!}
        <div class="form-group">
            {!! Form::label('name', 'Workout name:', array('class' =>'control-label col-sm-2')) !!}
            <div class="col-sm-10">
                {!! Form::text('name', $name, array('class' => 'form-control', 'placeholder' => 'Enter workout name')) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('tcx', 'TCX file:', array('class' =>'control-label col-sm-2')) !!}
            <div class="col-sm-10">
                {!! Form::file('tcx', array('class' =>'form-control')) !!}
                <p class="help-block">Select TXC file</p>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                {!! Form::submit('Upload', array('class' => 'btn btn-primary')) !!}
                <a href="{{ url('/workouts') }}" class="btn btn-default pull-right">Cancel</a>
            </div>
        </div>
        {!! Form::close()  !!}
    </div>
@endsection
