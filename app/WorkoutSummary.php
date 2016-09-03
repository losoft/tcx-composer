<?php
/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 01.09.2016
 * Time: 19:55
 */

namespace app;

class WorkoutSummary
{
    public $id;
    public $name;
    public $type;
    public $startTime;
    public $duration;
    public $calories;
    public $distance;
    public $speed;
    public $pace;
    public $averageHeartRate;
    public $maximumHeartRate;

    public function __construct($id, $name, $type, $startTime, $duration, $calories, $distance, $speed, $pace,
                                $averageHeartRate, $maximumHeartRate)
    {
        $this->id = $id;
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
    }
}
