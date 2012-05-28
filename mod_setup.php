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
 * Prints a particular instance of sityper setup
 *
 * @package    mod
 * @subpackage sityper
 * @copyright  2012 Jaka Luthar (jaka.luthar@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


global $USER;
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // sityper instance ID - it should be named as the first character of the module

if(isset($_POST['button']))
$param1 = $_POST['button'];
if(isset($param1) && get_string('fconfirm', 'sityper') == $param1)
{
	$modePO = optional_param('mode', null, PARAM_INT);
	$lessonPO = optional_param('lesson', null, PARAM_INT);
    $goalPO = optional_param('requiredgoal', null, PARAM_INT);
	global $DB, $CFG;
	$sityper  = $DB->get_record('sityper', array('id' => $n), '*', MUST_EXIST);
	$sityper->lesson = $lessonPO;
	$sityper->isexam = $modePO;
	$sityper->requiredgoal = $goalPO;
	if($modePO == 1){
		$exercisePO = optional_param('exercise', null, PARAM_INT);
		$sityper->exercise = $exercisePO;
	}
	$DB->update_record('sityper', $sityper);
	header('Location: '.$CFG->wwwroot.'/mod/sityper/view.php?n='.$n);
}

$modePO = optional_param('mode', null, PARAM_INT);
$lessonPO = optional_param('lesson', null, PARAM_INT);

if ($id) {
    $cm         = get_coursemodule_from_id('sityper', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $sityper  = $DB->get_record('sityper', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $sityper  = $DB->get_record('sityper', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $sityper->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('sityper', $sityper->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

//add_to_log($course->id, 'sityper', 'view', "view.php?id={$cm->id}", $sityper->name, $cm->id);

/// Print the page header

$PAGE->set_url('/mod/sityper/mod_setup.php', array('id' => $cm->id));
$PAGE->set_title(format_string($sityper->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// other things you may want to set - remove if not needed
$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('tb1');
//$PAGE->add_body_class('sityper-'.$somevar);
// Output starts here
echo $OUTPUT->header();
// Replace the following lines with you own code
echo $OUTPUT->heading($sityper->name);
//get_record('sityper_exercises', array('id' => $eid));
//$sityper = jget_sityper_record($n);
//$exercise_ID = $sityper->exercise;
//$exercise = get_exercise_record($exercise_ID);
//$textToEnter = $exercise->texttotype; //"N=".$n." exercise_ID=".$sityper->exercise." fjajfjfjfj name=".$sityper->name." fjfjfjfjfj";

//onload="initTextToEnter('')"

//$grds = get_typergradesfull($_GET['sid']);
$htmlout = '';
$htmlout .= '<form id="setupform" name="setupform" method="POST">';
$htmlout .= '<table><tr><td>'.get_string('fmode', 'sityper').'</td><td><select onchange="this.form.submit()" name="mode">';
$lessons = get_typerlessons();
if($modePO == 0 || is_null($modePO))
{
	$htmlout .= '<option selected="true" value="0">'.
            get_string('sflesson', 'sityper').'</option><option value="1">'.
            get_string('isexamtext', 'sityper').'</option>';
    $htmlout .= '</select></td></tr><tr><td>';
    $htmlout .= get_string('flesson', 'sityper').'</td><td><select onchange="this.form.submit()" name="lesson">';
    for($ij=0; $ij<count($lessons); $ij++)
    {
		if($lessons[$ij]['id'] == $lessonPO)
			$htmlout .= '<option selected="true" value="'.$lessons[$ij]['id'].'">'.$lessons[$ij]['lessonname'].'</option>';
		else
			$htmlout .= '<option value="'.$lessons[$ij]['id'].'">'.$lessons[$ij]['lessonname'].'</option>';
	}
    $htmlout .= '</select></td></tr><tr><td>'.get_string('requiredgoal', 'sityper').'</td><td><input style="width: 20px;" type="text" name="requiredgoal"> % </td></tr></table>';
}
else if($modePO == 1)
{
	$htmlout .= '<option value="0">'.
            get_string('sflesson', 'sityper').'</option><option value="1" selected="true">'.
            get_string('isexamtext', 'sityper').'</option>';
    $htmlout .= '</select></td></tr><tr><td>';
    $htmlout .= get_string('flesson', 'sityper').'</td><td><select onchange="this.form.submit()" name="lesson">';
    for($ij=0; $ij<count($lessons); $ij++)
    {
		if($lessons[$ij]['id'] == $lessonPO)
			$htmlout .= '<option selected="true" value="'.$lessons[$ij]['id'].'">'.$lessons[$ij]['lessonname'].'</option>';
		else
			$htmlout .= '<option value="'.$lessons[$ij]['id'].'">'.$lessons[$ij]['lessonname'].'</option>';
	}
    $htmlout .= '</select></td></tr>';
    $exercises = get_exercises_by_lesson($lessonPO);
    $htmlout .= '<tr><td>'.get_string('fexercise', 'sityper').'</td><td><select name="exercise">';
    for($ik=0; $ik<count($exercises); $ik++)
    {
		$htmlout .= '<option value="'.$exercises[$ik]['id'].'">'.$exercises[$ik]['exercisename'].'</option>';
	}
    $htmlout .= '</select></td></tr></table>';
}
$htmlout .= '<br><input name="button" value="'.get_string('fconfirm', 'sityper').'" type="submit">';
$htmlout .= '</form>';
echo $htmlout;
// Finish the page
echo $OUTPUT->footer();


