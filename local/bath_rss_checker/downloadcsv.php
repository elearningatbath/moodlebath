<?php 
require_once ( '../../config.php');
require_once($CFG->libdir.'/simplepie/moodle_simplepie.php');
require_once('lib.php');
$objCheckRSS = new RSSChecker();
$bad_feeds = $objCheckRSS->get_bad_feeds();
$block_instances = $objCheckRSS->get_rss_instances();
$data = $objCheckRSS->getRSSData($block_instances, $bad_feeds);
$objCSV = new createCSV($data);
?>
