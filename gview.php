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
 * This file displays grades of the paricular mootyper instance.
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
$se = optional_param('exercise', 0, PARAM_INT);
$md = optional_param('jmode', 0, PARAM_INT);
$us = optional_param('juser', 0, PARAM_INT);
if($md == 1)
    $us = 0;
else if($md == 0)
	$se = 0;

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

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

//add_to_log($course->id, 'mootyper', 'view', "view.php?id={$cm->id}", $mootyper->name, $cm->id);

/// Print the page header

$PAGE->set_url('/mod/mootyper/gview.php', array('id' => $cm->id));
$PAGE->set_title(format_string($mootyper->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// other things you may want to set - remove if not needed
$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('tb1');
//$PAGE->add_body_class('mootyper-'.$somevar);
// Output starts here
echo $OUTPUT->header();
// Replace the following lines with you own code
echo $OUTPUT->heading($mootyper->name);
//get_record('mootyper_exercises', array('id' => $eid));
//$mootyper = jget_mootyper_record($n);
//$exercise_ID = $mootyper->exercise;
//$exercise = get_exercise_record($exercise_ID);
//$textToEnter = $exercise->texttotype; //"N=".$n." exercise_ID=".$mootyper->exercise." fjajfjfjfj name=".$mootyper->name." fjfjfjfjfj";

//onload="initTextToEnter('')"
$htmlout = '';
$htmlout .= '<div id="mainDiv">';
if($mootyper->isexam)
{
	$grds = get_typergradesfull($_GET['sid']);
	if($grds != FALSE){
		$htmlout .= '<table style="border-style: solid;"><tr><td>'.get_string('student', 'mootyper').'</td><td>'.get_string('vmistakes', 'mootyper').'</td><td>'.
					get_string('timeinseconds', 'mootyper').'</td><td>'.get_string('hitsperminute', 'mootyper').'</td><td>'.get_string('fullhits', 'mootyper').
					'</td><td>'.get_string('precision', 'mootyper').'</td><td>'.get_string('timetaken', 'mootyper').'</td></tr>';
		foreach($grds as $gr)
		{
			$htmlout .= '<tr style="border-top-style: solid;"><td>'.$gr->firstname.' '.$gr->lastname.'</td><td>'.$gr->mistakes.'</td><td>'.$gr->timeinseconds.
			' s</td><td>'.$gr->hitsperminute.'</td><td>'.$gr->fullhits.'</td><td>'.$gr->precisionfield.'%</td><td>'.date('d. M Y G:i', $gr->timetaken).'</td></tr>';
		}
		$avg = get_grades_avg($grds);
		$htmlout .= '<tr style="border-top-style: solid;"><td><strong>'.get_string('average', 'mootyper').': </strong></td><td>'.$avg['mistakes'].'</td><td>'.$avg['timeinseconds'].' s</td><td>'.$avg['hitsperminute'].'</td><td>'.$avg['fullhits'].'</td><td>'.$avg['precisionfield'].'%</td><td></td></tr>';
		$htmlout .= '</table>';
	}
	else
		echo get_string('nogrades', 'mootyper');
}
else
{
	
	
	$htmlout .= '<form method="post">';
	$htmlout .= '<table><tr><td>'.get_string('gviewmode', 'mootyper').'</td><td>';
	$htmlout .= '<select onchange="this.form.submit()" name="jmode"><option value="0">'.get_string('byuser', 'mootyper').'</option>';
	if($md == 1)
		$htmlout .= '<option value="1" selected="true">'.get_string('bymootyper', 'mootyper').'</option>';
	else
		$htmlout .= '<option value="1">'.get_string('bymootyper', 'mootyper').'</option>';
	$htmlout .= '</select></td></tr>';
	
	if($md == 0)
	{
		$usrs = get_users_of_one_instance($mootyper->id);
		$htmlout .= '<tr><td>'.get_string('student', 'mootyper').'</td><td>';
		$htmlout .= '<select name="juser" onchange="this.form.submit()">';
		$htmlout .= '<option value="0">'.get_string('allstring', 'mootyper').'</option>';
		if($usrs != FALSE)	
			foreach($usrs as $x)
			{
				if($us == $x->id)
					$htmlout .= '<option value="'.$x->id.'" selected="true">'.$x->firstname.' '.$x->lastname.'</option>';
				else
					$htmlout .= '<option value="'.$x->id.'">'.$x->firstname.' '.$x->lastname.'</option>';
			}         
		$htmlout .= '</select>';
		$htmlout .= '</td></tr>';
	}
	else
	{
		$exes = get_exercises_by_lesson($mootyper->lesson);
		$htmlout .= '<tr><td>'.get_string('fexercise', 'mootyper').'</td><td>';
		$htmlout .= '<select name="exercise" onchange="this.form.submit()">';
		$htmlout .= '<option value="0">'.get_string('allstring', 'mootyper').'</option>';
		foreach($exes as $x)
		{
			if($se == $x['id'])
				$htmlout .= '<option value="'.$x['id'].'" selected="true">'.$x['exercisename'].'</option>';
			else
				$htmlout .= '<option value="'.$x['id'].'">'.$x['exercisename'].'</option>';
		}         
		$htmlout .= '</select>';
		$htmlout .= '</td></tr>';		
	}

	//now get grades with get_typer_grades_adv
	$grds = get_typer_grades_adv($mootyper->id, $se, $us);
	if($grds != FALSE){
		$htmlout .= '<table style="border-style: solid;"><tr><td>'.get_string('student', 'mootyper').'</td><td>'.
		get_string('fexercise', 'mootyper').'</td><td>'.get_string('vmistakes', 'mootyper').'</td><td>'.
		get_string('timeinseconds', 'mootyper').'</td><td>'.get_string('hitsperminute', 'mootyper').'</td><td>'.
		get_string('fullhits', 'mootyper').'</td><td>'.get_string('precision', 'mootyper').'</td><td>'.
		get_string('timetaken', 'mootyper').'</td></tr>';
		foreach($grds as $gr)
		{
			if($gr->pass)
				$stil = 'background-color: #7FEF6C;';
			else
				$stil = 'background-color: #FF6C6C;';
			$htmlout .= '<tr style="border-top-style: solid;'.$stil.'"><td>'.$gr->firstname.' '.$gr->lastname.'</td><td>'.$gr->exercisename.'</td><td>'.$gr->mistakes.'</td><td>'.
			$gr->timeinseconds.' s</td><td>'.$gr->hitsperminute.'</td><td>'.$gr->fullhits.'</td><td>'.$gr->precisionfield.'%</td><td>'.date('d. M Y G:i', $gr->timetaken).'</td></tr>';
		}
		$htmlout .= '</table>';
	}
	else
		echo get_string('nogrades', 'mootyper');
	$htmlout .= '</table>';
	$htmlout .= '</form>';
}
$htmlout .= '</div>';
echo $htmlout;
// Finish the page
echo $OUTPUT->footer();


