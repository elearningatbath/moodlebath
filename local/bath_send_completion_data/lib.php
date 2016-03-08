<?php
defined('MOODLE_INTERNAL') or die; // Not allowed for non-admins
require_once($CFG->libdir . '/enrollib.php');
require_once($CFG->dirroot. '/local/bath_send_completion_data/classes/completiondata.class.php');
require_once($CFG->dirroot. '/local/bath_send_completion_data/classes/filter_completion.php');
require_once($CFG->dirroot. '/local/bath_send_completion_data/classes/samis.class.php');
require_once('classes/BathException.php');
/**
 * Cron to run the completion data to pass to SAMIS
 */
function local_bath_send_completion_data_scheduled_task($task_lastruntime)
{
    global $DB, $CFG;
    $config_vars = get_config('local_bath_send_completion_data');
    $courselist = $config_vars->courselist;
    $courseids = explode(",", $courselist);
    $completed_users_data = null;
    $lastcrontime = $config_vars->lastcron;
    foreach ($courseids as $courseid) {
        try {
            if ($objCourse = $DB->get_record('course', ['id' => $courseid])) {
                mtrace("++++++++ Sending completion data for ***-- $objCourse->fullname --*** occurred after ". date("F j, Y, g:i a",$lastcrontime)."  +++++++");
                $objFilterManualStudents = new local_bath_send_completion_data\filterCompletion($objCourse, 5, 'sits',$lastcrontime);
                //Get filtered enrolments
                $completed_users_data = $objFilterManualStudents->filterEnrolments();
                //Now that we have filtered data we send that data to SAMIS
                try{
                    $objSamisPush = new local_bath_send_completion_data\samisPush();
                    $lastcrontime = $task_lastruntime;
                    set_config('lastcron',$lastcrontime,'local_bath_send_completion_data');
                }
                catch(\local_bath_send_completion_data\BathException $e){
                    echo $e->getMessage();
                    return false;
                }
                if(!empty($completed_users_data)){
                    foreach ($completed_users_data as $objUserData) {
                        //$objUserData->username = 'ja704-xx'; //FIXME  - change this when going live
                        try{
                            mtrace("++++Inserting completion data for ".$objUserData->username." +++");
                            if(!$objSamisPush->insert_completion_data($objUserData)){
                                throw new \local_bath_send_completion_data\BathException("Could not insert completion data for $objUserData->username ");
                            }
                        }
                        catch (\local_bath_send_completion_data\BathException $e) {
                            mtrace($e->taskException());
                        }
                    }
                }
            } else {
                //Later this will also go into a log
                throw new \local_bath_send_completion_data\BathException("++Course id {$courseid} does not seem to a valid Moodle Course! Skipping++");
            }
        } catch (\local_bath_send_completion_data\BathException $e) {
            mtrace($e->taskException());
        }
    }

}