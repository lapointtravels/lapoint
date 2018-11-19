<?php

ini_set("allow_url_fopen", true);

header('Access-Control-Allow-Origin: *');

$destination_type = '%25_';
$dest = '%25_';
$camp = '%25_';
$level = '%25';

if (isset($_GET["destination_type"]) && $_GET["destination_type"]) {
	$destination_type = $_GET["destination_type"] ."_";
}

if (isset($_GET["destination"]) && $_GET["destination"]) {
	$dest = $_GET["destination"] ."_";
}

if (isset($_GET["camp"]) && $_GET["camp"]) {
	$camp = $_GET["camp"] ."_";
}

if (isset($_GET["level"]) && $_GET["level"]) {
	$level = $_GET["level"];
}

// If the destination and camp code is identical remove one since that's the way the id is set up atm.
/*if ($dest == $camp) {
	$camp = '';
}*/

// SPECIAL CASE: If the destination type is YOUTH skip the camp code..
if (strcmp($destination_type, "YOUTH_") === 0 || strcmp($destination_type, "YC_") === 0) {
	$product = $destination_type . $dest . $level;
} else {
	$product = $destination_type . $dest . $camp . $level;
}

/////////////////////////////////////////////////////////////////////////////
// Set that timezone - going with Lisbon as lapoint headquarters is in portugal
date_default_timezone_set('Europe/Lisbon');

$lang = (isset($_GET['lang']) && $_GET["lang"]) ? $_GET["lang"] : "SE";
$max_count = (isset($_GET['maxnumberfortourlist1'])) ? $_GET['maxnumberfortourlist1'] : 8;
$start_date = (isset($_GET['startDate']) && $_GET['startDate']) ? $_GET['startDate'] : date("Y-m-d");

$data = array(
	"product" => $product,
	"lang" => $lang,
	"startDate" => $start_date,
	"siteLanguageVersion" => $lang,
	"maxnumberfortourlist1" => $max_count, // * 2, // so we can remove any GROUPS from the result and most likely not run out of results to show
	"ifFullShowOtherDates" => "yes"
);

if (isset($_GET['duration']) && $_GET['duration']) {
    $data["duration"] = $_GET['duration'];
}

$vars = implode('&', array_map(function ($v, $k) {
	return $k . '=' . $v;
}, $data, array_keys($data)));



// http://booking.lapoint.se/clientfiles/tourlist1.asp?product=SC_%_ST&lang=SE&date=2016-03-17&maxnumberfortourlist1=10&siteLanguageVersion=SE&startDate=2016-03-17

// echo "http://booking.lapoint.se/clientfiles/tourlist1.asp?" . $vars;


echo "<!-- https://lapoint.travelize.se/clientfiles/tourlist1.asp?" . $vars . "-->";
//$data = file_get_contents("http://booking.lapoint.se/clientfiles/tourlist1.asp?" . $vars);
$data = file_get_contents("https://lapoint.travelize.se/clientfiles/tourlist1.asp?" . $vars);

/*if($_GET['product'] == "SC_NO_STADT_B") {
	if($_GET['lang'] == "SE") {
		$data = "Basic kan bokas via Surfcamp Stadt/basic: <a href='http://www.lapoint.se/package/surfcamp-norge-basic/'>Surfcamp Stadt/basic</a><br /><br />";
	}
	if($_GET['lang'] == "DK") {
		$data = "Basic kan reserveres p&aring; f&oslash;lgende side: Surf Camp Norge / Basic: <a href='http://www.lapoint.dk/package/norge-stadt-basic/'>Surfcamp Stadt/basic</a><br /><br />";
	}
	if($_GET['lang'] == "NO") {
		$data = "Basic kan bookes p&aring; f&oslash;lgende side: Surfecamp Norge/Basic: <a href='http://www.lapoint.no/package/surfcamp-stad-basic/'>Surfcamp Stadt/basic</a><br /><br />";
	}

	if($_GET['lang'] == "UK") {
		$data = "Basic can be booked at following page: Surf camp Norway/Basic: <a href='http://www.lapointcamps.com/package/surf-camp-norway-basic/'>Surfcamp Stadt/basic</a><br /><br />";
	}
}*/

// remove any GROUPS from the result
//var_dump( $data );

echo utf8_encode($data);
//echo $data;

echo "<!-- " . $product . "-->";
exit;

