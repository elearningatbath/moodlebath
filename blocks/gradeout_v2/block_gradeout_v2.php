<?php

class block_gradeout_v2 extends block_base
{

    function init()
    {
        GLOBAL $CFG;
        $this->title = get_string('pluginname', 'block_gradeout_v2');

        $this->version = 2016030700;

        //the $CFG->block_gradeout_v2_cron UI field has been removed
        /*        if (isset($CFG->block_gradeout_v2_cron)) {
                    $this->cron = $CFG->block_gradeout_v2_cron;
                } else {
                    $this->cron = 30;
                }*/

    }

    /**
     * Block can be viewed/installed only one for the whole site
     * cannot have many instances in several courses
     *
     * @return array
     */
    function applicable_formats()
    {
        return array('all' => false, 'site' => true);
    }

    /**
     * Disallows configuring at instance level.
     * @return boolean
     **/
    function instance_allow_config()
    {
        return false;
    }

    /* Allows block to be globally configured from admin
     * Makes the UI available to administrators only
     * @return boolean
     **/
    function has_config()
    {
        return true;
    }

    function get_content()
    {
        if ($this->content !== NULL) {
            return $this->content;
        }
        global $CFG, $COURSE;
        $context = context_course::instance($COURSE->id);
        $this->content = new stdClass;
        $this->content->text = '';

        if (isset($this->config->text)) {
            $this->content->footer = $this->config->text;
        } else {
            $this->content->footer = '';
        }
        return $this->content;
    }


    function cron()
    {
        /** TODO THIS IS THE OLD CRON FUNCTION AND WILL BE REMOVED
         * Run the cron for Gradeout to transfer grades from Moodle to SAMIS
         * @return bool
         * This is where all the magic happens. It integrates the sits and the cohort class from the sits/lib folder to
         * be used in the plugin. It first checks for cron using the config value stored in the plugins table.
         * It then creates a new instance of sits class, get the current academic year and the other settings for the
         * plugin. For each courseid entered in the $CFG->block_gradeout_v2_courses fields, it fetches the cohorts, context,
         * users in the context with their roles and finally gets all the quiz activities for that course.
         */

    }


}
