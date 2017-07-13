<?php
require_once("utilities.php");

$swLat=param('swLat');
$swLng=param('swLng');
$neLat=param('neLat');
$neLng=param('neLng');

print doJsonQuery("SELECT village_id, village_name, village_lat, village_lng FROM villages
		WHERE village_lat>$swLat AND village_lat<$neLat AND village_lng>$swLng AND village_lng<$neLng");

?>