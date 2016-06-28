<?php
namespace local_bath_send_completion_data;
    /**
     * Class courseCompletionData
     * @package local_bath_send_completion_data
     * Class to get raw completion data from Moodle table.
     * For each course ,we get all the nerolled users. For each enrolled user we fetch their completion data
     */
/**
 * Class courseCompletionData
 * @package local_bath_send_completion_data
 */
class courseCompletionData
{
    private $course;
    public $context;
    public $totalcompletedusers;
    private $context_level = CONTEXT_COURSE;

    /**
     * courseCompletionData constructor.
     * @param $course
     */
    public function __construct($course){
        $this->course = $course;
    }
    /**
     * Returns context object for a given course id
     * @return mixed
     */
    private function getContextfromCourse(){
        return \context_course::instance($this->course->id);
    }

    /**
     * Gets enrolled users for a given course context
     * @return mixed
     */
    private function getEnrolledUsers(){
        global $DB,$CFG;
        $this->context = $this->getContextfromCourse();
        return get_enrolled_users($this->context);
    }

    /**
     * public function accessed by plugin to get completion data
     * @return mixed
     */
    public function getCompletionData($lastcron){
        global $DB;
        $enrolledusers = $this->getEnrolledUsers();
        //Get completion data for those enrolled users
        if(!empty($enrolledusers)){
            foreach($enrolledusers as $user){
                $this->totalcompletedusers = $this->totalcompletedusers + 1 ;
                $userid = $user->id;
                $sql = $this->completionSQL($lastcron);
                $usercompletiondata[$userid] = $DB->get_records_sql($sql,[$userid]);
            }
            foreach($usercompletiondata as $key => $data){
                if(empty($data)){
                    unset($usercompletiondata[$key]);
                    $this->totalcompletedusers = $this->totalcompletedusers - 1 ;
                }
            }
            return $usercompletiondata;
        }
    }
    public function totalCompletedUsers(){
        return $this->totalcompletedusers;
    }
    /**
     * RAW SQL to show courses with completions
     * @return string
     */
    private function completionSQL($lastcron){
        $sql = <<<sql
        SELECT e.id AS 'enrolid',e.enrol AS 'enrolment_type',cc.id AS 'course_completion_id',cc.course,c.idnumber AS 'sits_code',u.id AS 'userid',u.username AS 'username',u.firstname,u.lastname,ra.roleid AS 'role',
        cc.timecompleted AS 'timecompleted'
        FROM {course} c
        JOIN {enrol} e ON e.courseid = c.id
        JOIN {user_enrolments} ue ON ue.enrolid = e.id
        JOIN {context} ctx ON ctx.instanceid = c.id AND ctx.contextlevel = $this->context_level
        JOIN {user} u
        ON u.id = ue.userid
        JOIN {role_assignments} ra ON ra.userid = u.id AND u.id = ?
        JOIN {course_completions} cc ON c.id = cc.course AND cc.userid = ra.userid AND cc.timestarted <> 0
        AND cc.timecompleted IS NOT NULL
        WHERE c.id = {$this->course->id} AND cc.timecompleted > {$lastcron}
        GROUP by ue.userid
        ORDER BY cc.timecompleted
sql;

        return $sql;
    }


}