<?php

class block_bath_oue extends block_base
{
    public function init()
    {
        global $OUTPUT;
        $this->title = get_string('pluginname', 'block_bath_oue') . ' ' . $OUTPUT->help_icon('oue_view', 'block_bath_oue');
    }
    public function get_content()
    {
    	
        global $OUTPUT,$USER,$CFG,$COURSE;
		$CFG->jsrev = -1;
        if ($this->content !== null) {
            return $this->content;
        }
        if (!isloggedin()) {
            return $this->content;
        }
        $module              = array(
            'name' => 'bath_oue',
            'fullpath' => '/blocks/bath_oue/js/module.js',
            'strings' => array(
                array(
                    'survey_message',
                    'block_bath_oue'
                )
            ) 
        );
        $params              = array(
            'username' => $USER->username,
        );
        $this->page->requires->js_init_call('M.OUE.init', array(
            $params
        ), false, $module);
		$sotw_link = "https://www.bath.ac.uk/samis-sso/eval";
		$this->content = new stdClass;
		$this->content->text = "<div  style=\"display:none;\" id=\"no_survey_results\">You have no unit evaluations pending</div>";
        $this->content->text .= "<div id=\"survey_loading\"></div>";
        $this->content->text .= "<div id=\"oue_notice\" class =\"wide\" style=\"display:none;\"></div>";
        $this->content->text .= "<div id=\"notice_links\" style=\"display:none;\"><a href=\"#\" onClick=\"_gaq.push(['_trackEvent','OUE','clicked','Complete Now Link']);window.open('$sotw_link','OUE','resizable=yes,scrollbars=yes,width ='+document.documentElement.clientWidth +',height='+document.documentElement.clientHeight+'');return false\">Complete now &amp; enter prize draw for &pound;500*</a> </div>";
        $oue_link_text    = get_string('oue_link_text', 'block_bath_oue');
        $survey_message   = get_string('survey_message', 'block_bath_oue');
        $global_error_msg = get_config('bath_oue', 'global_error_msg');
        $oue_link_html    = "<div id =\"oue_link\" onClick=\"_gaq.push(['_trackEvent','OUE','clicked','Complete Now Button']);window.open('$sotw_link','OUE','resizable=yes,scrollbars=yes,width ='+document.documentElement.clientWidth +',height='+document.documentElement.clientHeight+'');return false;\">$oue_link_text</div>";
		$bab_logo = $OUTPUT->pix_url('betteratbathlogo','block_bath_oue');
		$bab_image_link = "<a onClick=\"ga(['_trackEvent','OUE','clicked','Better-at-Bath Logo']);\" href=\"http://www.bath.ac.uk/students/betteratbath/\" target=\"_blank\"><img src=\"$bab_logo\" alt=\"Better @ Bath\" /></a>"; 
        $this->content->text .= "<div style=\"display:none;\" id=\"global_error_msg\"> $global_error_msg</div>";
        $this->content->text .= "<div id=\"survey_container\" style=\"display:none;\">
	    <p class=\"survey_progress\"></p>
		<progress id=\"progressbar\"  style=\"display:none;\" max=\"\" min =\"0\" value=\"\"><div id=\"svg-container\" style=\"position:relative;padding:10px\"></div></progress></div>";
		$this->content->text .= "<div class=\"static_links\">";
		$this->content->text .= $oue_link_html ;
		$this->content->text .= "<div onClick=\"ga(['_trackEvent','OUE','clicked','Previous feedback link']);\" id = \"previous_feedback\"> See how departments have responded to <a target = \"_blank\" href=\"http://www.bath.ac.uk/students/student-feedback/unit-evaluation/feedback/\">previous feedback</a></div>";
		$this->content->text .=  $bab_image_link;
		$this->content->text .= "</div><p onClick=\"ga(['_trackEvent','OUE','clicked','Terms and Conditions link']);\" id=\"terms_conditions_draw\"><a href=\"http://www.bath.ac.uk/students/student-feedback/unit-evaluation/index.html\" target=\"_blank\" >* By completing your Unit Evaluations you help us become Better@Bath &amp; you are entered automatically into a prize draw for &pound;500</a></p>";
        return $this->content;
    }
    public function has_config()
    {
        return true;
    }
}

