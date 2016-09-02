@extends('layouts.app')

@section('title')
    <title>TCX Composer - Workout</title>
@endsection

@section('content')
    <div class="container">
        <h2 class="page-header">Workout details</h2>

        <div id="chart_div"></div>

    </div>
@endsection

@section('javascript')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {packages: ['corechart']});
        google.charts.setOnLoadCallback(drawCrosshairs);

        function drawCrosshairs() {
            var data = new google.visualization.DataTable();
            data.addColumn('datetime', 'Time');
            data.addColumn('number', 'Distance');
            data.addColumn('number', 'Altitude');

            data.addRows([
                    <?php
                    foreach ($workout->trackPoints as $trackPoint) {
                        echo '['
                                . 'new Date("' . $trackPoint->time . '")'
                                . ', '
                                . $trackPoint->distanceMeters
                                . ', '
                                . $trackPoint->altitudeMeters
                                . '],';
                    }

                    ?>
            ]);

            var materialOptions = {
                chart: {
                    title: 'Analysis'
                },
                width: 1130,
                height: 350,
                series: {
                    // Gives each series an axis name that matches the Y-axis below.
                    0: {axis: 'Distance'},
                    1: {axis: 'Altitude'}
                },
                hAxis: {
                    format: 'HH:mm:ss'
                }
            };

            var classicOptions = {
                title: 'Analysis',
                width: 1130,
                height: 350,
                // Gives each series an axis that matches the vAxes number below.
                series: {
                    0: {targetAxisIndex: 0},
                    1: {targetAxisIndex: 1}
                },
                vAxes: {
                    // Adds titles to each axis.
                    0: {title: 'Distance'},
                    1: {title: 'Altitude'}
                },
                hAxis: {
                    format: 'HH:mm:ss'
                }
            };

            //var formatter = new google.visualization.DateFormat({pattern: 'HH:mm:ss'});
            //formatter.format(data, 0);
            var chartDiv = document.getElementById('chart_div');
            var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
           //var materialChart = new google.charts.Line(chartDiv);
            chart.draw(data, materialOptions);

            //chart.draw(data, options);
            //chart.setSelection([{row: 38, column: 1}]);

        }
    </script>
@endsection