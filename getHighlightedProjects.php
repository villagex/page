<?php
require_once("utilities.php");

$file = fopen("cached/highlighted_projects.json","w");
$count = 0;
$result = doQuery("SELECT project_id, project_name, picture_filename, project_summary, village_name, project_funded, project_budget FROM projects JOIN villages ON project_village_id=village_id JOIN pictures ON project_image_id=picture_id WHERE project_status = 'funding' ORDER BY project_posted DESC LIMIT 3");

while ($row = $result->fetch_assoc()) {
	fwrite($file, ($count > 0 ? "," : '{ "type": "FeatureCollection", "features": [').
			'{
		"id": "'.$row['project_id'].'",
		"name": "'.$row['project_name'].'",
		"villageName": "'.$row['village_name'].'",
		"picture_filename": "'.$row['picture_filename'].'",
		"project_budget": "'.$row['project_budget'].'",
		"project_funded": "'.$row['project_funded'].'",
		"project_summary": '.json_encode($row['project_summary']).'
	}');
	$count++;
}
fwrite($file, "]}");
fclose($file);
?>