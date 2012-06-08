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
 *
 * @package    mod
 * @subpackage mootyper
 * @copyright  2011 Jaka Luthar (jaka.luthar@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


//require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
//require_once(dirname(__FILE__).'/lib.php');
global $USER;
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or

if ($id) {
    //$cm         = get_coursemodule_from_id('mootyper', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
}
else {
    error('You must specify a course_module ID or an instance ID');
}
require_login($course, true);
$lessonPO = optional_param('lesson', -1, PARAM_INT);
if(isset($_POST['button']))
   $param1 = $_POST['button']; 
if(isset($param1) && get_string('fconfirm', 'mootyper') == $param1 )
  //DB insert
{
	global $DB;
	//$lessonPO = optional_param('lesson', -1, PARAM_INT);
	$texttotypeePO = $_POST['texttotype'];
	//$enamePO = $_POST['exercisename'];
	if($lessonPO == -1)
	{
		$lsnnamePO = $_POST['lessonname'];
		$lsnrecord = new stdClass();
		$lsnrecord->lessonname = $lsnnamePO;
		$lesson_id = $DB->insert_record('mootyper_lessons', $lsnrecord, true);
	}
	else
		$lesson_id = $lessonPO;
	$snum = get_new_snumber($lesson_id);
	$erecord->exercisename = "".$snum;
	$erecord->snumber = $snum;
	$erecord->lesson = $lesson_id;
	$erecord->texttotype = str_replace("\r\n", '\n', $texttotypeePO);
	$DB->insert_record('mootyper_exercises', $erecord, false);
	$webDir = $CFG->wwwroot . '/mod/mootyper/exercises.php?id='.$id;
	//header('Location: '.$webDir);
	echo '<script type="text/javascript">window.location="'.$webDir.'";</script>';
}
//$context = get_context_instance(CONTEXT_MODULE, $cm->id);

//add_to_log($course->id, 'mootyper', 'view', "view.php?id={$cm->id}", $mootyper->name, $cm->id);

/// Print the page header

$PAGE->set_url('/mod/mootyper/eins.php', array('id' => $course->id));
$PAGE->set_title(get_string('etitle', 'mootyper'));
$PAGE->set_heading(get_string('eheading', 'mootyper'));
//$PAGE->set_context($context);

// other things you may want to set - remove if not needed
$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('tb1');
//$PAGE->add_body_class('mootyper-'.$somevar);

// Output starts here
echo $OUTPUT->header();
// action="?id='.$id.'&ins=true"
echo '<form method="POST">';
$lessons = get_typerlessons();
echo get_string('fnewexercise', 'mootyper').'&nbsp;';
echo '<select onchange="this.form.submit()" name="lesson">';
echo '<option value="-1">'.get_string('fnewlesson', 'mootyper').'</option>';
for($ij=0; $ij<count($lessons); $ij++)
{
	if($lessons[$ij]['id'] == $lessonPO)
		echo '<option selected="true" value="'.$lessons[$ij]['id'].'">'.$lessons[$ij]['lessonname'].'</option>';
	else
		echo '<option value="'.$lessons[$ij]['id'].'">'.$lessons[$ij]['lessonname'].'</option>';
}
echo '</select>';
if($lessonPO == -1)
	echo '<br><br>...'.get_string('lsnname', 'mootyper').': <input type="text" name="lessonname">';
//echo '<br><br>'.get_string('ename', 'mootyper').'<input type="text" name="exercisename">';
echo '<br><br>'.get_string('fexercise', 'mootyper').':<br>'.
	 '<textarea name="texttotype"></textarea><br>'.
	 '<br><input name="button" type="submit" value="'.get_string('fconfirm', 'mootyper').'">'.
     '</form>';

echo $OUTPUT->footer();
