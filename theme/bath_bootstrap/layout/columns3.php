<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
$html = theme_bath_bootstrap_get_html_for_settings($OUTPUT, $PAGE);
if (right_to_left()) {
    $regionbsid = 'region-bs-main-and-post';
} else {
    $regionbsid = 'region-bs-main-and-pre';
}

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>" />
    <?php echo $OUTPUT->standard_head_html() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>
<div id="page-wrapper">
<div id="page" class="container-fluid">
<div class="headermenu">
    	                
                    <?php echo $OUTPUT->page_heading_menu(); ?>
                    <?php echo $OUTPUT->user_menu(); ?>

               </div>
        <header id="page-header" class="clearfix">
    	<?php //echo $html->heading; ?>
    	<?php echo html_writer::link($CFG->wwwroot, '', array('title' => get_string('home'), 'class' => 'logo'));?>		
        <?php echo $OUTPUT->page_heading(); ?>
        <div id="course-header">
            <?php echo $OUTPUT->course_header(); ?>
        </div>
    </header>
            		<header role="banner" class="navbar navbar-default<?php echo $html->navbarclass ?> ">
    <nav role="navigation" class="navbar-inner">
        <div class="container-fluid">
            <a class="brand" href="<?php echo $CFG->wwwroot;?>"><?php echo $SITE->shortname; ?></a>
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <div class="nav-collapse collapse">
                <?php echo $OUTPUT->custom_menu(); ?>
            </div>
        </div>
    </nav>
</header>
        <div id="page-navbar" class="clearfix">
            <div class="breadcrumb-nav"><?php echo $OUTPUT->navbar(); ?></div>
            <nav class="breadcrumb-button"><?php echo $OUTPUT->page_heading_button(); ?></nav>
        </div>
<!-- Bath Alert message when needed-->
<?php
        if(!empty($PAGE->theme->settings->alertmessage)){
?>
        <div id="bath-alert"><span><?php echo $PAGE->theme->settings->alertmessage;?></span></div>
<?php           } ?>

    <div id="page-content" class="row-fluid">
        <div id="<?php echo $regionbsid ?>" class="span9">
            <div class="row-fluid">
                <section id="region-main" class="span8 pull-right">
                    <?php
                    echo $OUTPUT->course_content_header();
                    echo $OUTPUT->main_content();
                    echo $OUTPUT->course_content_footer();
                    ?>
                </section>
               
                <?php echo $OUTPUT->blocks('side-pre', 'span4 desktop-first-column'); ?>
                
            </div>
        </div>
        
        <?php echo $OUTPUT->blocks('side-post', 'span3'); ?>
        
    </div> <!--End of page-content-->

       <footer id="page-footer">
        <div id="course-footer"><?php echo $OUTPUT->course_footer(); ?></div>
        <p class="helplink"><?php echo $OUTPUT->page_doc_link(); ?></p>
        <div class="footer-left">

             
                    <div id="footnote"><?php echo $html->footnote;?></div>
            <a href="http://moodle.org" title="Moodle">
                <img src="<?php echo $OUTPUT->pix_url('footer/moodle-logo','theme')?>" alt="Moodle logo" />
            </a>
        </div>
                <div id="credits">
	<p><a href="http://www.bath.ac.uk/learningandteaching/">Learning and Teaching Enhancement Office</a>, University of Bath, Bath, BA2 7AY, UK &middot; tel 01225 388388
	<br>
		<a href="http://www.bath.ac.uk/web/copyright/">&copy;</a> <?php echo(date('Y')); ?> &middot;
		<a href="http://www.bath.ac.uk/web/disclaimer/">disclaimer</a> &middot;
		<a href="http://www.bath.ac.uk/web/privacy.html">privacy statement</a> &middot;
		<a href="http://www.bath.ac.uk/foi/" title="Freedom of Information">FoI</a>
	<br>Maintained by the <a href="mailto:e-learning@bath.ac.uk">e-Learning Team</a>.</p>
<?php 
/*$device_type =  get_device_type(); 
if ($device_type == 'tablet'){
echo 'Using a tablet? Click below to try the experimental<br> <a href="?theme=mymobile">theme for mobile devices</a>';
}
*/
?>
</div>
	<div class="footer-right">
        <?php
        echo $OUTPUT->login_info();
        //echo $OUTPUT->home_link();
        ?>
	</div>
<?php echo $OUTPUT->standard_footer_html();?>
    </footer>

    <?php echo $OUTPUT->standard_end_of_body_html() ?>

	</div>
</div><!--End of page-wrapper-->
</body>
</html>
