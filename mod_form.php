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
 * The main mootyper configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod
 * @subpackage mootyper
 * @copyright  2012 Jaka Luthar (jaka.luthar@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/mootyper/locallib.php');
/**
 * Module instance settings form
 */
class mod_mootyper_mod_form extends moodleform_mod {
    public function definition() {
        $mform = $this->_form;
        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('mootypername', 'mootyper'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags))
            $mform->setType('name', PARAM_TEXT);
        else
            $mform->setType('name', PARAM_CLEAN);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'mootypername', 'mootyper');
        $this->add_intro_editor();
		global $CFG, $COURSE;
        $mform->addElement('date_time_selector', 'timeopen', get_string('mootyperopentime', 'mootyper'), array('optional' => true, 'step' => 1));
        $mform->addElement('date_time_selector', 'timeclose', get_string('mootyperclosetime', 'mootyper'), array('optional' => true, 'step' => 1));
        //$mform->addElement('passwordunmask', 'password', get_string('requirepassword', 'mootyper'));
        $mform->addElement('header', 'mootyperz', get_string('pluginadministration', 'mootyper'));
        $jlnk3 = $CFG->wwwroot . '/mod/mootyper/exercises.php?id='.$COURSE->id;
        $mform->addElement('html', '<a id="jlnk3" href="'.$jlnk3.'">'.get_string('emanage', 'mootyper').'</a>');
        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }
}
