<?php
/**
 * @package    blocks
 * @subpackage sits
 * @copyright  2011 University of Bath
 * @author     Alex Lydiate {@link http://alexlydiate.co.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');

require_once($CFG->dirroot . '/group/lib.php');
require_once($CFG->dirroot . '/lib/grouplib.php');
require_once($CFG->dirroot . '/enrol/sits/lib.php');  //This used to require samis_management...not no more!

require_login();
if (isguestuser()) {
	print_error('guestsarenotallowed', '', $returnurl); //FIXME need more security than this
}

//Add BUCS Users to Moodle
//$PAGE->set_url('/blocks/sits/view.php', array('courseid'=>$course->id));
//$PAGE->requires->js('/enrol/sits/js/sits_block.js', true); //commented for debug, stuck in manually, plays better with Firebug
$PAGE->set_pagelayout('popup');
$PAGE->requires->css('/blocks/sits/gui/css/samis_user_interface.css');
$PAGE->set_url('/blocks/sits/gui/samis_add_user_interface.php',array('courseid'=> 1));
$plugin = enrol_get_plugin('sits');
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_title(get_string('manage_mappings', 'enrol_sits'));
$PAGE->set_heading('Add Bath users to Moodle'); //Set to a space in order to display the logo in 'popup' layout. al412.

echo $OUTPUT->header();?>

        <div id = "container">
            <div id = "period_container" class="admin_box">
                <input type="text" name="courseid" style="display: none;" /> 
                <b>Bath username of person to be added: </b><input type="text" name="bucsname"
                	id="bucs_id_input" value="" maxlength="12" size="12" /> 
                <input type="submit" value="Add User" id="useradd_sub"
                	onclick="sits_block.add_user()" />
                <div class = "admin_instruction">
                <p>This form can be used to add a Bath username to Moodle. <a target = "_blank" href="http://www.bath.ac.uk/bucs/tools/waaa/">Web Application Access Accounts (WAAA)</a> are not held in SAMIS and therefore cannot be validated. <br/>
                Therefore, please ensure such usernames are correct before adding them</p>
                </div>  
            </div>
        </div>
    <!-- JS - to be called into <head> after dev - plays better with Firebug like this-->
<!-- YUI Base Dependency -->
<script src="./js/yui/yahoo-min.js"
    type="text/javascript"></script>
<!-- YUI Used for Custom Events and event listener bindings -->
<script src="./js/yui/event-min.js"
    type="text/javascript"></script>
<!-- YUI AJAX connection -->
<script
    src="./js/yui/connection-min.js"
    type="text/javascript"></script>
<!-- YUI DOM Source file -->
<script src="./js/yui/dom-min.js"></script>
<!-- YUI Element, depends on DOM -->
<script
    src="./js/yui/element-min.js"></script>
<script src="./js/sits_block.js" type="text/javascript"></script>
<!--  end JS -->
<?php echo $OUTPUT->footer(); ?>
