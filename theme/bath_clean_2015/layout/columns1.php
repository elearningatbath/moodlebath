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

/**
 * The one column layout.
 *
 * @package   theme_bath_clean_2015
 * @copyright 2013 Moodle, moodle.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Get the HTML for the settings bits.
$html = theme_bath_clean_2015_get_html_for_settings($OUTPUT, $PAGE);

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

<header role="banner" class="navbar navbar-fixed-top<?php echo $html->navbarclass ?> moodle-has-zindex">
    <nav role="navigation" class="navbar-inner">
        <div class="container-fluid">
            <a class="brand" href="<?php echo $CFG->wwwroot;?>"><?php echo
                format_string($SITE->shortname, true, array('context' => context_course::instance(SITEID)));
                ?></a>
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <?php echo $OUTPUT->user_menu(); ?>
            <div class="nav-collapse collapse">
                <?php echo $OUTPUT->custom_menu(); ?>
                <ul class="nav pull-right">
                    <li><?php echo $OUTPUT->page_heading_menu(); ?></li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<div id="page" class="container-fluid">

    <header id="page-header" class="clearfix">
        <?php echo $html->heading; ?>
        <div id="page-navbar" class="clearfix">
            <nav class="breadcrumb-nav"><?php echo $OUTPUT->navbar(); ?></nav>
            <div class="breadcrumb-button"><?php echo $OUTPUT->page_heading_button(); ?></div>
        </div>
        <div id="course-header">
            <?php echo $OUTPUT->course_header(); ?>
        </div>
    </header>

    <div id="page-content" class="row-fluid">
        <section id="region-main" class="span12">
            <?php
            echo $OUTPUT->course_content_header();
            echo $OUTPUT->main_content();
            echo $OUTPUT->course_content_footer();
            ?>
        </section>
    </div>

    <!-- <footer id="page-footer">
        <div id="course-footer"><?php echo $OUTPUT->course_footer(); ?></div>
        <p class="helplink"><?php echo $OUTPUT->page_doc_link(); ?></p>
        <?php
        echo $html->footnote;
        echo $OUTPUT->login_info();
        echo $OUTPUT->home_link();
        echo $OUTPUT->standard_footer_html();
        ?>
    </footer>-->
                              <footer>

                <div class="row">
                    <div class="small-6 medium-4 large-3 column footerAddress" id="footerAddress">
                        <ul class="footerContactLinks">
                            <li><a href="http://www.bath.ac.uk/about/contact-us/">Contact us</a></li>
                            <li><a href="http://www.bath.ac.uk/travel-advice/">Getting here</a></li>
                        </ul>
                        <div class="findPeople">
                            <form action="http://www.bath.ac.uk/contact/" method="get" name="findpeople" id="findpeople" onsubmit="return (this.q.value != '' &amp;&amp; this.q.value != 'search')">
                                <label for="findpeopleinput">Person finder</label>
                                <input type="text" name="pgeneralsearch" class="findPeopleInputBox" id="findpeopleinput" onfocus="if (this.value == 'Find people...') {this.value=''}" onblur="if (this.value == '') {this.value='Find people...'}" value="Find people...">
                                <input type="image" class="findPeopleSubmit" name="submit" id="findpeoplego" src="/common/images/style/findPeople_go.png" alt="go">
                                <input type="hidden" value="basic" name="search">
                            </form>
                        </div><!-- /findPeople -->
                    </div>
                    <div class="hide-for-small medium-4 large-6 column footerAwards" id="footerAwardContainer">
                        <a href="http://go.bath.ac.uk/award1">
                            <!--[if gte IE 9]><!--><img src="/common/svg/nss.svg" alt="We're joint 1st for student satisfaction in the 2014 National Student Survey"><!--<![endif]-->
                            <!--[if lt IE 9]><img src="/common/images/style/nss.png" alt="We're joint 1st for student satisfaction in the 2014 National Student Survey"><![endif]-->
                        </a>
                        <a href="http://go.bath.ac.uk/award2">
                            <!--[if gte IE 9]><!--><img src="/common/svg/the-award.svg" alt="The Times Higher Education supplement - first for student experience 2015"><!--<![endif]-->
                            <!--[if lt IE 9]><img src="/common/images/style/the-award.png" alt="The Times Higher Education supplement - first for student experience 2015" /><![endif]-->
                        </a>
                    </div>
                    <div class="small-6 medium-4 large-3 column footerExplore" id="footerExplore">
                        <a title="More destination options" href="http://www.bath.ac.uk/index/" id="footerExploreLink" class="button tiny round footerExploreLink">Explore the University</a>
                        <div class="footerSocialNetworks">
                                <a href="https://www.youtube.com/user/UniofBath">
                                    <!--[if gte IE 9]><!--><img alt="You Tube logo" src="http://bath.ac.uk/common/svg/youtube.svg"><!--<![endif]-->
                                    <!--[if lt IE 9]><img alt="You Tube logo" src="http://bath.ac.uk/common/images/logos/youtube.png" /><![endif]-->
                                </a>
                                <a href="http://www.facebook.com/uniofbath">
                                    <!--[if gte IE 9]><!--><img alt="Facebook logo" src="http://bath.ac.uk/common/svg/facebook.svg" class="facebook-logo"><!--<![endif]-->
                                    <!--[if lt IE 9]><img alt="Facebook logo" src="http://bath.ac.uk/common/images/logos/facebook.png" class="facebook-logo" /><![endif]-->
                                </a>
                                <a href="http://twitter.com/UniOfBath">
                                    <!--[if gte IE 9]><!--><img alt="Twitter logo" src="http://bath.ac.uk/common/svg/twitter.svg" class="twitter-logo"><!--<![endif]-->
                                    <!--[if lt IE 9]><img alt="Twitter logo" src="http://bath.ac.uk/common/images/logos/twitter.png" class="twitter-logo" /><![endif]-->
                                </a>
                        </div><!-- /socialNetworks -->
                        <p>© 2015 University of Bath</p>

                    </div>

                </div>

            </footer>

    <?php echo $OUTPUT->standard_end_of_body_html() ?>

</div>
</body>
</html>
