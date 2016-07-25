<?php
/* SITS Integration Block
 *
 * Copyright (C) 2011 University of Bath
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details:
 *
 * http://www.gnu.org/copyleft/gpl.html
 */

/**
 * @package    blocks
 * @subpackage sits
 * @copyright  2011 University of Bath
 * @author     Alex Lydiate {@link http://alexlydiate.co.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_sits extends block_base {

    function init() {
        $this->title = get_string('sits', 'block_sits');
        $this->cron = 300;
    }

    function get_content()
    {
        global $CFG, $COURSE;
        $context = context_course::instance($COURSE->id);
        if(has_capability('moodle/course:update', $context))
        {
            if ($this->content !== NULL)
            {
                return $this->content;
            }else{
                $this->set_content();
                return $this->content;
            }
        }
        else
        {
            return null; //students don't get to see the block.
        }
    }

    function instance_allow_config() {
        return true;
    }

    function set_content(){
        GLOBAL $CFG, $COURSE;
        $context = context_course::instance(1);
        $cohorts_title = get_string('link_cohorts','block_sits');
        $adduser_title = get_string('add_user','block_sits');
        $sits_gui_enabled = get_config('block_sits', 'sits_gui_enabled');
        if($sits_gui_enabled){
            $markup = <<<html
<script type="text/javascript">
</script>
<a href="#" onclick="sits_block.open_samis_cohort_window();">$cohorts_title</a><br/>
<a href="#" onclick="sits_block.open_samis_add_user_window();">$adduser_title</a>
html;

            if(has_capability('moodle/site:config', $context)){
                $markup .= '<br/>---';
                $markup .= '<br/><a href="#" onclick="sits_block.open_samis_admin_window();">' . get_string('sits_admin','block_sits') . '</a>';
            }
        }else{
            $markup = '<b>The block is currently disabled</b>.<br/><br/>' . get_config('block_sits','sits_disable_message') . '</br/>';
        }
            //Administrator Only Functionality
            if(has_capability('moodle/site:config', $context)){
            $markup .= '<br/><a href="/admin/settings.php?section=blocksettingsits">' . get_string('sits_settings','block_sits') . '</a>';
        }
        $this->content = new stdClass;
        $this->content->text = $markup;
        $this->content->footer = '';
    }

    function has_config() {
        return true;
    }
}
