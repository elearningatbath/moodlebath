<html>
	<head>
		<title>Dead Feeds</title>
		<style type="text/css">
		body{font-family:Tahoma}
			table { width:80%;border-left:1px solid #A52A2A;margin-bottom:10px;border-right:1px solid #A52A2A;font-size: 12px;}
			table thead {background-color:#A52A2A;color:white;text-align:left;}
			tr{border-right:1px solid #A52A2A}
			td{border-bottom:1px solid #A52A2A;padding:5px;}
		</style>
	</head>
<body>
	<div id="summary_box">
		
		
	</div>
<?php
require_once ( '../../config.php');
global $CFG, $DB;
require_once($CFG->libdir.'/simplepie/moodle_simplepie.php');
// Get the rss_feeds
$rs = $DB->get_recordset('block_rss_client');

/*foreach($rs as $record)
{
	//For each feed return me the courses the user is enrolled on.
	$userid = $record->userid; //These are distinct usernames
	$arrEnrolments[] = get_enrolments($userid);
}
$sql_enrol = "SELECT courseid FROM {enrol} WHERE  id = ?";
foreach($arrEnrolments as $arrEnrol)
{
	foreach($arrEnrol as $userid => $enrol)
	{
		foreach($enrol as $key=>$enrolid)
		{
			$result = $DB->get_records_sql($sql_enrol,array($enrolid));
			//print_r($result);
		}
	}
}*/

foreach($rs as $record)
{
	 
	 $feed =  new moodle_simplepie();
            // set timeout for longer than normal to be agressive at
            // fetching feeds if possible..
            $feed->set_timeout(40);
            $feed->set_cache_duration(0);
            $feed->set_feed_url($record->url);
            $feed->init();

            if ($feed->error()) {
				$errors[] = $record->id;
                $status = false;
            } else {
               // mtrace ('ok');
            }
}

//print_r($errors);
$rss_block_instances =   $DB->get_records('block_instances',array('blockname'=>'rss_client'));
//print_r($rss_block_instances);
$arrObj = array();
$arrCSV = array();
foreach($rss_block_instances as $key => $block_instance)
{
	$parentcontextid = $block_instance->parentcontextid;
	$context = $DB->get_records('context',array('id'=>$parentcontextid));
	$level = get_contextlevel($context[$parentcontextid]->contextlevel);
	$config = unserialize(base64_decode($block_instance->configdata));
	
	foreach($config->rssid as $rssid)
	{
		if(in_array($rssid, $errors))
		{
			//echo "I found RSSID: $rssid ...";
			$userid = "";
			$courseid = "";
			$objRss = $DB->get_record('block_rss_client', array('id'=> $rssid));
			$ObjbadRSS = new stdClass();
			if($level == 'COURSE')
			{
				//echo "\nlevel is course \n";
				$courseid = $context[$parentcontextid]->instanceid;
				$course = $DB->get_record('course',array('id'=>$courseid));
				$ObjbadRSS->course = $course;
				$ObjbadRSS->rss = $objRss;
				$arrObj[] = $ObjbadRSS;
			}
			if($level == 'USER')
			{
				$userid = $context[$parentcontextid]->instanceid;
				$objUser = $DB->get_record('user',array('id'=>$userid));
				$ObjbadRSS->user = $objUser;
				$ObjbadRSS->rss = $objRss;
				$arrObj[] = $ObjbadRSS;
			}
		}
	}
}
$html = "";
foreach($arrObj as $object)
{
	//print_r($object);
	$html.= "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" >";
	//RSS Details
	$rss_url = $object->rss->url;
	$rss_title = $object->rss->title;
	$arrCSV[] = $rss_url;
	$arrCSV[] = $rss_title;
	if(key($object) == 'course')
	{
		//var_dump($object->course);
		//Course header
		$html.= "<thead><tr><th>Course Details</th><th>RSS feed</th></tr></thead>";
		//Course Details
		$fullname = $object->course->fullname;
		$arrCSV[] = $fullname;
		$id = $object->course->id;
		$arrCSV[] = $id;
		$idnumber =$object->course->idnumber;
		$arrCSV[] = $idnumber;
		$html.= "<tr><td><strong>Course Name:</strong> ".$fullname."</td><td><strong>RSS Title:</strong> $rss_title</td></tr><tr><td><strong>Moodle Course ID:</strong> $id </td><td><strong>URL:</strong> <a href=\"$rss_url\">$rss_url</a> </td></tr><tr><td rowspan=\"1\"> <strong>ID Number:</strong> $idnumber</td>";
	}
	if(key($object) == 'user')
	{
		//User header
		$html.= "<thead><tr><th>User Details</th><th>RSS feed</th></tr></thead>";
		$fullname = $object->user->firstname." ".$object->user->lastname;
		$id = $object->user->id;
		$email = $object->user->email;
		$html.= "<tr><td><strong>Full Name:</strong> ".$fullname."</td><td><strong>RSS Title:</strong> $rss_title</td></tr><tr><td><strong>Moodle User ID:</strong> $id </td><td><strong>URL:</strong> <a href=\"$rss_url\">$rss_url</a> </td></tr><tr><td rowspan=\"1\"><strong> Email:</strong> $email </td>";
		//User details
	}
	$html.= "</table>";
}
//echo ($html) ;
function get_enrolments($userid)
{
	global $CFG, $DB;
	$sql = "SELECT e.id AS 'enrolid' FROM {user_enrolments} ue JOIN 
			{enrol} e ON e.id = ue.enrolid WHERE userid = ?;
	";
	$result = $DB->get_records_sql($sql,array($userid));
	foreach($result as $key=>$objEnrol)
	{
		$enrolids[$userid][] = $objEnrol->enrolid;
	}
	return $enrolids;
}
function get_contextlevel($context)
{
	$level = "";
	switch($context)
	{
		case '50':
			$level= 'COURSE';
			break;
		case '80':
			$level = 'BLOCK';
			break;
		case '30':
		$level = 'USER';
		break;
	}
	return $level;
}
function createCSV($content)
{
	$filename = "badRSS.csv";
	$fp = fopen('php://output','w') or die("Cant create / open the file!");
	//First write the headers
	$headers = array("Course Name","Course ID","ID Number","Full Name","User ID","Email","RSS Title","RSS URL");
	fputcsv($fp, $headers);
	
	foreach($content as $object)
	{
		$coursename = $courseid = $idnumber = $fullname = $userid = $email = "";
		$rss_title = $object->rss->title;
		$rss_url = $object->rss->url;
		if(key($object) == 'course')
		{
			$property ='course';
			$coursename  = $object->{$property}->fullname;
			$courseid = $object->{$property}->id;
			$idnumber = $object->{$property}->idnumber;
		}
		else
			{
				$property = 'user';
				$fullname = $object->{$property}->firstname." ".$object->{$property}->lastname;
				$userid = $object->{$property}->id;
				$email = $object->{$property}->email;
			}
			fputcsv($fp,array($coursename,$courseid,$idnumber,$fullname,$userid,$email,$rss_title,$rss_url));
	}
	header('Content-type: application/csv');
	readfile($filename);
}


/*****************************************************/
global $CFG, $DB;
require_once($CFG->libdir.'/simplepie/moodle_simplepie.php');
require_once('lib.php');
$objCheckRSS = new RSSChecker();
$bad_feeds = $objCheckRSS->get_bad_feeds();
$block_instances = $objCheckRSS->get_rss_instances();
$data = $objCheckRSS->getRSSData($block_instances, $bad_feeds);
echo $objCheckRSS->create_rss_html($data);
 ?>
 <a href="downloadcsv.php">Click here to download the CSV version</a>
 </body>
 </html>