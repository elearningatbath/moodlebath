<?php
namespace local_bath_send_completion_data;
require_once($CFG->dirroot . '/local/sits/lib/cohort.class.php'); //Added cohort class for sits validation
/**
* Class grade_module_cohort_completion
* @package local_bath_send_completion_data
*/
class grade_module_cohort_completion extends \grade_module_cohort
{
    public $type,$sits_code,$academic_year,$mav_occur,$map_code,$mab_seq;

    /**
    * grade_module_cohort_completion constructor.
    * @param $sits_code
    * @param $period_code
    * @param $academic_year
    * @param $mav_occur
    * @param $mab_seq
    * @param $map_code
    */
    public function __construct($sits_code, $period_code, $academic_year, $mav_occur,$map_code, $mab_seq){

$this->type ='grade_module_completion';
$this->sits_code = $sits_code;
$this->period_code = $period_code;
$this->academic_year = $academic_year;
$this->mav_occur = $mav_occur;
$this->mab_seq = $mab_seq;
$this->map_code = $map_code;
$this->validate_module();
    }
}