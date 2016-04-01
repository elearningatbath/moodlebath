<?php

namespace local_bath_send_completion_data\task;
class pass_completion_data extends \core\task\scheduled_task {
    public $sitsconnected = true;
    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name(){
        return get_string('pluginname','local_bath_send_completion_data');
    }
    /**
     * Run grade pass cron.
     */
    public function execute(){
        global $CFG,$DB;
        require_once($CFG->dirroot . '/local/bath_send_completion_data/lib.php');
        //$task_lastruntime = $DB->get_field('task_scheduled','lastruntime',array('component' => 'local_bath_send_completion_data'));
        $task_lastruntime = parent::get_last_run_time();
        local_bath_send_completion_data_scheduled_task($task_lastruntime);

    }

}