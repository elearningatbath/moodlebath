<?php


/**
 * Created by PhpStorm.
 * User: sf427
 * Date: 08/03/2016
 * Time: 10:54
 */

//namespace gradeout_v2_task\task;

class gradeout_v2_task extends \core\task\scheduled_task
{
    public function get_name()
    {
        // Shown in admin screens
        return get_string('pluginname', 'block_gradeout_v2');
    }

    public function execute()
    {
        global $CFG, $DB;
        global $last_cron_time;
        // these includes should point to the core classes in sits block (or local?)
        require_once($CFG->dirroot . '/local/sits/lib/sits.final.class.php');

        $starttime = microtime();//gets the current time in microseconds
        //gets the database gradeout_v2 RECORD in the Block tbl and assigns it to $thisblock
        $thisblock = $DB->get_record('block', array('name' => 'gradeout_v2')); // 19 to 22 conversion - Hittesh - UoB

        /*
        * $thisblock->cron Gets the 'cron' field of the retrieved record. The cron field contains the interval
        * (in seconds) at which the cron should run. If there is a difference in the UI and DB cron interval then
        * get the (NEW) UI cron interval and put it in the DB.
        * If the UI cron interval is (0) disabled then return TRUE and exit,
        *
        */

        /*   THE CODE BELOW HAS BEEN DISABLED BECAUSE THE $CFG->block_gradeout_v2_cron TEXTBOX IN THE UI HAS BEEN REMOVED
            if ($thisblock->cron != $CFG->block_gradeout_v2_cron) {
            if ($CFG->block_gradeout_v2_cron == 0) {//if UI cron interval == 0 disabled
                mtrace('disabled. set cron > 0');
                return false;// cron is disabled so exit this function
            }

            $thisblock->cron = $CFG->block_gradeout_v2_cron;//assigns the UI cron time to the DB field
            $DB->update_record('block', $thisblock);//updates the 'block' table sets 'cron' field to UI value
        }*/
        //SITS requires that we pass it a report (make the log_report function available)
        $this->report = new report(); // Added as it requires it - Hittesh
        try{
            $sits = new sits($this->report); //Added as it requires it - Hittesh
        }
        catch(\Exception $e){
            $e->getMessage();
            mtrace("Delaying exporting QA53 grades until SITS is back online");
            return false;
        }
        //checks if SITS is NOT online
        if (!$sits) {
            //SITS is offline
            mtrace("Delaying exporting QA53 grades until SITS is back online");
            return false;// exits cron() function
        }

        $cur_ac_year = $sits->get_current_academic_year();

        $courses = explode(',', $CFG->block_gradeout_v2_courses);//Turns a comma separated string to an array

        //when did the cron run last time?
        $last_cron_time = $DB->get_field('block', 'lastcron', array('name' => 'gradeout_v2')); // Hittesh Ahuja 19 to 22 conversion
        mtrace('last run ' . $last_cron_time);
        mtrace('pass mark is ' . $CFG->block_gradeout_v2_passmark . '%');
        /* 'courses' is an array of all the course ids in the UI text field
		for each course do...
		 */
        foreach ($courses as $courseid) {
            //gets one DB record for the specific course id
            $course = $DB->get_record('course', array('id' => $courseid)); // 19 to 22 conversion - Hittesh
            try {
                //get what type of cohort it is: program, module, or grade_module?
                //$sits_code, $period_code, $academic_year, $mav_occur, $map_code, $mab_seq
                $cohort = new grade_module_cohort($course->idnumber, 'AY', $cur_ac_year, 'A', $course->idnumber . 'A', '01');
                $context = context_course::instance($courseid);
                //Get all the users assigned this role (5 = students) in this context or higher
                $students = get_role_users(5, $context, false, 'u.id, u.username');
                /* if $students is an array then put number of elements in $count */
                is_array($students) ? $count = count($students) : $count = 0;
                //if there are no quizzes in the specific module then $quizzes = null
                // ELSE
                //For Each Quizz in the module...
                if (!$quizzes = get_coursemodules_in_course("quiz", $courseid)){//returns an array of the quizzes for the specific module
                    $quizzes = null;
                    mtrace('no quiz in course id ' . $courseid . ': check config?');
                } else {
                    mtrace('grading course ' . $course->idnumber . ' (id ' . $courseid . '), ' . $count . ' students, ' . count($quizzes) . ' quiz(zes)');
                    foreach ($quizzes as $quiz) {
                        $this->grade_quiz($sits, $quiz, $students, $cohort);
                    }
                }
            } catch (Exception $e) {
                $quoteid = $course->idnumber ? $quoteid = '"' . $course->idnumber . '"' : $quoteid = 'blank';
                mtrace($e->getMessage() . ': idnumber is ' . $quoteid . ' in course ' . $courseid);
            }
        }

        $difftime = microtime_diff($starttime, microtime());
        mtrace(count($courses) . " courses graded (took " . $difftime . " seconds)");
        return true;

    }

