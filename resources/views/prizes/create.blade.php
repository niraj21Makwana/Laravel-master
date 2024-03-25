@extends('default')

@section('content')

    @include('prob-notice')
    @if ($totalProbability == 100)
        <div class="alert alert-success" role="alert">
            Sum of all prizes probability is 100%
        </div>
    @else
        <div class="alert alert-warning" role="alert">
            Sum of all prizes probability is {{ $totalProbability }}%. You have yet to add {{ $remainingProbability }}%
            to reach 100%.
        </div>
    @endif


    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                {{ $error }} <br>
            @endforeach
        </div>
    @endif

    {!! Form::open(['route' => 'prizes.store']) !!}

    <div class="mb-3">
        {{ Form::label('title', 'Title', ['class' => 'form-label']) }}
        {{ Form::text('title', null, ['class' => 'form-control']) }}
    </div>
    <div class="mb-3">
        {{ Form::label('probability', 'Probability', ['class' => 'form-label']) }}
        {{ Form::number('probability', null, ['class' => 'form-control', 'min' => '0', 'max' => '100', 'placeholder' => '0 - 100', 'step' => '0.01']) }}
    </div>


    {{ Form::submit('Create', ['class' => 'btn btn-primary']) }}

    {{ Form::close() }}


@stop
