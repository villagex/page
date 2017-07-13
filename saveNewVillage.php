<?php
require_once("utilities.php");
$villageLat = param('villageLat');
$villageLng = param('villageLng');
$villageName = param('villageName');
$villageConnection = param('villageConnection');
$villageImageId = param('villageImageId');
$villageCountry = param('villageCountry');
$villageRegion = param('villageRegion');
$email = param('email');
$fullName = param('fullName');

doQuery("INSERT INTO village_submissions (vs_contact_name, vs_contact_email, vs_village_name, vs_village_country, vs_connection, vs_region, vs_image_id) VALUES ('$fullName', '$email', '$villageName', '$villageCountry', '$villageConnection', '$villageRegion', $villageImageId)");
$filename ='';
$result = doQuery("SELECT picture_filename FROM pictures WHERE picture_id=$villageImageId");
if ($row = $result->fetch_assoc()) {
	$filename = "http://{$_SERVER['SERVER_NAME']}/uploads/".$row['picture_filename'];
}
$body = "Village Name: $villageName\nLatlng: $villageLat,$villageLng\nFull Name: $fullName\nEmail: $email\nCountry: $villageCountry\nRegion: $villageRegion\nPicture: $filename\nConnection: $villageConnection";

sendMailSend("jdepree@gmail.com", "New Village Added", $body, "Village X", "admin@adventureanywhere.org");
$countryId = $regionId = 0;
$result = doQuery("SELECT country_id FROM countries WHERE country_code='$villageCountry'");
if ($row = $result->fetch_assoc()) {
	$countryId = $row['country_id'];
}
$result = doQuery("SELECT region_id FROM regions WHERE region_name='$villageRegion'");
if ($row = $result->fetch_assoc()) {
	$regionId = $row['region_id'];
} else {
	doQuery("INSERT INTO regions (region_name) VALUES ('$villageRegion')");
	$regionId = $link->insert_id;
}
doQuery("INSERT INTO villages (village_name, village_region, village_country, village_lat, village_lng, village_thumbnail) 
		VALUES ('$villageName', $regionId, $countryId, $villageLat, $villageLng, $villageImageId)");
?>