    /**
     * @param $sits
     * @param $quiz
     * @param $students
     * @param $cohort
     */
    function grade_quiz(&$sits, &$quiz, &$students, &$cohort)
    {

        global $CFG, $last_cron_time, $DB;

        // this takes raw mark from all first successful attempts since last cron
        //$CFG->prefix is the tables prefix for example mdl_
        $query = 'select userid, sumgrades, min(timefinish) as timefinish,timemodified from ' . $CFG->prefix . 'quiz_attempts where quiz = ' . $quiz->instance .
            ' and sumgrades > ' . $CFG->block_gradeout_v2_passmark .
            ' and timefinish > ' . $last_cron_time . ' group by userid order by timefinish';

        /* The SQL is as follows:
        'select userid, sumgrades, min(timefinish) as timefinish, timemodified from mdl_quiz_attempts where quiz = 2
         * and sumgrades > 84 and timefinish > 0 group by userid order by timefinish';
         * */
        if ($grades = $DB->get_recordset_sql($query)) {
            foreach ($grades as $grade) { // while($grade = rs_fetch_next_record($grades)){ //deprecated in 2
                $grade->sas_agrg = 'P';
                $grade->smr_agrd = date("d M Y H i s");
                /* Update request from Amy Cavanagh - Hittesh Ahuja*/
                $grade->smr_rslt = 'P';
                $grade->smr_cred = 0.00;
                $grade->smr_coma = 1;
                $grade->sas_coma = 1;
                $grade->smr_proc = 'COM';
                if ($this->update_record($sits, $students[$grade->userid], $grade, $cohort)) {
                    mtrace('pass recorded: ' . $students[$grade->userid]->username . ' :: ' . $cohort->sits_code . ' :: ' . $grade->sumgrades . ' :: ' . $grade->timemodified);
                } else {
                    mtrace('error: pass NOT recorded: ' . $students[$grade->userid]->username . ' :: ' . $cohort->sits_code . ' :: ' . $grade->sumgrades . ' :: ' . $grade->timemodified);
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
    function update_record(&$sits, &$student, &$grade, &$cohort)
    {
        if (!$sits->update_agreed_grade($student, $grade, $cohort)) {
            $this->sas1b_warning($student, $cohort);
            return $sits->insert_agreed_grade($student, $grade, $cohort);
        } else {
            return true;
        }
    }

    /**
     * Sends a warning email about SAS1B
     * @param $student
     * @param $cohort
     */
    function sas1b_warning(&$student, &$cohort)
    {
        global $CFG;
        $warn_users = explode(',', $CFG->block_gradeout_v2_warn_user);
        foreach ($warn_users as $email) {
            $user->email = $email;
            $user->fullname = get_string('warn_convenor', 'block_gradeout_v2');
            $from = get_string('warn_from', 'block_gradeout_v2');
            $subject = get_string('warn_subject', 'block_gradeout_v2');
            $messagetext = get_string('warn_messagetext', 'block_gradeout_v2') . $cohort->sits_code . ', ' . $student->username;
            email_to_user($user, $from, $subject, $messagetext);
        }
        mtrace('SAS1B NOT DONE IN COHORT ' . $cohort->sits_code . ', student ' . $student->username . '. Users ' . $CFG->block_gradeout_v2_warn_user . ' have been informed.');
    }



    /*(Optional - since 2.8) Run this task even when the plugin is disabled. In rare cases, you may want the scheduled
     tasks for a plugin to run, even when the plugin is disabled. Some of the enrolment plugins do this to clean up data.
     If this is the case, the scheduled task must override the "get_run_if_component_disabled()" method and return true
     instead of false. If they do not do this, the scheduled task will not be run while the plugin is disabled.
     * */
    private function get_run_if_component_disabled()
    {
        return true;

    }


}