<?php
/**
 * Library proxy link filter.
 *
 * @package    filter
 * @subpackage libproxylinks
 * @copyright  2012 James Barrett
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This class looks for links in Moodle text and
 * uses the library proxy to allow access to  IP
 * filtered journals.
 */
class filter_libproxylinks extends moodle_text_filter {
	public function filter($text, array $options = array()) {
		if (!stripos($text, 'href')) {
			return $text;
		}
		$newtext = $this->links($text);
		return $newtext;
	}

	protected function links(&$text) {
	   global $CFG;
	   $urlpattern = "/<a[^>]+href=\"([^\"]+)/i";
	   $libproxylink = get_config('filter_libproxylinks','proxylink');
	   preg_match_all($urlpattern, $text, $matches);
	   $newtext = $text;
	   foreach( $matches[1] as $link) {
		if (stripos($link, $CFG->wwwroot) !== false || stripos($link, 'bath.ac.uk') !== false) {
		    // This link is a moodle or a bath link, don't change it.
		    $newlink = $link;
		} else if (stripos($link, "http") !== false) {
			//If the subject itself is a link, then preserve the old text but point to the new link
		    $newlink = clean_param($libproxylink."".urlencode(html_entity_decode($link)),PARAM_URL);
		} else {
		    $newlink = $link;
		}
		$newtext = str_replace($link, $newlink, $newtext);
		//$newtext = "<a href=\"$newlink\">$link</a>";
	   }
	   return $newtext;
	}
}