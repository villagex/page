<?php

function exifToNumber($value, $format) {
	$spos = strpos($value, '/');
	if ($spos === false) {
		return sprintf($format, $value);
	} else {        
		list($base,$divider) = explode("/", $value);
		
		if ($divider == 0) 
			return sprintf($format, 0);
		else
			return sprintf($format, ($base / $divider));
	}
}

function exifToCoordinate($reference, $coordinate) {
	if ($reference == 'S' || $reference == 'W')
		$prefix = '-';
	else
		$prefix = '';
		
	return $prefix . sprintf('%.6F', exifToNumber($coordinate[0], '%.6F') +
		(((exifToNumber($coordinate[1], '%.6F') * 60) +	
		(exifToNumber($coordinate[2], '%.6F'))) / 3600));
}

function getCoordinates($filename) {
	if (extension_loaded('exif')) {
	    $read_data_compatible_types = array(IMAGETYPE_JPEG, IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM);
		$type_of_image = exif_imagetype($filename);
		if (in_array($type_of_image, $read_data_compatible_types)) {
        	try {
				$exif = exif_read_data($filename, 'EXIF', TRUE);		
			
				if (isset($exif['GPSLatitudeRef']) && isset($exif['GPSLatitude']) && 
					isset($exif['GPSLongitudeRef']) && isset($exif['GPSLongitude'])) {
					return array (
						exifToCoordinate($exif['GPSLatitudeRef'], $exif['GPSLatitude']), 
						exifToCoordinate($exif['GPSLongitudeRef'], $exif['GPSLongitude'])
					);
				}
        	} catch(Exception $e) {}
		}
	}
	return array(0, 0);
}

function getSuggestedRotation($filename) {
	if (extension_loaded('exif')) {
		try {
			$exif = @exif_read_data($filename, 'EXIF', TRUE);
			if (isset($exif['IFD0']) && isset($exif['IFD0']['Orientation'])) {
				$ort = $exif['IFD0']['Orientation'];
				switch($ort) {
					case 3:
						return 180;
					case 6:
						return -90;
					case 8:
						return 90;
				}
			}
		} catch(Exception $e) {}
	}
	return 0;
}

function coordinate2DMS($coordinate, $pos, $neg) {
	$sign = $coordinate >= 0 ? $pos : $neg;
	
	$coordinate = abs($coordinate);
	$degree = intval($coordinate);
	$coordinate = ($coordinate - $degree) * 60;
	$minute = intval($coordinate);
	$second = ($coordinate - $minute) * 60;
	
	return sprintf("%s %d&#xB0; %02d&#x2032; %05.2f&#x2033;", $sign, $degree, $minute, $second);
}

?>