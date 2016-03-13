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
$orderBy = optional_param('orderby', -1, PARAM_INT);
$des = optional_param('desc', -1, PARAM_INT);
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
$context = context_module::instance($cm->id);
$PAGE->set_url('/mod/mootyper/owngrades.php', array('id' => $cm->id));
$PAGE->set_title(format_string($mootyper->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->set_cacheable(false);
echo $OUTPUT->header();
echo '<link rel="stylesheet" type="text/css" href="style.css">';
echo $OUTPUT->heading($mootyper->name);
$htmlout = '';
$htmlout .= '<div id="mainDiv">';

	//Update the library
	if($des == -1 || $des == 0)
		$grds = get_typergradesuser($_GET['n'], $USER->id, $orderBy, 0);
	else if($des == 1)
		$grds = get_typergradesuser($_GET['n'], $USER->id, $orderBy, 1);
	else
		$grds = get_typergradesuser($_GET['n'], $USER->id, $orderBy, $des);
	
	if($des == -1 || $des == 1){
		$lnkAdd = "&desc=0";
	}
	else{
		$lnkAdd = "&desc=1";
	}
	$arrTextAdds = array();
	$arrTextAdds[2] = '<span class="arrow-s" style="font-size:1em;"></span>'; 
	$arrTextAdds[4] = '<span class="arrow-s" style="font-size:1em;"></span>'; 
	$arrTextAdds[5] = '<span class="arrow-s" style="font-size:1em;"></span>'; 
	$arrTextAdds[6] = '<span class="arrow-s" style="font-size:1em;"></span>'; 
	$arrTextAdds[7] = '<span class="arrow-s" style="font-size:1em;"></span>'; 
	$arrTextAdds[8] = '<span class="arrow-s" style="font-size:1em;"></span>'; 
	$arrTextAdds[9] = '<span class="arrow-s" style="font-size:1em;"></span>'; 
	$arrTextAdds[12] = '<span class="arrow-s" style="font-size:1em;"></span>';
	$arrTextAdds[$orderBy] = $des == -1 || $des == 1 ? 
		'<span class="arrow-s" style="font-size:1em;"></span>' : 
		'<span class="arrow-n" style="font-size:1em;"></span>';
	if($grds != FALSE){
		$htmlout .= '<table style="border-style: solid;"><tr><td>Exercise</td><td><a href="?id='.$id.'&n='.$n.'&orderby=4'.$lnkAdd.'">'.
		get_string('vmistakes', 'mootyper').'</a>'.$arrTextAdds[4].'</td><td><a href="?id='.$id.'&n='.$n.'&orderby=5'.$lnkAdd.'">'.
		get_string('timeinseconds', 'mootyper').'</a>'.$arrTextAdds[5].'</td><td><a href="?id='.$id.'&n='.$n.'&orderby=6'.$lnkAdd.'">'.
		get_string('hitsperminute', 'mootyper').'</a>'.$arrTextAdds[6].'</td><td><a href="?id='.$id.'&n='.$n.'&orderby=7'.$lnkAdd.'">'.
		get_string('fullhits', 'mootyper').'</a>'.$arrTextAdds[7].'</td><td><a href="?id='.$id.'&n='.$n.'&orderby=8'.$lnkAdd.'">'.
		get_string('precision', 'mootyper').'</a>'.$arrTextAdds[8].'</td><td><a href="?id='.$id.'&n='.$n.'&orderby=9'.$lnkAdd.'">'.
		get_string('timetaken', 'mootyper').'</a>'.$arrTextAdds[9].'</td><td><a href="?id='.$id.'&n='.$n.'&orderby=12'.$lnkAdd.'">'.
		get_string('wpm', 'mootyper').'</a>'.$arrTextAdds[12].'</td></tr>';
		foreach($grds as $gr)
		{			
			if(!$mootyper->isexam && $gr->pass)
				$stil = ' background-color: #7FEF6C;';
			else if(!$mootyper->isexam && !$gr->pass)
				$stil = ' background-color: #FF6C6C;';
			else
				$stil = '';
			$f_col = $mootyper->isexam ? '---' : $gr->exercisename;
			$htmlout .= '<tr style="border-top-style: solid;'.$stil.'"><td>'.$f_col.'</td><td>'.$gr->mistakes.'</td><td>'.$gr->timeinseconds.
			' s</td><td>'.$gr->hitsperminute.'</td><td>'.$gr->fullhits.'</td><td>'.$gr->precisionfield.'%</td><td>'.date('d. M Y G:i', $gr->timetaken).'</td><td>'.$gr->wpm.'</td></tr>';
		}
		$avg = get_grades_avg($grds);
		if(!$mootyper->isexam)
			$htmlout .= '<tr style="border-top-style: solid;"><td><strong>'.get_string('average', 'mootyper').': </strong></td><td>'.$avg['mistakes'].'</td><td>'.$avg['timeinseconds'].' s</td><td>'.$avg['hitsperminute'].'</td><td>'.$avg['fullhits'].'</td><td>'.$avg['precisionfield'].'%</td><td></td><td></td></tr>';
		$htmlout .= '</table>';
	}
	else
		echo get_string('nogrades', 'mootyper');
$htmlout .= '</div>';
echo $htmlout;
echo $OUTPUT->footer();



