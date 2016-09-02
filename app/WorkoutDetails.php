<?php
/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 02.09.2016
 * Time: 19:39
 */

namespace app;

use App\WorkoutSummary as WorkoutSummary;

class WorkoutDetails extends WorkoutSummary
{
    public $trackPoints;

    public function __construct($name, $type, $startTime, $duration, $calories, $distance, $speed, $pace,
                                $averageHeartRate, $maximumHeartRate, $trackPoints = array())
    {
        $this->name = $name;
        $this->type = $type;
        $this->startTime = $startTime;
        $this->duration = $duration;
        $this->calories = $calories;
        $this->distance = $distance;
        $this->speed = $speed;
        $this->pace = $pace;
        $this->averageHeartRate = $averageHeartRate;
        $this->maximumHeartRate = $maximumHeartRate;
        $this->trackPoints = $trackPoints;
    }

    public function add($trackPoint)
    {
        $this->trackPoints[] = $trackPoint;
    }

    public static function collectHeartRateBpm()
    {
        $heartRateBpms = array();
        foreach (self::trackPoints as $trackPoint) {
            if ($trackPoint->heartRateBpm === false) {
                continue;
            }
            $heartRateBpms[] = $trackPoint->heartRateBpm;
        }
        return $heartRateBpms;
    }

    public function collectDistanceMeters()
    {
        $distancesMeters = array();
        foreach ($this->trackPoints as $trackPoint) {
            if ($trackPoint->distanceMeters === false) {
                continue;
            }
            $distancesMeters[] = $trackPoint->distanceMeters;
        }
        return $distancesMeters;
    }

    public static function collectAltitudeMeters()
    {
        $altitudesMeters = array();
        foreach (self::trackPoints as $trackPoint) {
            if ($trackPoint->altitudeMeters === false) {
                continue;
            }
            $altitudesMeters[] = $trackPoint->altitudeMeters;
        }
        return $altitudesMeters;
    }

    public static function collectPositions()
    {
        $positions = array();
        foreach (self::trackPoints as $trackPoint) {
            if ($trackPoint->latitudeDegrees === false && $trackPoint->longitudeDegrees === false) {
                continue;
            }
            $positions[] = array(
                'latitudeDegrees' => $trackPoint->latitudeDegrees,
                'longitudeDegrees' => $trackPoint->longitudeDegrees
            );
        }
        return $positions;
    }
}
