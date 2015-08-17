<?php
$hassidepre = $PAGE->blocks->region_has_content('side-pre', $OUTPUT);
$hassidepost = $PAGE->blocks->region_has_content('side-post', $OUTPUT);
$hasfooter = (empty($PAGE->layout_options['nofooter']));
$hasfootnote = (!empty($PAGE->theme->settings->footnote));
echo $OUTPUT->doctype(); ?>
<html <?php echo $OUTPUT->htmlattributes() ?>>
<head>
    <title>Uob >>> <?php echo $PAGE->title ?></title>
    <?php echo $OUTPUT->standard_head_html() ?>
</head>
<body id="<?php p($PAGE->bodyid); ?>" class="<?php p($PAGE->bodyclasses); ?>">
<?php echo $OUTPUT->standard_top_of_body_html() ?>
<div id="page">
<?php if ($PAGE->heading || (empty($PAGE->layout_options['nonavbar']) && $PAGE->has_navbar())) { ?>
    <div id="page-header">
        <?php if ($PAGE->heading) { ?>
        	         <div id="logo"></div>
            <h1 class="headermain"><?php echo $PAGE->heading ?></h1>
            <div class="headermenu"><?php
                echo $OUTPUT->user_menu();
                if (!empty($PAGE->layout_options['langmenu'])) {
                    echo $OUTPUT->lang_menu();
                }
                echo $PAGE->headingmenu
            ?></div>
        <?php } ?>
        <?php if (empty($PAGE->layout_options['nonavbar']) && $PAGE->has_navbar()) { ?>
            <div class="navbar clearfix">
                <div class="breadcrumb"><?php echo $OUTPUT->navbar(); ?></div>
                <div class="navbutton"> <?php echo $PAGE->button; ?></div>
            </div>
        <?php } ?>
    </div>
<?php } ?>

<!-- END OF CUSTOMMENU AND NAVBAR -->
    <div id="page-content">
       <div id="region-main-box">
           <div id="region-post-box">
              <div id="region-main-wrap">
                 <div id="region-main-pad">
                   <div id="region-main">
                     <div class="region-content">
                            <?php echo $OUTPUT->main_content() ?>
                     </div>
                   </div>
                 </div>
               </div>

                <?php if ($hassidepre) { ?>
                <div id="region-pre" class="block-region">
                   <div class="region-content">
                        <?php echo $OUTPUT->blocks_for_region('side-pre') ?>
                   </div>
                </div>
                <?php } ?>

                <?php if ($hassidepost) { ?>
                <div id="region-post" class="block-region">
                   <div class="region-content">
                        <?php echo $OUTPUT->blocks_for_region('side-post') ?>
                   </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- START OF FOOTER -->
    <?php if ($hasfooter) { ?>
    <div id="page-footer" class="clearfix">

        <div class="footer-left">

            <?php if ($hasfootnote) { ?>
                    <div id="footnote"><?php echo $PAGE->theme->settings->footnote;?></div>
            <?php } ?>

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
            <?php echo $OUTPUT->login_info();?>
        </div>

        <?php echo $OUTPUT->standard_footer_html(); ?>
    </div>
    <?php } ?>
    <div class="clearfix"></div>
</div>
</div>
<?php echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>