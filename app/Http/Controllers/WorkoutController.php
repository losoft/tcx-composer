<?php

namespace App\Http\Controllers;

use App\TrackPoint;
use App\Workout;
use App\WorkoutDetails;
use App\WorkoutSummary;
use DateTime;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Ramsey\Uuid\Uuid;

class WorkoutController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function workouts()
    {
        $workouts = Workout::where('user', '=', Auth::user()->id)->get();
        $workoutsSummary = array();
        foreach ($workouts as $workout) {
            $workoutsSummary[] = self::workoutSummary($workout);
        }
        $data = array(
            'workouts' => $workoutsSummary
        );
        return view('workouts', $data);
    }

    public function upload()
    {
        $data = array(
            'status' => 'UNKNOWN',
            'name' => ''
        );
        return view('workouts-upload', $data);
    }

    public function uploaded(Request $request)
    {
        $data = (object)array(
            'status' => 'UNKNOWN',
            'name' => $request->get('name'),
            'tcx' => $request->exists('tcx') ? $request->file('tcx')->getFilename() : ''
        );

        $workout = self::workoutFromRequest($request);
        $report = self::validateWorkout($workout);
        if ($report->valid) {
            $workout->save();

            $data->status = 'SUCCESS';
            $data->workout = self::workoutSummary($workout);
            return view('workouts-uploaded', (array)$data);
        }
        $data->validation = $report->validation;
        $data->status = 'FAILED';
        return view('workouts-upload', (array)$data);
    }

    public function show($id)
    {
        $workout = Workout::where([
            ['id', '=', $id],
            ['user', '=', Auth::user()->id]
        ])->first();
        if ($workout == null) {
            return Response::view('error-404', array(), 404);
        }
        $workoutDetails = self::workoutDetails($workout);
        $data = array(
            'workout' => $workoutDetails
        );
        return view('workouts-show', $data);
    }

    /**
     * @param $request
     * @return Workout
     */
    public static function workoutFromRequest($request)
    {
        $workout = new Workout();
        $workout->id = Uuid::uuid4()->toString();
        $workout->name = $request->get('name');
        $workout->track = $request->exists('tcx') && $request->file('tcx')->isValid()
            ? file_get_contents($request->file('tcx')->getRealPath()) : '';
        $workout->user = Auth::user()->id;
        return $workout;
    }

    /**
     * @param $workout
     * @return object
     */
    public static function validateWorkout($workout)
    {
        $report = (object)array(
            'validation' => '',
            'valid' => false
        );
        $report->validation .= self::validateName($workout->name);
        $report->validation .= (!empty($report->validation) ? ' ' : '')
            . self::validateTrack($workout->track);
        $report->valid = empty($report->validation) ? true : false;
        return $report;
    }

    /**
     * @param $name
     * @return string
     */
    public static function validateName($name)
    {
        if (empty($name)) {
            return 'Name can not be empty.';
        }
        if (strlen($name) < 2) {
            return 'Name value is to short.';
        }
        if (strlen($name) > 255) {
            return 'Name value is to long.';
        }
        return '';
    }

    /**
     * @param $track
     * @return string
     */
    public static function validateTrack($track)
    {
        if (empty($track)) {
            return 'Training data can not be empty.';
        }
        try {
            $xml = SimpleXML_Load_String($track);
            if ($xml->getName() != 'TrainingCenterDatabase') {
                return 'Training data has unsupported format.';
            }
            $trackpoints = $xml->Activities->Activity->Lap->Track->children();
            if ($trackpoints->count() == 0) {
                return 'Training data does not contain any trackpoints.';
            }
            // TODO: read summary data and check if exists
        } catch (\Exception $e) {
            return 'Training data is corrupted or has unsupported format.';
        }
        return '';
    }

    /**
     * @param $workout
     * @return WorkoutSummary
     */
    public static function workoutSummary($workout)
    {
        $xml = SimpleXML_Load_String($workout->track);
        $type = (string)$xml->Activities->Activity['Sport'];
        $startTime = self::normalizeISO8601((string)$xml->Activities->Activity->Lap['StartTime']);
        $duration = (float)$xml->Activities->Activity->Lap->TotalTimeSeconds;
        $distance = (float)$xml->Activities->Activity->Lap->DistanceMeters;
        // TODO: get the speed and pace from track data
        $speed = 0;
        $pace = 0;
        $averageHeartRate = (int)$xml->Activities->Activity->Lap->AverageHeartRateBpm->Value;
        $maximumHeartRate = (int)$xml->Activities->Activity->Lap->MaximumHeartRateBpm->Value;
        $calories = (int)$xml->Activities->Activity->Lap->Calories;

        $result = new WorkoutSummary($workout->id, $workout->name, $type, $startTime, $duration, $calories,
            $distance, $speed, $pace, $averageHeartRate, $maximumHeartRate);

        return $result;
    }

    /**
     * @param $workout
     * @return WorkoutDetails
     */
    public static function workoutDetails($workout)
    {
        $xml = SimpleXML_Load_String($workout->track);
        $type = (string)$xml->Activities->Activity['Sport'];
        // FIXME: read start time from first track point
        $startTime = self::normalizeISO8601((string)$xml->Activities->Activity->Lap['StartTime']);
        $duration = (float)$xml->Activities->Activity->Lap->TotalTimeSeconds;
        $distance = (float)$xml->Activities->Activity->Lap->DistanceMeters;
        // FIXME: get the max and average speed from track data
        // FIXME: max and average speed to WorkoutDetails and WorkoutSummary
        $speed = 0;
        $averageHeartRate = (int)$xml->Activities->Activity->Lap->AverageHeartRateBpm->Value;
        $maximumHeartRate = (int)$xml->Activities->Activity->Lap->MaximumHeartRateBpm->Value;
        $calories = (int)$xml->Activities->Activity->Lap->Calories;

        $result = new WorkoutDetails($workout->id, $workout->name, $type, $startTime, $duration, $calories,
            $distance, $speed, $averageHeartRate, $maximumHeartRate);

        $xmlTrackPoints = $xml->Activities->Activity->Lap->Track->children();
        $totalDistance = 0;
        $lastXmlTrackPoint = $xmlTrackPoints[0];
        foreach ($xmlTrackPoints as $xmlTrackPoint) {
            $trackPoint = self::workoutTrackPoint($lastXmlTrackPoint, $xmlTrackPoint, $totalDistance, $startTime);
            $totalDistance = $trackPoint->distanceMeters;
            $lastXmlTrackPoint = $xmlTrackPoint;
            $result->add($trackPoint);
        }

        return $result;
    }

    private static function workoutTrackPoint($lastXmlTrackPoint, $xmlTrackPoint, $totalDistance, $startTime)
    {
        $time = (string)$xmlTrackPoint->Time;
        $duration = self::calculateDuration($startTime, $time);
        $lastTime = (string)$lastXmlTrackPoint->Time;
        $metersDifference = self::determineDistance($lastXmlTrackPoint, $xmlTrackPoint);
        $distanceMeters = $totalDistance + $metersDifference;
        $timeDifference = self::calculateDuration($lastTime, $time);
        $speed = self::calculateSpeed($metersDifference, $timeDifference);
        $heartRateBpm = (int)$xmlTrackPoint->HeartRateBpm->Value;
        $latitudeDegrees = (float)$xmlTrackPoint->Position->LatitudeDegrees;
        $longitudeDegrees = (float)$xmlTrackPoint->Position->LongitudeDegrees;
        $altitudeMeters = (int)$xmlTrackPoint->AltitudeMeters;
        $sensorState = (string)$xmlTrackPoint->SensorState;

        return new TrackPoint($time, $duration, $distanceMeters, $heartRateBpm, $latitudeDegrees,
            $longitudeDegrees, $altitudeMeters, $sensorState, $speed);
    }

    const ISO8601U = 'Y-m-d\TH:i:s.uO';

    /**
     * @param $iso8601DateTime
     * @return false|string
     */
    private static function normalizeISO8601($iso8601DateTime)
    {
        // FIXME: check the time-zone and setup correct offset
        $dateTime = DateTime::createFromFormat(self::ISO8601U, $iso8601DateTime);
        if ($dateTime !== false) {
            return gmdate('Y-m-d\TH:i:s\Z', $dateTime->getTimestamp());
        }
        return $iso8601DateTime;
    }

    /**
     * Calculates difference in seconds between two dates in ISO8601 format
     * @param $startTime
     * @param $endTime
     * @return int
     */
    private static function calculateDuration($startTime, $endTime)
    {
        if ($startTime == null || $endTime == null) {
            return 0;
        }
        $startTimeDate = DateTime::createFromFormat(DateTime::ISO8601, self::normalizeISO8601($startTime));
        $endTimeDate = DateTime::createFromFormat(DateTime::ISO8601, self::normalizeISO8601($endTime));
        $duration = $endTimeDate->getTimestamp() - $startTimeDate->getTimestamp();
        return $duration;
    }

    /**
     * @param $time between two track points in seconds
     * @param $distance between two track points in meters
     * @return float
     */
    private static function calculateSpeed($distance, $time)
    {
        if ($distance == 0 || $time == 0) {
            return 0;
        }
        return (float)($distance / $time);
    }

    private static function determineDistance($firstXmlTrackPoint, $secondXmlTrackPoint)
    {
        if ($firstXmlTrackPoint == null || $secondXmlTrackPoint == null) {
            return (float)0;
        }
        if (empty($firstXmlTrackPoint->Position) || empty($secondXmlTrackPoint->Position)) {
            return (float)$secondXmlTrackPoint->DistanceMeters - (float)$firstXmlTrackPoint->DistanceMeters;
        }
        $firstLatitudeDegrees = (float)$firstXmlTrackPoint->Position->LatitudeDegrees;
        $firstLongitudeDegrees = (float)$firstXmlTrackPoint->Position->LongitudeDegrees;
        $secondLatitudeDegrees = (float)$secondXmlTrackPoint->Position->LatitudeDegrees;
        $secondLongitudeDegrees = (float)$secondXmlTrackPoint->Position->LongitudeDegrees;

        return (float)self::calculateDistance($firstLatitudeDegrees, $firstLongitudeDegrees,
            $secondLatitudeDegrees, $secondLongitudeDegrees);
    }

    private static function calculateDistance($firstLatitudeDegrees, $firstLongitudeDegrees,
                                              $secondLatitudeDegrees, $secondLongitudeDegrees)
    {
        if ($firstLatitudeDegrees == null || $firstLongitudeDegrees == null
            || $secondLatitudeDegrees == null || $secondLongitudeDegrees == null) {
            return (float) 0;
        }

        return self::haversineGreatCircleDistance($firstLatitudeDegrees, $firstLongitudeDegrees,
            $secondLatitudeDegrees, $secondLongitudeDegrees);
    }

    /**
     * Calculates the great-circle distance between two points, with the Haversine formula.
     * @see http://stackoverflow.com/questions/10053358/measuring-the-distance-between-two-coordinates-in-php
     * @param float $latitudeFrom Latitude of start point in [deg decimal]
     * @param float $longitudeFrom Longitude of start point in [deg decimal]
     * @param float $latitudeTo Latitude of target point in [deg decimal]
     * @param float $longitudeTo Longitude of target point in [deg decimal]
     * @param float $earthRadius Mean earth radius in [m]
     * @return float Distance between points in [m] (same as earthRadius)
     */
    private static function haversineGreatCircleDistance(
        $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
    {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    /**
     * Calculates the great-circle distance between two points, with the Vincenty formula.
     * @see http://stackoverflow.com/questions/10053358/measuring-the-distance-between-two-coordinates-in-php
     * @param float $latitudeFrom Latitude of start point in [deg decimal]
     * @param float $longitudeFrom Longitude of start point in [deg decimal]
     * @param float $latitudeTo Latitude of target point in [deg decimal]
     * @param float $longitudeTo Longitude of target point in [deg decimal]
     * @param float $earthRadius Mean earth radius in [m]
     * @return float Distance between points in [m] (same as earthRadius)
     */
    public static function vincentyGreatCircleDistance(
        $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
    {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $lonDelta = $lonTo - $lonFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2) +
            pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);
        return $angle * $earthRadius;
    }
}
