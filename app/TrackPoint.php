<?php
/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 02.09.2016
 * Time: 19:41
 */

namespace app;

class TrackPoint
{
    public $time;
    public $duration;
    public $distanceMeters;
    public $heartRateBpm;
    public $latitudeDegrees;
    public $longitudeDegrees;
    public $altitudeMeters;
    public $sensorState;

    public function __construct($time,
                                $duration,
                                $distanceMeters,
                                $heartRateBpm,
                                $latitudeDegrees,
                                $longitudeDegrees,
                                $altitudeMeters,
                                $sensorState)
    {
        $this->time = $time;
        $this->duration = $duration;
        $this->distanceMeters = $distanceMeters;
        $this->heartRateBpm = $heartRateBpm;
        $this->latitudeDegrees = $latitudeDegrees;
        $this->longitudeDegrees = $longitudeDegrees;
        $this->altitudeMeters = $altitudeMeters;
        $this->sensorState = $sensorState;
    }
}