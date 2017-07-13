<?php
require_once("utilities.php");

$swLat=param('swLat');
$swLng=param('swLng');
$neLat=param('neLat');
$neLng=param('neLng');

print doJsonQuery("SELECT project_id, project_village_id, project_name, project_lat, project_lng, project_budget, project_funded, picture_filename, project_type, project_summary, village_name, GROUP_CONCAT(CONCAT(pu_image_id, ':', DATE_FORMAT(pu_timestamp, '%b %D, %Y'), ' ', IFNULL(pu_description,' ')) SEPARATOR '~') AS updatePictures FROM projects JOIN villages ON project_village_id=village_id
		JOIN pictures ON picture_id=project_image_id LEFT JOIN (SELECT pu_project_id, pu_image_id, pu_timestamp, pu_description FROM project_updates WHERE pu_image_id IN (SELECT picture_id FROM pictures) ORDER BY pu_timestamp) AS images ON pu_project_id=project_id WHERE project_lat>$swLat AND project_lat<$neLat AND project_lng>$swLng AND project_lng<$neLng GROUP BY project_id ORDER BY project_funded");

?>