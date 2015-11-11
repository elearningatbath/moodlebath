<?php

class createCSV {
	
	public function __construct($content)
	{
		header('Content-disposition: attachment; filename=dead_rss_feeds.csv');
		header('Content-type: application/csv');
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
	readfile("php://output");
		
	}
}

class RSSChecker{
	
	public $feeds = array();
	public $rss_block_instances;
	
	public function __construct()
	{
		$this->feeds = $this->get_rss_feeds();
	}
	public function get_rss_instances()
	{
		global $CFG, $DB;
		return $rss_block_instances = $DB->get_records('block_instances',array('blockname'=>'rss_client'));
		
	}
	public function get_rss_feeds()
	{
		global $CFG, $DB;
		$rs = $DB->get_recordset('block_rss_client');
		return $rs;
	}
	
	function create_rss_html($arrObjRSS)
	{
		$html = "";
		foreach($arrObjRSS as $object)
		{
			//print_r($object);
			$html.= "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" >";
			//RSS Details
			$rss_url = $object->rss->url;
			$rss_title = $object->rss->title;
			if(key($object) == 'course')
			{
				//var_dump($object->course);
				//Course header
				$html.= "<thead><tr><th>Course Details</th><th>RSS feed</th></tr></thead>";
				//Course Details
				$fullname = $object->course->fullname;
				$id = $object->course->id;
				$idnumber =$object->course->idnumber;
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
		}
	$html.= "</table>";
	return $html;
}
	public function get_bad_feeds()
	{
		$errors = array();
		foreach($this->feeds as $record)
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
            } 
		}
		return $errors;
	}
	private function get_enrolments($userid)
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
	public function getRSSData($instances,$errors)
	{
		$arrObj = array();
		global $CFG, $DB;
		foreach($instances as $key => $block_instance)
		{
			$parentcontextid = $block_instance->parentcontextid;
			$context = $DB->get_records('context',array('id'=>$parentcontextid));
			$level = $this->get_context_level($context[$parentcontextid]->contextlevel);
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
		return $arrObj;
	}
		 
	private function get_context_level($context)
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
		
}
