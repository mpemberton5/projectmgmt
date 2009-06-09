<?php
/* $Id: time.php,v 1.4 2009/05/07 21:36:20 markp Exp $ */

//security check
//if (! defined('UID')) {
//  die('Direct file access not permitted');
//}

//this is the regex for input validation filter used in common.php
$validation_regex = "/([^\x09\x0a\x0d\x20-\x7e\xa0-\xff])/s"; //ISO-8859-x

//dates
$month_array = array (NULL, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' );
$week_array = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' );


/******************************************************************************/
// NUMERIC FUNCTIONS
/******************************************************************************/
/**
 * @param $number
 * @return unknown_type
 */
function str2no($number){
	$number = str_replace(",", "", $number);
	return $number;
}

/**
 * @param $number
 * @return unknown_type
 */
function no2str($number){
	$number = number_format($number,2, '.', ',');
	return $number;
}

/******************************************************************************/
// Create a pgsql/mysql datetime stamp
/******************************************************************************/
/**
 * @param $day
 * @param $month
 * @param $year
 * @return unknown_type
 */
function date_to_datetime($day, $month, $year) {
	global $month_array;

	//check for valid calendar date
	if (!checkdate($month, $day, $year)) {
		warning('Invalid Date', sprintf('The date of %s is not a valid calendar date (Check the number of days in month).<br />Please go back and re-enter a valid date.', $year.'-'.$month_array[$month ].'-'.$day));
	}

	//format is 2004-08-02 00:00:00
	return sprintf('%04d-%02d-%02d 00:00:00', $year, $month, $day);
}

/******************************************************************************/
// Take a database datestamp and make it look nice
/******************************************************************************/
/**
 * @param $timestamp
 * @return unknown_type
 */
function nicedate($timestamp) {
	global $month_array;

	if (empty($timestamp))
	return '';

	$date_array = explode('-', substr($timestamp, 0, 10));

	//format is 2004-Aug-02
	return ($date_array[1]).'-'.$date_array[2].'-'.$date_array[0];
}

/******************************************************************************/
/******************************************************************************/
/**
 * @param $std_date
 * @param $fmt (1 = MM-DD-YYYY,2 = YYYY-MM-DD)
 * @return unknown_type
 */
function database_date($std_date,$fmt=1) {

	// $std_date - a date MDY in some type of format
	// $fmt = 1 = MM-DD-YYYY
	//        2 = YYYY-MM-DD
	if (empty($std_date))
	return "";

	$date_array = explode('-', substr($std_date, 0, 10));
	if ($fmt == 1) {
		return sprintf('%04d-%02d-%02d 00:00:00', (int)$date_array[2], (int)$date_array[0], (int)$date_array[1]);
	} else if ($fmt == 2) {
		return sprintf('%04d-%02d-%02d 00:00:00', (int)$date_array[0], (int)$date_array[1], (int)$date_array[2]);
	}
	return "0";
}

/******************************************************************************/
// Take a database timestamp and make it look nice
/******************************************************************************/
/**
 * @param $timestamp
 * @return unknown_type
 */
function nicetime($timestamp) {
	global $month_array;

	if (empty($timestamp))
	return '';

	$date_array = explode('-', substr($timestamp, 0, 10));

	$time = substr($timestamp, 11, 5);

	//format is 2004-Aug-02 18:06  +1200
	return sprintf('%s-%s-%02d %s &nbsp;&nbsp;%+03d00', $date_array[0], $month_array[(int)($date_array[1])], (int)$date_array[2], $time, TZ);
}

/******************************************************************************/
//generate a HTML drop down box for date from a pg/my timestamp
/******************************************************************************/
/**
 * @param $timestamp
 * @return unknown_type
 */
function date_select_from_timestamp($timestamp='') {

	if ($timestamp == '')
	return date_select(-1, -1, -1);

	//deparse the line
	$date_array = explode('-', substr($timestamp, 0, 10));

	//show line
	return date_select($date_array[2], $date_array[1], $date_array[0]);
}

/******************************************************************************/
// date function to add specific amount of time to date (sec,min,hour,day,year)
/******************************************************************************/
// $newdate = dateadd("d",3,"2006-12-12");    #  add 3 days to date
// $newdate = dateadd("s",3,"2006-12-12");    #  add 3 seconds to date
// $newdate = dateadd("m",3,"2006-12-12");    #  add 3 minutes to date
// $newdate = dateadd("h",3,"2006-12-12");    #  add 3 hours to date
// $newdate = dateadd("ww",3,"2006-12-12");   #  add 3 weeks days to date
// $newdate = dateadd("m",3,"2006-12-12");    #  add 3 months to date
// $newdate = dateadd("yyyy",3,"2006-12-12"); #  add 3 years to date
// $newdate = dateadd("d",-3,"2006-12-12");   #  subtract 3 days from date
/******************************************************************************/
/**
 * @param $interval
 * @param $number
 * @param $dateTime
 * @return unknown_type
 */
function dateAdd($interval,$number,$dateTime) {

	$dateTime = (strtotime($dateTime) != -1) ? strtotime($dateTime) : $dateTime;
	$dateTimeArr = getdate($dateTime);

	$yr  = $dateTimeArr['year'];
	$mon = $dateTimeArr['mon'];
	$day = $dateTimeArr['mday'];
	$hr  = $dateTimeArr['hours'];
	$min = $dateTimeArr['minutes'];
	$sec = $dateTimeArr['seconds'];

	switch($interval) {
		case "s"://seconds
			$sec += $number;
			break;

		case "n"://minutes
			$min += $number;
			break;

		case "h"://hours
			$hr += $number;
			break;

		case "d"://days
			$day += $number;
			break;

		case "ww"://Week
			$day += ($number * 7);
			break;

		case "m": //similar result "m" dateDiff Microsoft
			$mon += $number;
			break;

		case "yyyy": //similar result "yyyy" dateDiff Microsoft
			$yr += $number;
			break;

		default:
			$day += $number;
	}

	$dateTime = mktime($hr,$min,$sec,$mon,$day,$yr);
	$dateTimeArr=getdate($dateTime);

	$nosecmin = 0;
	$min=$dateTimeArr['minutes'];
	$sec=$dateTimeArr['seconds'];

	if ($hr==0) {
		$nosecmin += 1;
	}
	if ($min==0) {
		$nosecmin += 1;
	}
	if ($sec==0) {
		$nosecmin += 1;
	}

	if ($nosecmin>2) {
		return(date("Y-m-d",$dateTime));
	} else {
		return(date("Y-m-d G:i:s",$dateTime));
	}
}

?>