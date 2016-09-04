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
            //dataTable.addColumn('number', 'Altitude');
            //dataTable.addColumn({type: 'string', role: 'tooltip', p: {'html': true}});
            dataTable.addColumn('number', 'Heart Rate');
            dataTable.addColumn({type: 'string', role: 'tooltip', p: {'html': true}});
            dataTable.addColumn('number', 'Speed');
            dataTable.addColumn({type: 'string', role: 'tooltip', p: {'html': true}});
            //dataTable.addColumn('number', 'Pace');
            //dataTable.addColumn({type: 'string', role: 'tooltip', p: {'html': true}});

            dataTable.addRows([
                <?php
                foreach ($workout->trackPoints as $trackPoint) {
                    $hms = gmdate("H:i:s", $trackPoint->duration);
                    $distance = round($trackPoint->distanceMeters, 0);
                    $speed_kmh = round(($trackPoint->speed * 18) / 5, 2);
                    $pace = $speed_kmh == 0 ? 0 : 60 / $speed_kmh;
                    $pace = $pace * 60;
                    $pace_min = floor($pace / 60);
                    $pace_sec = $pace % 60;
                    $pace_sec = ($pace_sec > 9) ? $pace_sec : "0$pace_sec";
                    $pace_mink = "$pace_min:$pace_sec";
                    $html = "<span><b>Duration:</b> $hms</span><br/>" .
                            "<span><b>Distance:</b> $distance</span><br/>" .
                            //"<span><b>Altitude:</b> $trackPoint->altitudeMeters</span><br/>" .
                            "<span><b>Heart Rate:</b> $trackPoint->heartRateBpm</span><br/>" .
                            "<span><b>Speed:</b> $speed_kmh km/h</span><br/>" .
                            "<span><b>Pace:</b> $pace_mink min/km</span><br/>";
                    echo "[$trackPoint->duration, " .
                            //"$trackPoint->altitudeMeters, '$html', " .
                            "$trackPoint->heartRateBpm, '$html', " .
                            "$speed_kmh, '$html', " .
                            //"$trackPoint->pace, '$html' " .
                            "],";
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
                vAxes: {
                    0: {title: 'Heart Rate'},
                    1: {title: 'Speed'}
                },
                series: {
                    0: {targetAxisIndex: 0},
                    1: {targetAxisIndex: 1}
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