<?php
namespace local_bath_send_completion_data;
require_once('completiondata.class.php');
/**
 * Class filterCompletion
 * @package local_bath_send_completion_data
 */
class filterCompletion
{
    public $role_type;
    public $enrolment_type;
    public $completion_data;
    public $filtered_user_completion_data;

    /**
     * Returns completion data based filters
     * filterCompletion constructor.
     * @param $course Moodle course
     * @param $role Moodle user roles
     * @param $enrolment_type Moodle enrolment type
     */
    public function __construct($course, $role, $enrolment_type,$lastcron){
        $this->role_type = $role;
        $this->enrolment_type = $enrolment_type;
        $objCC = new courseCompletionData($course);
        $this->completion_data =  $objCC->getCompletionData($lastcron);
        $this->context = $objCC->context;
        $this->completedusers = $objCC->totalcompletedusers();
    }
    /**
     * Filters enrolments based on constructor arguments
     * @return array
     */
    public function filterEnrolments(){

        $objFilteredData = array();
        foreach($this->completion_data as $userid => $arrUser){
            foreach($arrUser as $enrolid => $objCompletionData){
                $objCompletionData->username = $objCompletionData->username;
                if($objCompletionData->enrolment_type == $this->enrolment_type && $objCompletionData->role = $this->role_type){
                    $objFilteredData[] = $objCompletionData;
                }
            }
        }
        return $objFilteredData;
    }
}