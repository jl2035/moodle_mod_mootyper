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
 * This is a one-line short description of the file
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod
 * @subpackage mootyper
 * @copyright  2012 Jaka Luthar (jaka.luthar@gmail.com)
 * @copyright  2016 onwards AL Rachels (drachels@drachels.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


//require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
//require_once(dirname(__FILE__).'/lib.php');
global $USER;
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or

if ($id) {
    //$cm         = get_coursemodule_from_id('mootyper', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
}
else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true);
$context = context_course::instance($id);

//add_to_log($course->id, 'mootyper', 'view', "view.php?id={$cm->id}", $mootyper->name, $cm->id);

/// Print the page header

$PAGE->set_url('/mod/mootyper/exercises.php', array('id' => $course->id));
$PAGE->set_title(get_string('etitle', 'mootyper'));
$PAGE->set_heading(get_string('eheading', 'mootyper'));
//$PAGE->set_context($context);

// other things you may want to set - remove if not needed
$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('tb1');
//$PAGE->add_body_class('mootyper-'.$somevar);

// Output starts here
echo $OUTPUT->header();
require_once(dirname(__FILE__).'/locallib.php');

$lessonPO = optional_param('lesson', 0, PARAM_INT);
$jlnk2 = $CFG->wwwroot . '/mod/mootyper/eins.php?id='.$id;
echo '<a href="'.$jlnk2.'">'.get_string('eaddnew', 'mootyper').'</a><br><br>';
//$lessons = get_typerlessons();
/*if(has_capability('mod/mootyper:editall', context_module::instance($id)))
	$lessons = get_typerlessons();
else */
$lessons = get_mootyperlessons($USER->id, $id);
	
if($lessonPO == 0 && count($lessons) > 0)
	$lessonPO = $lessons[0]['id'];
echo '<form method="post">';
echo get_string('excategory', 'mootyper').': <select onchange="this.form.submit()" name="lesson">';
$selected_lesson_index = 0;
for($ij=0; $ij<count($lessons); $ij++)
{
	if($lessons[$ij]['id'] == $lessonPO)
	{
		echo '<option selected="true" value="'.$lessons[$ij]['id'].'">'.$lessons[$ij]['lessonname'].'</option>';
		$selected_lesson_index = $ij;
	}
	else
		echo '<option value="'.$lessons[$ij]['id'].'">'.$lessons[$ij]['lessonname'].'</option>';
}
echo '</select>';
if(is_editable_by_me($USER->id, $lessonPO))
	echo ' <a onclick="return confirm(\''.get_string('removeconfirm', 'mootyper').$lessons[$selected_lesson_index]['lessonname'].
    '\')" href="erem.php?id='.$course->id.'&l='.$lessons[$selected_lesson_index]['id'].'">'.
	get_string('removeall','mootyper').'\''.$lessons[$selected_lesson_index]['lessonname'].'\'</a>';
echo '</form><br>';
echo '<table style="border: solid;"><tr><td>'.get_string('ename','mootyper').'</td><td>'.get_string('etext', 'mootyper').'</td><td></td></tr>';
$exercises = get_typerexercisesfull($lessonPO);
foreach($exercises as $ex)
{
	$strToCut = $ex['texttotype'];
	$strToCut = str_replace('\n', '<br>', $strToCut);
	if(strlen($strToCut) > 65)
		$strToCut = substr($strToCut, 0, 65).'...';
	//$jWebDir = $CFG->wwwroot . '/course/view.php?id='.$_POST['rpCourseId'];
	$jlink =   '<a href="erem.php?id='.$course->id.'&r='.$ex['id'].'">'.get_string('eremove', 'mootyper').'</a>';
	$jlink2 = '<a href="eedit.php?id='.$course->id.'&ex='.$ex['id'].'">'.get_string('eeditlabel', 'mootyper').'</a>';
	echo '<tr style="border-top: solid;"><td>'.$ex['exercisename'].'</td><td>'.$strToCut.'</td>';
	if(is_editable_by_me($USER->id, $lessonPO))
		echo '<td>'.$jlink2.' | '.$jlink.'</td>';
	echo '</tr>';
}
echo '</table>';

// Trigger module exercise_edited event.
$event = \mod_mootyper\event\course_exercises_viewed::create(array(
	'objectid' => $course->id,
	'context' => $context
));
$event->trigger();

echo $OUTPUT->footer();
