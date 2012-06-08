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
 * Prints a particular instance of mootyper
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod
 * @subpackage mootyper
 * @copyright  2012 Jaka Luthar (jaka.luthar@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $USER;
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

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

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);
add_to_log($course->id, 'mootyper', 'view', "view.php?id={$cm->id}", $mootyper->name, $cm->id);

/// Print the page header

$PAGE->set_url('/mod/mootyper/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($mootyper->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// other things you may want to set - remove if not needed
$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('tb1');
//$PAGE->add_body_class('mootyper-'.$somevar);

// Output starts here
echo $OUTPUT->header();

if ($mootyper->intro) { // Conditions to show the intro can change to look for own settings or whatever
    echo $OUTPUT->box(format_module_intro('mootyper', $mootyper, $cm->id), 'generalbox mod_introbox', 'mootyperintro');
}
if($mootyper->lesson != NULL)
{
// Replace the following lines with you own code
//echo $OUTPUT->heading('Yay! It works!'
//get_record('mootyper_exercises', array('id' => $eid));
//$mootyper = jget_mootyper_record($n);
if($mootyper->isexam)
{
    $insertDir = $CFG->wwwroot . '/mod/mootyper/gadd.php';
    $exercise_ID = $mootyper->exercise;
    $exercise = get_exercise_record($exercise_ID);
    $textToEnter = $exercise->texttotype; //"N=".$n." exercise_ID=".$mootyper->exercise." fjajfjfjfj name=".$mootyper->name." fjfjfjfjfj";
}
else
{
	$reqiredGoal = $mootyper->requiredgoal;
	$insertDir = $CFG->wwwroot . '/mod/mootyper/gcnext.php';
	$exercise = get_exercise_from_mootyper($mootyper->id, $mootyper->lesson, $USER->id);
	if($exercise != FALSE){
	$exercise_ID = $exercise->id;
	$textToEnter = $exercise->texttotype;}
}
if(exam_already_done($mootyper, $USER->id) && $mootyper->isexam)
{
	echo get_string('examdone', 'mootyper');
	echo "<br>";
	if (has_capability('mod/mootyper:viewgrades', get_context_instance(CONTEXT_COURSE, $course->id))) {
				$jlnk4 = $CFG->wwwroot . '/mod/mootyper/gview.php?id='.$id.'&sid='.$mootyper->id.'&n='.$mootyper->id;
				echo '<a href="'.$jlnk4.'">'.get_string('viewgrades', 'mootyper').'</a><br><br>';
    }
}
else if($exercise != FALSE)
{
echo '<link rel="stylesheet" type="text/css" href="style.css">';
//js_init_call !!!!!!!!!
echo '<script type="text/javascript" src="typer.js"></script>';
//onload="initTextToEnter('')"

?>
<div id="mainDiv">
			<form name='form1' id='form1' method='post' action='<?php echo $insertDir; ?>'> 
<div id="tipkovnica" style="float: left; text-align:center; margin-left: auto; margin-right: auto;">
<h4><?php if(!$mootyper->isexam) echo $exercise->exercisename; ?></h4>
<br>
	<div style="float: left; padding-bottom: 10px;" id="textToEnter"></div><br>
	<div id="innerTipkovnica" style="margin: 0px auto;display: inline-block;"><br>
<span id="jkeytildo" class="normal">¸</span>
<span id="jkey1" class="normal">1</span>
<span id="jkey2" class="normal">2</span>
<span id="jkey3" class="normal">3</span>
<span id="jkey4" class="normal">4</span>
<span id="jkey5" class="normal">5</span>
<span id="jkey6" class="normal">6</span>
<span id="jkey7" class="normal">7</span>
<span id="jkey8" class="normal">8</span>
<span id="jkey9" class="normal">9</span>
<span id="jkey0" class="normal">0</span>
<span id="jkeyvprasaj" class="normal">?</span>
<span id="jkeyplus" class="normal">+</span>
<span id="jkeybackspace" class="normal" style="border-right-style: solid;">Backspace</span><br>
<span id="jkeytab" class="normal" style="width: 50px;">Tab</span>
<span id="jkeyq" class="normal">Q</span>
<span id="jkeyw" class="normal">W</span>
<span id="jkeye" class="normal">E</span>
<span id="jkeyr" class="normal">R</span>
<span id="jkeyt" class="normal">T</span>
<span id="jkeyz" class="normal">Z</span>
<span id="jkeyu" class="normal">U</span>
<span id="jkeyi" class="normal">I</span>
<span id="jkeyo" class="normal">O</span>
<span id="jkeyp" class="normal">P</span>
<span id="jkeyš" class="normal">Š</span>
<span id="jkeyđ" class="normal" style="border-right-style: solid;">Đ</span>
<br>
<span id="jkeycaps" class="normal" style="width: 60px;">C.lock</span>
<span id="jkeya" class="finger4">A</span>
<span id="jkeys" class="finger3">S</span>
<span id="jkeyd" class="finger2">D</span>
<span id="jkeyf" class="finger1">F</span>
<span id="jkeyg" class="normal">G</span>
<span id="jkeyh" class="normal">H</span>
<span id="jkeyj" class="finger1">J</span>
<span id="jkeyk" class="finger2">K</span>
<span id="jkeyl" class="finger3">L</span>
<span id="jkeyč" class="finger4">Č</span>
<span id="jkeyć" class="normal">Ć</span>
<span id="jkeyž" class="normal">Ž</span>
<span id="jkeyenter" class="normal" style="border-right-style: solid;">Enter</span>
<br>
<span id="jkeyshiftl" class="normal" style="width: 50px;">Shift</span>
<span id="jkeyckck" class="normal">&lt; &gt;</span>
<span id="jkeyy" class="normal">Y</span>
<span id="jkeyx" class="normal">X</span>
<span id="jkeyc" class="normal">C</span>
<span id="jkeyv" class="normal">V</span>
<span id="jkeyb" class="normal">B</span>
<span id="jkeyn" class="normal">N</span>
<span id="jkeym" class="normal">M</span>
<span id="jkeyvejica" class="normal">,</span>
<span id="jkeypika" class="normal">.</span>
<span id="jkeypomislaj" class="normal">-</span>
<span id="jkeyshiftd" class="normal" style="width: 75px; border-right-style: solid;">Shift</span>
<br>
<span id="jkeyctrll" class="normal" style="width: 40px;">Ctrl</span>
<span id="jkeyfn" class="normal">Fn</span>
<span id="jkeyalt" class="normal" style="width: 40px;">Alt</span>
<span id="jkeyspace" class="normal" style="width: 250px;">Space</span>
<span id="jkeyaltgr" class="normal" style="width: 45px;">Alt gr</span>
<!--span id="jempty" class="normal" style="width: 30px;">&nbsp;</span-->
<span id="jkeyctrlr" class="normal" style="width: 60px; border-right-style: solid;">Ctrl</span><br>
</div>
	
<br>
					    <textarea name="tb1" wrap="off" id="tb1" class="tb1" onfocus="this.value=''" onkeypress="return gumbPritisnjen(event)"  
			            onpaste="return false" onselectstart="return false"
			            onCopy="return false" onCut="return false" 
			            onDrag="return false" onDrop="return false" autocomplete="off"><?php echo get_string('chere', 'mootyper').'...'; ?></textarea>
			            				            
</div>				
<div id="reportDiv" style="float: right; /*position: relative; right: 90px; top: 35px;*/">
											<?php
			if (has_capability('mod/mootyper:viewgrades', get_context_instance(CONTEXT_COURSE, $course->id))) {
				$jlnk4 = $CFG->wwwroot . '/mod/mootyper/gview.php?id='.$id.'&sid='.$mootyper->id.'&n='.$mootyper->id;;
				echo '<a href="'.$jlnk4.'">'.get_string('viewgrades', 'mootyper').'</a><br><br>';
			}
			?>
							<input name='rpCourseId' type='hidden' value='<?php echo $course->id; ?>'>
							<input name='rpSityperId' type='hidden' value='<?php echo $mootyper->id; ?>'>
							<input name='rpUser' type='hidden' value='<?php echo $USER->id; ?>'>
							<input name='rpExercise' type='hidden' value='<?php echo $exercise_ID; ?>'>
							<input name='rpFullHits' type='hidden' value=''>
							<input name='rpGoal' type='hidden' value='<?php if(isset($reqiredGoal)) echo $reqiredGoal; ?>'>
						    <input name='rpTimeInput' type='hidden'>
						    <input name='rpMistakesInput' type='hidden'>
						    <input name='rpAccInput' type='hidden'>
						    <input name='rpSpeedInput' type='hidden'>
							<div id="rdDiv2">
								<strong><?php echo get_string('rtime', 'mootyper'); ?></strong> <span id="jsTime">0</span> s<br>
								<strong><?php echo get_string('rprogress', 'mootyper'); ?></strong> <span id="jsProgress"> 0</span><br>
								<strong><?php echo get_string('rmistakes', 'mootyper'); ?></strong> <span id="jsMistakes">0</span><br>
								<strong><?php echo get_string('rprecision', 'mootyper'); ?></strong> <span id="jsAcc"> 0</span>%<br>
								<strong><?php echo get_string('rhitspermin', 'mootyper'); ?></strong> <span id="jsSpeed">0</span>
								<br>
							</div>
							<br><input style="visibility: hidden;" id="btnContinue" name='btnContinue' type="submit" value=<?php echo "'".get_string('fcontinue', 'mootyper')."'"; ?>> 
				</div>	
							
			</form>
		</div>
<?php
$textToInit = '';
for($it=0; $it<strlen($textToEnter); $it++)
{
	if($textToEnter[$it] == "\n")
		$textToInit .= '\n';
	else
		$textToInit .= $textToEnter[$it];
}
echo '<script type="text/javascript">
	initTextToEnter("'.$textToInit.'");
</script>';
// Finish the page
}
else
{
	echo get_string('endlesson', 'mootyper');
	echo "<br>";
	if (has_capability('mod/mootyper:viewgrades', get_context_instance(CONTEXT_COURSE, $course->id))) {
				$jlnk4 = $CFG->wwwroot . '/mod/mootyper/gview.php?id='.$id.'&sid='.$mootyper->id.'&n='.$mootyper->id;
				echo '<a href="'.$jlnk4.'">'.get_string('viewgrades', 'mootyper').'</a><br><br>';
    }
}
}
else
{
	if (has_capability('mod/mootyper:setup', get_context_instance(CONTEXT_COURSE, $course->id)))
	{
		$vaLnk = $CFG->wwwroot."/mod/mootyper/mod_setup.php?n=".$mootyper->id;
		echo '<a href="'.$vaLnk.'">'.get_string('fsetup', 'mootyper').'</a>';
	}
	else
		echo get_string('notreadyyet', 'mootyper');
}
echo $OUTPUT->footer();
