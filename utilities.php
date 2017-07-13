<?php 

require_once('config.php');

$link = getDBConn();
session_start();

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
	$reqVar = $_POST;
} else {
	$reqVar = $_GET;
}

function emailErrorHandler ($errno, $errstr, $errfile, $errline, $errcontext) {
	$context = print_r($errcontext, true);
	$trace = print_r(debug_backtrace(), true);
	//doQuery("INSERT INTO error_log (error_message, error_file, error_lineno, error_name) VALUES ('$errstr', '$errfile', '$errline', '$name')");
	$serverInfo = (isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '').' '.(isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '').' '.(isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '').' '.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').' '.(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '').' '.(isset($_SERVER['USER_AGENT']) ? $_SERVER['USER_AGENT'] : '').' '.(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : ''); 
	if (isset($_SESSION['pcv_user_id'])) {
		$session_user_id = $_SESSION['pcv_user_id'];
	} else {
		$session_user_id = 0;
	}
	//sendMailSend(getAdminEmail(), "Diagnostic Error ($session_user_id): $errstr", "$serverInfo\n\n$errno - $errstr \n\n$errfile - $errline\n\n$context\n\n$trace", getAdminEmail());
	print "<P><font color='red'>The system has suffered a terrible error.  Try reloading the page - that will probably fix it, and if you have a moment, please email the admin and let him know the circumstances that brought this on.</font><BR>$errstr</P>";
	exit();
}
set_error_handler("emailErrorHandler");

function getAdminEmail() {
	return "admin@adventureanywhere.org";
}

function getBaseURL() {
	return BASE_URL;
}

function emailAdmin($subject, $str) {
	//print "$subject<BR>$str";
	sendMailSend("jdepree@gmail.com", "PCV Diagnostic $subject", $str, getAdminEmail());
}

function sendMail($receiver, $subject, $body, $from) {
	global $affAbbr; 

	$body = str_replace("\\r\\n", "\n", $body);
	$body = str_replace("\\n", "\n", $body);
	
	$subject = stripslashes($subject);
	$body = stripslashes($body);
	$caret = strpos($from, '<');
	$fromName = 0;
	if ($caret > 0) {
		$fromName = trim(substr($from, 0, $caret - 1));
	}
	$from =  "From: ".($fromName ? $fromName : $affAbbr)." <".getAdminEmail().">\r\nReply-To: ".$from;
	$to = $receiver;

	if (strlen($to) < 2) {
		return 0;
	}

	sendMailSend($receiver, $subject, $body, $from);
	
	return 1;
}

function getDBConn() {
	$db_link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE );
	
	if (!$db_link) {
		emailAdmin("Could not connect", "Could not connect to database (utilities.getDBConn):".mysqli_error($db_link));
		die('Could not connect: ' . mysqli_error($db_link));
	}
	
	$now = new DateTime();
	$mins = $now->getOffset() / 60;
	$sgn = ($mins < 0 ? -1 : 1);
	$mins = abs($mins);
	$hrs = floor($mins / 60);
	$mins -= $hrs * 60;
	$offset = sprintf('%+d:%02d', $hrs*$sgn, $mins);
	$db_link->query("SET time_zone='$offset';");
	
	return $db_link;
}


function doQuery($queryToBeExecuted) {
	global $_SESSION;
	global $link;
	
	$escaped = $link->real_escape_string($queryToBeExecuted);
	//mysql_query("INSERT INTO log (log_record) VALUES ('$escaped')");
	print mysqli_error($link);
	if (!($result = $link->query($queryToBeExecuted))) {
		$email = '';
		$trace = print_r(debug_backtrace(), true);
		$name = 'Unlogged User';
		if (isset($_SESSION['session_first_name'])) {
			$name = $_SESSION['session_first_name'].' '.$_SESSION['session_last_name'];
		}
		
		emailAdmin("Exception", "Exception caused by: $name\n\n".mysqli_error($link)."\n\n".$queryToBeExecuted."\n\n".$trace);
		print "<FONT color='red'>Something has gone terribly wrong.  The administrator has been notified.  Please do not panic - you will be emailed as soon as the issue is resolved. ";
		//if (isset($_SESSION['session_admin'])) {
			print "<P>details: ".mysqli_error($link)." <BR>QUERY: $queryToBeExecuted</FONT><P>$trace</P>";
		//}
		die();
	}
	
	return $result;
}

function doQueryAndReport($subject, $query) {
	$result = doQuery($query);
	emailAdmin($subject, $query);
	return $result;
}

function doJsonQuery($query) {
	$result = doQuery($query);
	$rows = mysqli_fetch_all($result,MYSQLI_ASSOC);
	mysqli_free_result($result);
	closeDBConn();
	$json = json_encode($rows);
	return $json;
}

function closeDBConn() {
	global $link;
	mysqli_close($link);
}
// End DB functions

// Request processing
function getGet($key) {
	return getFromReq($key, $_GET);
}

function getPost($key) {
	return getFromReq($key, $_POST);
}

function getFromReq($key, $req) {
	global $link;
	return mysqli_real_escape_string($link, stripslashes($req[$key]));
}

