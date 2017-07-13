<?php
require_once("utilities.php");
$destination_path = "uploads/";

$allowedExtensions = array("jpeg", "jpg", "png", "gif");
$sizeLimit = 20 * 1024 * 1024;
$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
if (!$uploader->isFailed()) {
	$result = $uploader->handleUpload($destination_path);
	print json_encode($result);
} else {
	print "UPLOADER FAILED";
}

/**
 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr {
	/**
	 * Save the file to the specified path
	 * @return boolean TRUE on success
	 */
	function save($path) {
		$input = fopen("php://input", "r");
		$temp = tmpfile();
		$realSize = stream_copy_to_stream($input, $temp);
		fclose($input);
		
		if ($realSize != $this->getSize()){
			return false;
		}
		
		$target = fopen($path, "w");
		fseek($temp, 0, SEEK_SET);
		stream_copy_to_stream($temp, $target);
		fclose($target);
		
		return true;
	}
	function getName() {
		return param('qqfile');
	}
	function getSize() {
		if (isset($_SERVER["CONTENT_LENGTH"])){
			return (int)$_SERVER["CONTENT_LENGTH"];
		} else {
			throw new Exception('Getting content length is not supported.');
		}
	}
}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm {
	/**
	 * Save the file to the specified path
	 * @return boolean TRUE on success
	 */
	function save($path) {
		if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)){
			return false;
		}
		return true;
	}
	function getName() {
		return $_FILES['qqfile']['name'];
	}
	function getSize() {
		return $_FILES['qqfile']['size'];
	}
}

class qqFileUploader {
	private $allowedExtensions = array();
	private $sizeLimit = 10485760;
	private $file;
	
	function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760){
		$allowedExtensions = array_map("strtolower", $allowedExtensions);
		
		$this->allowedExtensions = $allowedExtensions;
		$this->sizeLimit = $sizeLimit;
		
		//$this->checkServerSettings();
		
		if (isset($_GET['qqfile'])) {
			$this->file = new qqUploadedFileXhr();
		} elseif (isset($_FILES['qqfile'])) {
			$this->file = new qqUploadedFileForm();
		} else {
			$this->file = false;
		}
	}
	
	function isFailed() {
		return $this->file == false;
	}
	
	private function checkServerSettings(){
		$postSize = $this->toBytes(ini_get('post_max_size'));
		$uploadSize = $this->toBytes(ini_get('upload_max_filesize'));
		
		if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
			$size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
			die("{'error':'increase post_max_size and upload_max_filesize to $size'}");
		}
	}
	
	private function toBytes($str){
		$val = trim($str);
		$last = strtolower($str[strlen($str)-1]);
		switch($last) {
			case 'g': $val *= 1024;
			case 'm': $val *= 1024;
			case 'k': $val *= 1024;
		}
		return $val;
	}
	
	/**
	 * Returns array('success'=>true) or array('error'=>'error message')
	 */
	function handleUpload($uploadDirectory){
		global $link;
		
		if (!is_writable($uploadDirectory)){
			return array('error' => "Server error. Upload directory isn't writable.");
		}
		
		if (!$this->file){
			return array('error' => 'No files were uploaded.');
		}
		
		$size = $this->file->getSize();
		
		if ($size == 0) {
			return array('error' => 'File is empty');
		}
		
		if ($size > $this->sizeLimit) {
			return array('error' => 'File is too large');
		}
		
		$original = $this->file->getName();
		$pathinfo = pathinfo($this->file->getName());
		$filename = $pathinfo['filename'];
		
		//$filename = md5(uniqid());
		if (isset($pathinfo['extension'])) {
			$ext = $pathinfo['extension'];
		} else {
			return array('error' => 'File has an invalid extension.');
		}
		if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
			$these = implode(', ', $this->allowedExtensions);
			return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
		}
		
		$index = strrpos($original, '.');
		if ($index) {
			$extension = substr($original, $index + 1);
		} else {
			$extension = 'jpg';
		}
		$tempFileName = 'UP_'.substr($original, 0, $index) . time() . '.' . $extension;
		$smallFileName = 'UP_'.substr($original, 0, $index) . time() . 's' . '.' . $extension;
		$target_path = $uploadDirectory . basename($tempFileName);
		$small_path = $uploadDirectory . basename($smallFileName);
		
		if ($this->file->save($target_path)) {
			include('utilities/ImageResizer.php');
			$image = new ImageResizer();
			//include('utilities/exif.php');
			//$coords = getCoordinates($target_path);
			//$rotation = getSuggestedRotation($target_path);
			
			$image->load($target_path);
			
			$imageHeight = $image->getHeight();
			$imageWidth = $image->getWidth();
			if ($imageWidth < $imageHeight && $imageWidth > 800) {
				$image->resizeToWidth(800);
			} elseif ($imageHeight > 800) {
				$image->resizeToHeight(800);
			}
			if ($imageHeight > 800 && $imageWidth > 800) {
				$image->crop(800, 800);
			} elseif ($imageHeight > 800) {
				$image->crop($imageWidth, 800);
			} elseif ($imageWidth > 800) {
				$image->crop(800, $imageHeight);
			}
			 
			/*if ($rotation != 0) {
				$image->rotate($rotation, -1);
			}*/
			
			$imageWidth = $image->getWidth();
			$imageHeight = $image->getHeight();
			$image->save($target_path);
			
			$image->load($target_path);
			$image->resizeToHeight(400);
			$image->save($small_path);
			
			if ($extension === 'png') {
				$image->load($target_path, IMAGETYPE_PNG);
			} elseif ($extension === 'gif') {
				$image->load($target_path, IMAGETYPE_GIF);
			} else {
				$image->load($target_path);
			}

			if ($extension === 'png') {
				$image->save($target_path, IMAGETYPE_PNG);
			} elseif ($extension === 'gif') {
				$image->save($target_path, IMAGETYPE_GIF);
			} else {
				$image->save($target_path);
			}
			
			$queryStr = "INSERT INTO pictures (picture_filename) VALUES ('$tempFileName')";
			doQuery($queryStr);
			$newId = $link->insert_id;
			return array('success'=>true, 'pictureId'=>$newId, 'newPath'=>$small_path, 'newBigPath'=>$target_path, 'newFilename'=>$smallFileName, 'newBigFilename'=>$tempFileName);
		}
		return array('error'=> 'Could not save uploaded file.' .
				'The upload was cancelled, or server error encountered');
	}
}
?>