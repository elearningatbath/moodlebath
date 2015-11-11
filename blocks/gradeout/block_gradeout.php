<?php
class block_gradeout extends block_base {

	function init(){
		GLOBAL $CFG;
		$this->title = get_string('pluginname','block_gradeout');
		if (isset($CFG->block_gradeout_cron)){
			$this->cron = $CFG->block_gradeout_cron;
		} else {
			$this->cron = 30;
		}
		$this->version=2011040801;
	}
	/**
	 * Block can be viewed on site level only
	 * @return array
	 */
	function applicable_formats() {
		return array('all' => false, 'site' => true);
	}
	/**
	 * Disallows configuring at instance level.
	 * @return boolean
	 **/
	function instance_allow_config() {
		return false;
	}
	/* Allows block to be globally configured from admin
	 * @return boolean
	 **/
	function has_config() {
		return true;
	}

    /**
     * Run the cron for Gradeout to transfer grades from Moodle to SAMIS
     * @return bool
     */
    function cron() {
		global $CFG, $DB;
		global $last_cron_time;
		// these includes should point to the core classes in sits block (or local?)
		require_once($CFG->dirroot . '/local/sits/lib/sits.final.class.php');
		$starttime = microtime();
		$thisblock = $DB->get_record('block',array('name'=>'gradeout')); // 19 to 22 conversion - Hittesh - UoB
		if($thisblock->cron != $CFG->block_gradeout_cron){
			if ($CFG->block_gradeout_cron == 0){
				mtrace('disabled. set cron > 0');
				return true;
			}
			$thisblock->cron = $CFG->block_gradeout_cron;
			$DB->update_record('block',$thisblock);
		}
		$this->report = new report(); // Added as it requires it - Hittesh
		$sits = new sits($this->report); //Added as it requires it - Hittesh
		if(!$sits){
			//SITS is offline
			 mtrace("Delaying exporting QA53 grades until SITS is back online");
			 return false;
		}
		$cur_ac_year = $sits->get_current_academic_year();
		$courses = explode(',', $CFG->block_gradeout_courses);
		$last_cron_time = $DB->get_field('block', 'lastcron', array('name'=> 'gradeout')); // Hittesh Ahuja 19 to 22 conversion
		mtrace('last run '.$last_cron_time);
		mtrace('pass mark is '.$CFG->block_gradeout_passmark.'%');
		foreach ($courses as $courseid){
			$course = $DB->get_record('course',array( 'id'=>$courseid)); // 19 to 22 conversion - Hittesh
			try{
				//$sits_code, $period_code, $academic_year, $mav_occur, $map_code, $mab_seq
				$cohort = new grade_module_cohort($course->idnumber, 'AY', $cur_ac_year,'A',$course->idnumber.'A','01');
				$context = context_course::instance($courseid);
				$students = get_role_users(5,$context,false,'u.id, u.username');
				is_array($students) ? $count = count($students) : $count = 0;
				if (!$quizzes = get_coursemodules_in_course("quiz", $courseid)){
					$quizzes = null;
					mtrace('no quiz in course id '.$courseid.': check config?');
				} else {
					mtrace('grading course '.$course->idnumber.' (id '.$courseid.'), '.$count.' students, '.count($quizzes).' quiz(zes)');
					foreach($quizzes as $quiz){
						$this->grade_quiz($sits,$quiz,$students,$cohort);
					}
				}
			} catch (Exception $e) {
				$quoteid = $course->idnumber ? $quoteid='"'.$course->idnumber.'"' : $quoteid='blank';
				mtrace($e->getMessage().': idnumber is '.$quoteid.' in course '.$courseid);
			}
		}
		
		$difftime = microtime_diff($starttime, microtime());
		mtrace(count($courses)." courses graded (took ".$difftime." seconds)");
		return true;
	}

    /**
     * @param $sits
     * @param $quiz
     * @param $students
     * @param $cohort
     */
    function grade_quiz(&$sits,&$quiz,&$students,&$cohort){

		global $CFG, $last_cron_time,$DB;
		// this takes raw mark from all first successful attempts since last cron
		$query = 'select userid, sumgrades, min(timefinish) as timefinish,timemodified from '.$CFG->prefix.'quiz_attempts where quiz = '.$quiz->instance.
			' and sumgrades > '.$CFG->block_gradeout_passmark.
			' and timefinish > '.$last_cron_time.' group by userid order by timefinish';
        if($grades = $DB->get_recordset_sql($query)){
			foreach ($grades as $grade){ // while($grade = rs_fetch_next_record($grades)){ //deprecated in 2
				$grade->sas_agrg = 'P';
				$grade->smr_agrd = date("d M Y H i s");
				/* Update request from Amy Cavanagh - Hittesh Ahuja*/
				$grade->smr_rslt = 'P';
				$grade->smr_cred = 0.00;
				$grade->smr_coma = 1;
				$grade->sas_coma = 1;
				$grade->smr_proc = 'COM';
				if($this->update_record($sits,$students[$grade->userid],$grade,$cohort)){
					mtrace('pass recorded: '.$students[$grade->userid]->username.' :: '.$cohort->sits_code.' :: '.$grade->sumgrades.' :: '.$grade->timemodified);
				} else {
					mtrace('error: pass NOT recorded: '.$students[$grade->userid]->username.' :: '.$cohort->sits_code.' :: '.$grade->sumgrades.' :: '.$grade->timemodified);
				}
			}
		}
	}

    /**
     * Updates SAMIS Record
     * @param $sits
     * @param $student
     * @param $grade
     * @param $cohort
     * @return bool
     */
    function update_record(&$sits,&$student,&$grade,&$cohort){
		if(!$sits->update_agreed_grade($student,$grade,$cohort)){
			$this->sas1b_warning($student,$cohort);
			return $sits->insert_agreed_grade($student,$grade,$cohort);
		} else {
			return true;
		}
	}

    /**
     * Sends a warning email about SAS1B
     * @param $student
     * @param $cohort
     */
    function sas1b_warning(&$student,&$cohort){
        global $CFG;
		$warn_users = explode(',', $CFG->block_gradeout_warn_user);
		foreach($warn_users as $email){
            $user->email = $email;
            $user->fullname = get_string('warn_convenor','block_gradeout');
			$from = get_string('warn_from','block_gradeout');
            $subject = get_string('warn_subject','block_gradeout');
            $messagetext = get_string('warn_messagetext','block_gradeout').$cohort->sits_code.', '.$student->username;
            email_to_user($user, $from, $subject, $messagetext);
		}
		mtrace('SAS1B NOT DONE IN COHORT '.$cohort->sits_code.', student '.$student->username.'. Users '.$CFG->block_gradeout_warn_user.' have been informed.');
	}

	function get_content(){
		if ($this->content !== NULL) {
			return $this->content;
		}
		global $CFG, $COURSE;
        $context = context_course::instance($COURSE->id);
		$this->content =  new stdClass;
		$this->content->text = '';

		if(isset($this->config->text)){
			$this->content->footer = $this->config->text;
		} else {
			$this->content->footer = '';
		}
		return $this->content;
	}
}
