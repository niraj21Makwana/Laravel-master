@extends('default')

@section('content')


    @include('prob-notice')


    <div class="container">
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

        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('prizes.create') }}" class="btn btn-info">Create</a>
                </div>
                <h1>Prizes</h1>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Title</th>
                            <th>Probability</th>
                            <th>Awarded</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prizes as $prize)
                            <tr>
                                <td>{{ $prize->id }}</td>
                                <td>{{ $prize->title }}</td>
                                <td>{{ $prize->probability }}</td>
                                <td>{{ $prize->awarded }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('prizes.edit', [$prize->id]) }}" class="btn btn-primary">Edit</a>
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['prizes.destroy', $prize->id]]) !!}
                                        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                                        {!! Form::close() !!}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h3>Simulate</h3>
                    </div>
                    <div class="card-body">
                        {!! Form::open(['method' => 'POST', 'route' => ['simulate']]) !!}
                        <div class="form-group">
                            {!! Form::label('number_of_prizes', 'Number of Prizes') !!}
                            {!! Form::number('number_of_prizes', 10, ['class' => 'form-control']) !!}
                        </div>
                        {!! Form::submit('Simulate', ['class' => 'btn btn-primary']) !!}
                        {!! Form::close() !!}
                    </div>
                    <br>
                    <div class="card-body">
                        {!! Form::open(['method' => 'POST', 'route' => ['reset']]) !!}
                        {!! Form::submit('Reset', ['class' => 'btn btn-primary']) !!}
                        {!! Form::close() !!}
                    </div>

                </div>
            </div>
        </div>
    </div>



    <div class="container  mb-4">
        <div class="row">
            <div class="col-md-6">
                <h2>Probability Settings</h2>
                <canvas id="probabilityChart"></canvas>
            </div>
            <div class="col-md-6">
                <h2>Actual Rewards</h2>
                <canvas id="awardedChart"></canvas>
            </div>
        </div>
    </div>
@stop


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <script>
        let probabilityLabels = [];
        let probabilityValues = [];
        let probabilityColors = [];
        let rewardLabels = [];
        let rewardValues = [];
        let rewardColors = [];

        @foreach ($prizes as $prize)
            probabilityLabels.push('{{ $prize->title }}');
            probabilityValues.push('{{ $prize->probability }}');
            probabilityColors.push(getRandomColor());
        @endforeach

        @foreach ($rewards as $reward)
            rewardLabels.push('{{ $reward->prize->title }}');
            rewardValues.push('{{ $reward->percentage_of_winners }}');
            rewardColors.push(getRandomColor());
        @endforeach

        var probabilityCanvas = document.getElementById('probabilityChart').getContext('2d');
        var rewardCanvas = document.getElementById('awardedChart').getContext('2d');
        createChart(probabilityLabels, probabilityValues, probabilityColors, probabilityCanvas);
        createChart(rewardLabels, rewardValues, rewardColors, rewardCanvas);



        //comman functions
        function createChart(chartLabels, chartValues, chartsColor, chartId) {
            let data = {
                labels: chartLabels,
                datasets: [{
                    data: chartValues,
                    backgroundColor: chartsColor
                }]
            };
            new Chart(chartId, {
                type: 'doughnut',
                data: data,
                options: {
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.parsed.toFixed(2) + '%';
                                }
                            }
                        },
                    }
                }
            })
        };

        function getRandomColor() {
            var letters = '0123456789ABCDEF';
            var color = '#';
            for (var i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }
    </script>
@endpush
