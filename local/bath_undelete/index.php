<?php
require_once(dirname(__FILE__) . '/../../config.php');
require_once("$CFG->libdir/formslib.php");
require_once(dirname(__FILE__) . '/lib.php');
$confirm = optional_param('confirm', false, PARAM_BOOL);
$username = optional_param('username',false,PARAM_RAW);
$PAGE->set_url('/local/bath_undelete/index.php');
$pluginheading = get_string('bath_undelete', 'local_bath_undelete');
$PAGE->set_heading($pluginheading);
$PAGE->set_title($pluginheading);
$returnurl = '/local/bath_undelete/index.php';
if (!$context = context_system::instance() || !is_siteadmin()) {
    print_error('nocontext');
}
require_login($context);
echo $OUTPUT->header();


if ($confirm && confirm_sesskey()) {
    //Restore the user
    restore_user($username);
}
if(isset($_GET['action'])){
    if($_GET['action'] == 'restore'){
        $yesurl = new moodle_url($PAGE->url, array('username'=>$_GET['username'],'confirm'=>1, 'sesskey'=>sesskey()));
        echo $OUTPUT->confirm('Are you sure you want to restore username:"'.$_GET['username']."\"", $yesurl, $returnurl);
        echo $OUTPUT->footer();
        die();
    }
}
class undelete_users_form extends moodleform{

    public function definition(){

        global $CFG;
        $mform = $this->_form; // Don't forget the underscore!
        $mform->addElement('text', 'user','Enter username:'); // Add elements to your form
        $mform->setType('user',PARAM_RAW);
        $this->add_action_buttons(false,'Search');
    }

}
$mform = new undelete_users_form();
$mform->display();
if (!$mform->is_cancelled()) {

    if($fromform = $mform->get_data()){
        //print_r($fromform);
        $username = $fromform->user;
        $user = get_hidden_user_details($username);
        if($user){
            $firstname = $user->firstname;
            $lastname = $user->lastname;
            $deleted = $user->deleted;
            $isdeleted = ($user->deleted == 1 ? true : false);
            $deleteword = ($deleted == 1 ? 'Yes' : 'No');
            $table = new html_table();
            $table->head = array('First Name','Last Name','Deleted ?','Action');
            $restorelink = '';
            if($isdeleted){
                $restorelink = "<button type=\"button\" class=\"btn btn-link\"><a href='index.php?action=restore&username=$username'>Restore</a></button>";
            }
            $table->data = array(array($firstname,$lastname,$deleteword,$restorelink));
            echo html_writer::table($table);
        }
        else{
            echo html_writer::start_span('error').get_string('usernotfound','local_bath_undelete').html_writer::end_span();
        }
    }
}
echo $OUTPUT->footer();