function param($key) {
	global $reqVar;
	return getFromReq($key, $reqVar);
}

function hasParam($key) {
	global $reqVar;
	return isset($reqVar[$key]);
}

function checked($key) {
	global $reqVar;
	return (isset($reqVar[$key]) ? 1 : 0);
}
// End request processing


function getDateRangeString($startTime, $endTime, $includeSpans=false) {
		$startMonth = date("F", $startTime);
		$shortStartMonth = date("M", $startTime);
		$startMonthNum = date("n", $startTime);
		$endMonth = date("F", $endTime);
		$shortEndMonth = date("M", $endTime);
		$endMonthNum = date("n", $endTime);
		$startDay = date("j", $startTime);
		$endDay = date("j", $endTime);
		$startYear = date("Y", $startTime);
		
		if ($includeSpans) {
			$yearStr = date("Y", $startTime);
			$endYear = date("Y", $endTime);
			
			$startMonthTime = mktime(0, 0, 0, $startMonthNum, 0, $yearStr);	
			$endMonthTime = mktime(0, 0, 0, $endMonthNum, 0, $endYear);
			$startDayTime = mktime(0, 0, 0, $startMonthNum, $startDay, $yearStr);
			$endDayTime = mktime(0, 0, 0, $endMonthNum, $endDay, $yearStr);
			$startYearTime = mktime(0, 0, 0, 0, 0, $yearStr);
			$endYearTime = mktime(0, 0, 0, 0, 0, $endYear);
			
			if ($endDay == $startDay && $startMonth == $endMonth) {
				$dateStr = "<span class='month' id='$startMonthTime'>$startMonth</span> <span class='day' id='$startTime~$endTime'>$startDay</span>, <span class='year' id='$startYearTime'>$yearStr</span>";
			} else {
				if ($startMonth == $endMonth && $yearStr == $endYear) {
					$dateStr = "<span class='month' id='$startMonthTime'>$startMonth</span> <span class='day' id='$startTime~$endTime'>$startDay - $endDay</span>, <span class='year' id='$endYearTime'>$endYear</span>";
				} else {
					$dateStr = "<span class='month' id='$startMonthTime'>$shortStartMonth</span> <span class='day' id='$startTime~$endTime'>$startDay</span> - <span class='month' id='$endMonthTime'>$shortEndMonth</span> <span class='day' id='$startTime~$endTime'>$endDay</span>, <span class='year' id='$endYearTime'>$endYear</span>";
				}
			}
		} else {
			if ($endDay == $startDay && $startMonth == $endMonth) {
				$dateStr = date("F j, Y", $startTime);
			} else {
				$endYear = date("Y", $endTime);
				if ($startMonth == $endMonth && $startYear == $endYear) {
					$dateStr = $startMonth." ".$startDay." - ".$endDay.", ".$endYear;
				} elseif ($startYear == $endYear) {
					$dateStr = $shortStartMonth." ".$startDay." - ".$shortEndMonth." ".$endDay.", ".$endYear;
				} else {
					$dateStr = $shortStartMonth." ".$startYear." - ".$shortEndMonth." ".$endYear;
				}
			}
		}
		return $dateStr;
}

function getTimeRangeString($startTime, $endTime) {
	if ($startTime == $endTime) {
		return getTimeString($startTime);
	}
	$startDayTime = mktime(0, 0, 0, date("m", $startTime), date("d", $startTime), date("Y", $startTime));
	$endDayTime = mktime(0, 0, 0, date("m", $endTime), date("d", $endTime), date("Y", $endTime));
	if ($endDayTime != $startDayTime) {
		return getDateRangeString($startTime, $endTime);
	} else {
		$targetTime = mktime(0, 0, 0, date("m", $startTime), date("d", $startTime), date("Y", $startTime));
		$nowTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		$tomorrow = mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"));
		$nextWeek = mktime(0, 0, 0, date("m"), date("d") + 7, date("Y"));
		
		$day = '';
		if ($nowTime == $targetTime) {
			$day = "Today ";
		} elseif ($tomorrow == $targetTime) {
			$day = "Tomorrow ";
		} elseif ($targetTime < $nextWeek) {
			$day = date('D ', $startTime);
		} else {
			$day = date('M j ', $startTime);
		}
		return $day.date('g:ia', $startTime).' - '.date('g:ia', $endTime);
	}
}

function getTimeString($time) {
	$targetTime = mktime(0, 0, 0, date("m", $time), date("d", $time), date("Y", $time));
	$nowTime = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
	$tomorrow = mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"));
	$nextWeek = mktime(0, 0, 0, date("m"), date("d") + 7, date("Y"));
	$lastWeek = mktime(0, 0, 0, date("m"), date("d") - 7, date("Y"));
	
	if ($nowTime == $targetTime) {
		return "Today ".date('g:ia', $time);
	} elseif ($tomorrow == $targetTime) {
		return "Tomorrow ".date('g:ia', $time);
	} elseif ($targetTime < $nextWeek && $targetTime > $lastWeek) {
		return date('D g:ia', $time);
	} else {
		return date('M j g:ia', $time);
	}
}

?>
