<?php

namespace local_bath_send_completion_data;
require_once($CFG->dirroot . '/local/sits/lib/report.class.php'); //SITS report class
require_once($CFG->dirroot . '/local/sits/lib/sits.final.class.php'); // Require SITS class
require_once('grade_module_cohort_completion.php'); //Added cohort class for sits validation
require_once('BathException.php');

/***
 * Class samisPush
 * Class used to push the completion data to SAMIS
 * @package local_bath_send_completion_data
 */
class samisPush
{
    /**
     * @var \sits
     */
    private $sits;
    /**
     * @var
     */
    private $academic_year;

    /**
     * samisPush constructor.
     */
    public function __construct()
    {
        $report = new \report();
        try {
            $this->sits = new \sits($report);
            $this->academic_year = $this->sits->get_current_academic_year(); //Get current academic year
        } catch (\Exception $e) {
            throw new BathException($e->getMessage());
        }
    }


    /**
     * Method used to validate the cohort in SAMIS
     * @param $data
     * @return mixed
     */
    private function validate_grade_module($data)
    {
        $cohorts = array();
        try {
            $this->get_module_assessment_details($data, $this->academic_year);
        } catch (\Exception $e) {
            mtrace($e->getMessage());
        }
        foreach ($data->module_details as $key => $arrayAssesmentDetails) {
            $cohorts[] = new grade_module_cohort_completion($data->sits_code, $data->psl_code, $this->academic_year, $arrayAssesmentDetails['MAV_OCCUR'], $arrayAssesmentDetails['MAP_CODE'], $arrayAssesmentDetails['MAB_SEQ']);
        }
        return $cohorts;
    }

    /**
     * Returns MAV_OCCUR , MAB_SEQ and MAP_CODE for a given unit code
     * @param $data
     * @param $academic_year
     * @return mixed
     * @throws \Exception
     */
    private function get_module_assessment_details(&$data, $academic_year)
    {
        $data->module_details = $this->sits->get_module_assessment_details($academic_year, $data->sits_code, $data->psl_code);
        if ($data->module_details === false) {
            throw new BathException("Cannot fetch Module Assessment Details for $data->sits_code. Please make sure they are set correctly");
        }
        return $data;
    }

    /**
     * Insert completion data to SAMIS
     * @param $completiondata
     * @return bool
     */
    public function insert_completion_data($completiondata)
    {
        //Prepare student details
        $student = new \stdClass();
        $student->username = $completiondata->username;
        //TODO this is only for SAMIS (test) change this when we go live
        $student->username = $completiondata->username . '-xx';
        //Prepare grading details
        $grade = $this->grading_data($completiondata->timecompleted);
        //Fetch the period slot code for the student
        //Note: There could be more than one period slot code for a student on that unit, so we need to add entries for
        //both of them
        $cohort = new \stdClass();
        $cohort->academic_year = $this->academic_year;
        try {
            $student->spr_code = $this->sits->get_spr_from_bucs_id($student->username, $cohort)->SPR_CODE;
            $psl_codes = $this->get_period_slot_code($completiondata->sits_code, $student->spr_code);
            //If more than one, loop it !
            if ($psl_codes !== false) {
                foreach ($psl_codes as $pslcode) {
                    $completiondata->psl_code = $pslcode;
                    try {
                        //Update data in the new table
                        $this->update_completion_data($completiondata, $student, $grade);
                    } catch (BathException $e) {
                        mtrace($e->taskException());
                    }
                }
            }
            return true;
        } catch (BathException $e) {
            mtrace($e->taskException());
        }
    }

    /**
     * Update the completion data in SAMIS
     * @param $data
     * @param $student Student details
     * @param $grade Grade details
     * @return bool True or False
     */
    private function update_completion_data($data, $student, $grade)
    {
        $cohorts = $this->validate_grade_module($data);
        if (!empty($cohorts)) {
            foreach ($cohorts as $cohort) {
                try {
                    $this->update_record($cohort, $student, $grade);
                    mtrace("Completion Data recorded for COHORT: " . $cohort->sits_code . " and Student: " . $student->username);
                } catch (BathException $e) {
                    mtrace($e->taskException());
                }
                return true;
            }
        }
    }

