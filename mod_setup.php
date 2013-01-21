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
 * Prints a particular instance of mootyper setup
 *
 * @package    mod
 * @subpackage mootyper
 * @copyright  2012 Jaka Luthar (jaka.luthar@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


global $USER;
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // mootyper instance ID - it should be named as the first character of the module

if ($id) {
    $cm         = get_coursemodule_from_id('mootyper', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $mootyper  = $DB->get_record('mootyper', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $mootyper  = $DB->get_record('mootyper', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $mootyper->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('mootyper', $mootyper->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

$mooCFG = get_config('mootyper');
$ePO = optional_param('e', 0, PARAM_INT);
$modePO = optional_param('mode', $mootyper->isexam, PARAM_INT);
$exercisePO = optional_param('exercise', $mootyper->exercise, PARAM_INT);
$lessonPO = optional_param('lesson', $mootyper->lesson, PARAM_INT);
$showKeyboardPO = optional_param('showkeyboard', "off", PARAM_CLEAN);
if(empty($_POST))
	$showKeyboardPO = $mootyper->showkeyboard == 1 ? "on" : "off";
if($mootyper->layout == NULL || is_null($mootyper->layout))
	$dfLy = $mooCFG->defaultlayout;
else
	$dfLy = $mootyper->layout;
$layoutPO = optional_param('layout', $dfLy, PARAM_INT);
if($mootyper->requiredgoal == NULL || is_null($mootyper->requiredgoal))
	$dfGoal =  $mooCFG->defaultprecision;
else
	$dfGoal = $mootyper->requiredgoal;
$goalPO = optional_param('requiredgoal', $dfGoal, PARAM_INT);
require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

if(isset($_POST['button']))
$param1 = $_POST['button'];
if(isset($param1) && get_string('fconfirm', 'mootyper') == $param1)
{
	$modePO = optional_param('mode', null, PARAM_INT);
	$lessonPO = optional_param('lesson', null, PARAM_INT);
	//$mooCFG = get_config('mootyper');
    //$defLayout = $mooCFG->defaultlayout;
    
    $goalPO = optional_param('requiredgoal', $mooCFG->defaultprecision, PARAM_INT);
    if($goalPO == 0) $goalPO = $mooCFG->defaultprecision;
    $layoutPO = optional_param('layout', 0, PARAM_INT);
    
    $showKeyboardPO = optional_param('showkeyboard', null, PARAM_CLEAN);
	global $DB, $CFG;
	$mootyper  = $DB->get_record('mootyper', array('id' => $n), '*', MUST_EXIST);
	$mootyper->lesson = $lessonPO;
	$mootyper->showkeyboard = $showKeyboardPO == 'on';
	$mootyper->layout = $layoutPO;
	$mootyper->isexam = $modePO;
	$mootyper->requiredgoal = $goalPO;
	if($modePO == 1){
		$exercisePO = optional_param('exercise', null, PARAM_INT);
		$mootyper->exercise = $exercisePO;
	}
	$DB->update_record('mootyper', $mootyper);
	header('Location: '.$CFG->wwwroot.'/mod/mootyper/view.php?n='.$n);
}



//add_to_log($course->id, 'mootyper', 'view', "view.php?id={$cm->id}", $mootyper->name, $cm->id);

/// Print the page header

$PAGE->set_url('/mod/mootyper/mod_setup.php', array('id' => $cm->id));
$PAGE->set_title(format_string($mootyper->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->set_cacheable(false);
















echo $OUTPUT->header();
echo $OUTPUT->heading($mootyper->name);
$htmlout = '';
$htmlout .= '<script type="text/javascript">
function removeAtts()
{
	document.getElementById("lesson").disabled = false;
	document.getElementById("mode").disabled = false;	
	document.getElementById("exercise").disabled = false;
}
</script>';
$htmlout .= '<form id="setupform" onsubmit="removeAtts();" name="setupform" method="POST">';
$disSelect = $ePO == 1 ? ' disabled="disabled"' : '';
$htmlout .= '<table><tr><td>'.get_string('fmode', 'mootyper').'</td><td><select'.$disSelect.' onchange="this.form.submit()" name="mode" id="mode">';
//$lessons = get_typerlessons();

if(has_capability('mod/mootyper:editall', get_context_instance(CONTEXT_COURSE, $course->id)))
	$lessons = get_typerlessons();
else
	$lessons = get_mootyperlessons($USER->id, $course->id);

if($modePO == 0 || is_null($modePO))
{
	$htmlout .= '<option selected="true" value="0">'.
            get_string('sflesson', 'mootyper').'</option><option value="1">'.
            get_string('isexamtext', 'mootyper').'</option>';
    $htmlout .= '</select></td></tr><tr><td>';
    $htmlout .= get_string('excategory', 'mootyper').'</td><td><select'.$disSelect.' onchange="this.form.submit()" id="lesson" name="lesson">';
    for($ij=0; $ij<count($lessons); $ij++)
    {
		if($lessons[$ij]['id'] == $lessonPO)
			$htmlout .= '<option selected="true" value="'.$lessons[$ij]['id'].'">'.$lessons[$ij]['lessonname'].'</option>';
		else
			$htmlout .= '<option value="'.$lessons[$ij]['id'].'">'.$lessons[$ij]['lessonname'].'</option>';
	}
    $htmlout .= '</select></td></tr><tr><td>'.get_string('requiredgoal', 'mootyper').'</td><td><input value="'.$goalPO.'" style="width: 20px;" type="text" name="requiredgoal"> % </td></tr>';
}
else if($modePO == 1)
{
	$htmlout .= '<option value="0">'.
            get_string('sflesson', 'mootyper').'</option><option value="1" selected="true">'.
            get_string('isexamtext', 'mootyper').'</option>';
    $htmlout .= '</select></td></tr><tr><td>';
    $htmlout .= get_string('flesson', 'mootyper').'</td><td><select'.$disSelect.' onchange="this.form.submit()" id="lesson" name="lesson">';
    for($ij=0; $ij<count($lessons); $ij++)
    {
		if($lessons[$ij]['id'] == $lessonPO)
			$htmlout .= '<option selected="true" value="'.$lessons[$ij]['id'].'">'.$lessons[$ij]['lessonname'].'</option>';
		else
			$htmlout .= '<option value="'.$lessons[$ij]['id'].'">'.$lessons[$ij]['lessonname'].'</option>';
	}
    $htmlout .= '</select></td></tr>';
    $exercises = get_exercises_by_lesson($lessonPO);
    $htmlout .= '<tr><td>'.get_string('fexercise', 'mootyper').'</td><td><select'.$disSelect.' name="exercise" id="exercise">';
    for($ik=0; $ik<count($exercises); $ik++)
    {
		if($exercises[$ik]['id'] == $exercisePO)
			$htmlout .= '<option selected="true" value="'.$exercises[$ik]['id'].'">'.$exercises[$ik]['exercisename'].'</option>';
		else
			$htmlout .= '<option value="'.$exercises[$ik]['id'].'">'.$exercises[$ik]['exercisename'].'</option>';
	}
    $htmlout .= '</select></td></tr>';
}
$htmlout .= '<tr><td>'.get_string('showkeyboard', 'mootyper').'</td><td>';
if($showKeyboardPO == 'on'){
	$htmlout .= '<input type="checkbox" checked="checked" onchange="this.form.submit()" name="showkeyboard">';
	$layouts = get_keyboard_layouts_db();
    //$mform->addElement('select', 'layout', get_string('layout', 'mootyper'), $layouts);
    $defLayout = $mooCFG->defaultlayout;
    $htmlout .= '<tr><td>'.get_string('layout', 'mootyper').'</td><td><select name="layout">';
    foreach($layouts as $lkey => $lval)
    {
		if((count($_POST) > 1) && ($lkey == $defLayout))
			$htmlout .= '<option value="'.$lkey.'" selected="true">'.$lval.'</option>';
		else if($lkey == $layoutPO)
			$htmlout .= '<option value="'.$lkey.'" selected="true">'.$lval.'</option>';
		else
			$htmlout .= '<option value="'.$lkey.'">'.$lval.'</option>';
	}
    $htmlout .= '</select>';
}
else
	$htmlout .= '<input type="checkbox" onchange="this.form.submit()" name="showkeyboard">';
$htmlout .= '</td></tr>';    

$htmlout .= '</table>';
$htmlout .= '<br><input name="button" value="'.get_string('fconfirm', 'mootyper').'" type="submit">';
$htmlout .= '</form>';
echo $htmlout;
// Finish the page
echo $OUTPUT->footer();


