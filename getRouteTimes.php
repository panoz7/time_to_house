<?php

include ("include/login.php");
require_once 'HTTP/Request2.php';

date_default_timezone_set('America/New_York');


// Get all routes for the current time
$query = "SELECT route_id, dest.address dest, origin.address origin  FROM route
INNER JOIN locations dest ON route.dest_id = dest.location_id
INNER JOIN locations origin ON route.origin_id = origin.location_id
WHERE TIME_FORMAT(time_start, '%H:%i') <= TIME_FORMAT(CONVERT_TZ(now(),'+00:00','-05:00'), '%H:%i')
AND TIME_FORMAT(time_end, '%H:%i') >= TIME_FORMAT(CONVERT_TZ(now(),'+00:00','-05:00'), '%H:%i')";

$result = $mysqli->query($query) OR DIE($mysqli->error);

// Iterate through the routes
while ($row = $result->fetch_assoc()) {
    $routeTime = getRouteTime($row['origin'], $row['dest']);
    $uploadSuccess = saveRouteTime($row['route_id'], $routeTime);

    echo $date = date('m/d/Y h:i:s a', time()).", ".$row['origin'].", ".$row['dest'].", ".$routeTime.", ".$uploadSuccess."\n";
}


function saveRouteTime($route_id, $routeTime) {
    global $mysqli;
    $query = "INSERT INTO results (route_id, result_time) VALUES ('$route_id','$routeTime')"; 
    return $mysqli->query($query) OR DIE($mysqli->error);
}

function getRouteTime($origin, $dest) {

    global $mapsApiKey;
    $url = "https://maps.googleapis.com/maps/api/directions/json?origin={$origin}&destination={$dest}&key={$mapsApiKey}&departure_time=now";

    $request = new Http_Request2($url);
    $request->setMethod(HTTP_Request2::METHOD_GET);
    
    try
    {
        $response = $request->send();
        $json = json_decode($response->getBody(),true);
        return $json['routes'][0]['legs'][0]['duration_in_traffic']['value'];

    }
    catch (HttpException $ex)
    {
        return 0;
    }

}










?>