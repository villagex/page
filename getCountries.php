<?php
require_once("utilities.php");

$file = fopen("cached/countries.json","w");
$result = doQuery("SELECT country_id, country_label, country_code, country_latitude, country_longitude, MIN(village_lat) AS country_bounds_sw_lat, MIN(village_lng) AS country_bounds_sw_lng, MAX(village_lat) AS country_bounds_ne_lat, MAX(village_lng) AS country_bounds_ne_lng, picture_filename, COUNT(DISTINCT village_id) AS villageCount, COUNT(DISTINCT project_id) AS projectCount, SUM(IF(project_status='funding', 1, 0)) AS fundingCount FROM countries JOIN villages ON village_pending=0 AND country_id=village_country LEFT JOIN projects ON village_id=project_village_id LEFT JOIN pictures ON village_thumbnail=picture_id WHERE country_bounds_sw_lat <> 0 GROUP BY country_id");
$count = 0;
while ($row = $result->fetch_assoc()) {
	$swLat = $row['country_bounds_sw_lat'];
	$swLng = $row['country_bounds_sw_lng'];
	$neLat = $row['country_bounds_ne_lat'];
	$neLng = $row['country_bounds_ne_lng'];
	if ($neLat - $swLat < .2) {
		$neLat = $neLat + .2;
		$swLat = $swLat - .2;
	}
	if ($neLng - $swLng < .2) {
		$neLng = $neLng + .2;
		$swLng = $swLng - .2;
	}
	fwrite($file, ($count > 0 ? "," : '{ "type": "FeatureCollection", "features": [').
	'{
		"type": "Feature",
		"geometry": {
			"type": "Point",
			"coordinates": ['.$row['country_longitude'].', '.$row['country_latitude'].']
		},
		"properties": {
			"id": "'.$row['country_id'].'",
			"name": "'.$row['country_label'].'",
			"icon": "'.$row['country_code'].'",
			"picture_filename": "'.$row['picture_filename'].'",
			"projectCount": "'.$row['projectCount'].'",
			"villageCount": "'.$row['villageCount'].'",
			"fundingCount": "'.$row['fundingCount'].'",
			"swLat": "'.$swLat.'",
			"swLng": "'.$swLng.'",
		 	"neLat": "'.$neLat.'",
			"neLng": "'.$neLng.'"
		}
	}');
	$count++;
}
fwrite($file, "]}");
fclose($file);
?>