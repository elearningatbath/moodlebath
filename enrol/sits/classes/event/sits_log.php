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

namespace enrol_sits\event;
defined('MOODLE_INTERNAL') || die();

class sits_log extends \core\event\base
{
    public static $message ;
    protected function init() {
        $this->data['crud'] = 'c'; // c(reate), r(ead), u(pdate), d(elete)
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'SITS';
    }
    public static function create_from_log($data){
        self::$message = $data['message'];
    }
    public static function get_name() {
        return get_string('eventsits_log', 'enrol_sits');
    }
    public function get_description() {
        //return "The user with id {$this->userid} created ... ... ... with id {$this->objectid}.";
        return self::$message;
    }

    public function get_legacy_logdata() {
        // Override if you are migrating an add_to_log() call.

    }
    public static function get_legacy_eventname() {
        // Override ONLY if you are migrating events_trigger() call.
        return 'SITS';
    }

    protected function get_legacy_eventdata() {
        // Override if you migrating events_trigger() call.
        $data = new \stdClass();
        $data->id = $this->objectid;
        $data->userid = $this->relateduserid;
        return $data;
    }

}