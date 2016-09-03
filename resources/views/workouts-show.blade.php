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
    <?php
    function reduce($array, $size)
    {
        $count = count($array);
        $offset = (int)$count / $size;
        $part = 1;
        $newArray = array();
        $newArray[] = $array[0];
        while ($part < $size) {
            $newArray[] = $array[$offset * $part];
            $part++;
        }
        $newArray[] = $array[$count - 1];
        return $newArray;
    }
    ?>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {packages: ['corechart']});
        google.charts.setOnLoadCallback(drawDuration);

        function drawDuration() {
            var dataTable = new google.visualization.DataTable();
            dataTable.addColumn('number', 'Duration');
            dataTable.addColumn('number', 'Altitude');
            dataTable.addColumn({type: 'string', role: 'tooltip', p: {'html': true}});
            dataTable.addColumn('number', 'Heart Rate');
            dataTable.addColumn({type: 'string', role: 'tooltip', p: {'html': true}});

            dataTable.addRows([
                <?php
                foreach ($workout->trackPoints as $trackPoint) {
                    $hms = gmdate("H:i:s", $trackPoint->duration);
                    $html = "<p><b>Durration:</b> $hms</p>" .
                            "<p><b>Altitude:</b> $trackPoint->altitudeMeters</p>" .
                            "<p><b>Heart Rate:</b> $trackPoint->heartRateBpm</p>";
                    echo "[$trackPoint->duration, $trackPoint->altitudeMeters, ".
                            "'$html', $trackPoint->heartRateBpm, '$html' ],";
                }
                ?>
            ]);

            var options = {
                chart: {
                    title: 'Analysis'
                },
                width: 1130,
                height: 350,
                hAxis: {
                    ticks: [
                        <?php
                        $ticks = reduce($workout->trackPoints, 7);
                        foreach ($ticks as $trackPoint) {
                            $hms = gmdate("H:i:s", $trackPoint->duration);
                            echo "{ v: $trackPoint->duration, f: '$hms' },";
                        }
                        ?>
                    ]
                },
                tooltip: {
                    isHtml: true
                },
            };

            var chartDiv = document.getElementById('chart_div');
            var chart = new google.visualization.LineChart(chartDiv);
            chart.draw(dataTable, options);
        }
    </script>
@endsection