<?php
if(!@$_POST['loadusers'])
{
	?>
	<html>
		<body>
			<p>Get all Users who haven't logged in for at least 365 days AND are not in LDAP</p>
			<form method="post">
				<input type="submit" value="Download CSV" />
				<input type="hidden" name="loadusers" value="true" />
			</form>
		</body>
	</html>
	<?php
}
else
{
	ini_set('max_execution_time',0);

	ob_start();

	require_once('../config.php');
	$days = 365;
	$server = "ldap.bath.ac.uk";
	$get_old_users = get_old_users($days);
	$attributes = array("accountState");
	$ldapc = ldap_connect($server);
	$count = 0;
	$export_users = array();
	foreach($get_old_users as $old_user)
	{
		$username = $old_user->username;
		// Set up variables we'll use to record the state.
		$sr = ldap_search($ldapc, "ou=people,o=bath.ac.uk", "uid=".$username, $attributes);

		//If we receive precicely zero results then return the user for deletion
		if (ldap_count_entries($ldapc, $sr) === 0) {
			/*$values = ldap_get_values($ldapc, ldap_first_entry($ldapc, $sr), "accountState");*/
			$count++;
			$export_users[] = $old_user;
		}
	}
	if(!empty($export_users))
	{
		create_csv($export_users,'users_not_logged_in');
	}
}




function create_csv($users,$filename){
	//print_r($users);
	ob_end_clean();
	header('Content-disposition: attachment; filename='.$filename.'.csv');
	header('Content-type: application/csv');
	$fp = fopen('php://output','w') or die("Cant create / open the file!");
	//First write the headers
	$headers = array("username","deleted");
	fputcsv($fp, $headers);
	
	foreach ($users as $objUser){
		//Write data to CSV
		fputcsv($fp,array($objUser->username,'1'));
	}
	readfile("php://output");
}
function get_old_users($days){
	global $DB;
	$sql = " 
	SELECT DISTINCT u.username
	-- ,IF(u.lastlogin = 0 ,0,DATE_FORMAT(FROM_UNIXTIME(u.lastlogin), '%d-%m-%Y ')) AS 'lastlogin'
	FROM {user} u 
	WHERE 
		lastlogin < UNIX_TIMESTAMP(DATE_SUB(SYSDATE(),INTERVAL $days DAY))
	AND deleted = 0 -- Only show users that are visible from the Moodle interface
	AND lastaccess < UNIX_TIMESTAMP(DATE_SUB(NOW(),INTERVAL $days DAY))
	AND u.username NOT IN ('admin','guest')
	"; 

	$result = $DB->get_records_sql($sql);
	return $result;
}