    /** Update the record in SAMIS
     * @param $cohort
     * @param $student
     * @param $grade
     * @return bool
     * @throws \Exception
     */
    private function update_record($cohort, $student, $grade)
    {
        if (!$this->sits->update_agreed_grade($student, $grade, $cohort)) {
            //Update not possible, try inserting it
            try {
                if (!empty($student->spr_code))
                    $this->sits->insert_agreed_grade($student, $grade, $cohort);
                return true;
            } catch (BathException $e) {
                throw new BathException("Cannot insert grade data for $student->username under cohort $cohort->sits_code");
            }
        }
    }

    /**
     * Return period slot code for a username on a course based on the current academic year
     * @param $sits_code
     * @param $username
     * @return mixed
     * @throws \Exception
     */
    private function get_period_slot_code($sits_code, $spr_code)
    {
        try {
            if (is_null($spr_code)) {
                throw new \Exception("Could not retrieve SPR Code for user registered for $sits_code");
            }
            $psl_codes = $this->sits->get_period_slot_code_for_unit($this->academic_year, $sits_code, $spr_code);
            if (empty($psl_codes)) {
                throw new BathException("Could not retrieve current PSL code for $sits_code");
            }
            return $psl_codes;
        } catch (BathException $e) {
            throw new BathException($e->taskException());
        }
    }

    /**
     * Preparing the data to be sent to SAMIS
     * @param $cc_date_time Date the course was completed
     * @return \stdClass
     */
    private function grading_data($cc_date_time)
    {
        $grade = new \stdClass();
        //TODO this will not be needed, its not being used by gradeout but the sits db astract function
        //needs it
        $grade->sumgrades = 0;
        /*
         * SAS Table
         */
        $grade->sas_agrg = 'C'; //Agreed Grade
        $grade->sas_coma = 1;
        /*
         * SMR Table
         */
        $grade->smr_agrd = date('d/m/Y H:i:s', $cc_date_time); // Formatted agreed mark
        $grade->smr_rslt = 'C'; //Result of Module
        $grade->smr_coma = 1; //Completed Number
        $grade->smr_cred = 0.00; //Credits passed
        $grade->smr_proc = 'COM'; //Process status
        return $grade;
    }

    /**
     * Sends a warning email about SAS1B
     * @param $student
     * @param $cohort
     * (SOT make this sas1b_warning function work for Research integrity and put it into samis.class.php)
     */
    public function sas1b_warning(&$student, &$cohort)
    {
        global $CFG, $DB;
        // $CFG->local_bath_completion_data_warn_user contains a list of comma separated email addresses
        $warn_user_emails = explode(',', get_config('local_bath_send_completion_data', 'sas1bemail'));
        foreach ($warn_user_emails as $email) {
            //Get $user object from $email
            $user = $DB->get_record('user', ['email' => $email]);
            $cohortcode = $cohort->sits_code;

            $from = get_string('warn_from', 'local_bath_send_completion_data');
            $subject = get_string('warn_subject', 'local_bath_send_completion_data', $student->username);
            $messagetext = get_string('warn_messagetext', 'local_bath_send_completion_data', $cohortcode) . ', ' . $student->username;
            try {
                if (!email_to_user($user, $from, $subject, $messagetext)) {
                    throw new \Exception ("Could not send email to $user->email regarding SAS1B for $cohortcode");
                }
            } catch (\Exception $e) {
                mtrace($e->getMessage());
            }
        }
        mtrace('SAS1B NOT DONE IN COHORT ' . $cohort->sits_code . ', student ' . $student->username . '. User ' . $user->email . ' have been informed.');
    }
}