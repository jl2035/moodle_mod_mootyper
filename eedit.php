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
 * This file is used to edit exercise content. Called from exercises.php.
 * 
 * @package    mod
 * @subpackage mootyper
 * @copyright  2011 Jaka Luthar (jaka.luthar@gmail.com)
 * @copyright  2016 onwards AL Rachels (drachels@drachels.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $USER;
global $DB;
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$exercise_ID = optional_param('ex', 0, PARAM_INT);

if ($id)
    $course     = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
else
    error('You must specify a course_module ID or an instance ID');
if($exercise_ID == 0)
	error('No exercise to edit!');

$context = context_course::instance($id);
	
require_login($course, true);
if(isset($_POST['button']))
   $param1 = $_POST['button']; 
if(isset($param1) && get_string('fconfirm', 'mootyper') == $param1 )
{
	$newText = $_POST['texttotype'];
	$rcrd = $DB->get_record('mootyper_exercises', array('id' => $exercise_ID), '*', MUST_EXIST);
	$updR = new stdClass();
	$updR->id = $rcrd->id;
	$updR->texttotype = str_replace("\r\n", '\n', $newText);
	$updR->exercisename = $rcrd->exercisename;
	$updR->lesson = $rcrd->lesson;
	$updR->snumber = $rcrd->snumber;
	$DB->update_record('mootyper_exercises', $updR);
	
	// Trigger module exercise_edited event.
	$event = \mod_mootyper\event\exercise_edited::create(array(
		'objectid' => $course->id,
		'context' => $context
	));
	$event->trigger();
	
	$webDir = $CFG->wwwroot . '/mod/mootyper/exercises.php?id='.$id;
	echo '<script type="text/javascript">window.location="'.$webDir.'";</script>';

}

$PAGE->set_url('/mod/mootyper/eedit.php', array('id' => $course->id, 'ex' => $exercise_ID));
$PAGE->set_title(get_string('etitle', 'mootyper'));
$PAGE->set_heading(get_string('eheading', 'mootyper'));
$PAGE->set_cacheable(false);
echo $OUTPUT->header();
$exerciseToEdit = $DB->get_record('mootyper_exercises', array('id' => $exercise_ID), 'texttotype', MUST_EXIST); ?>

<script type="text/javascript">
function isLetter(str) {
	var pattern = /[a-zčšžđćüöäèéàçâêîôº¡çñ]/i;
	return str.length === 1 && str.match(pattern);
}
function isNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}

var ok = true;

function clClick()
{
	var exercise_text = document.getElementById("texttotype").value;
	var allowed_chars = ['!','@','#','$','%','^','&','(',')','*','_','+',':',';','"','{','}','>','<','?','\'','-','/','=','.',',',' ','|','¡','`','ç','ñ','º','¿','ª','·','\n','\r','\r\n', '\n\r', ']', '[', '¬', '´', '`'];
	var shown_text = "";
	ok = true;
	for(var i=0; i<exercise_text.length; i++) {
		if((exercise_text[i] != '\n' && exercise_text[i] != '\r\n' && exercise_text[i] != '\n\r' && exercise_text[i] != '\r') && !isLetter(exercise_text[i]) && !isNumber(exercise_text[i]) && allowed_chars.indexOf(exercise_text[i]) == -1) {
			shown_text += '<span style="color: red;">'+exercise_text[i]+'</span>';
			ok = false;
			/*var text = (i-3)+'-'+exercise_text[i-3]+"\n";
			text += (i-2)+'-'+exercise_text[i-2]+"\n";
			text += (i-1)+'-'+exercise_text[i-1]+"\n";
			text += i+'-'+exercise_text[i]+"\n";
			text += (i+1)+'-'+exercise_text[i+1]+"\n";
			text += (i+2)+'-'+exercise_text[i+2];
			alert(text);*/
		}
		else
			shown_text += exercise_text[i];
	}
	if(!ok) {
		document.getElementById('text_holder_span').innerHTML = shown_text;
		return false;
	}
	else 
		return true;
}
</script>
<?php echo '<form method="POST">';
echo '<span id="text_holder_span" class=""></span><br>'.get_string('fexercise', 'mootyper').':<br>'.
	 '<textarea name="texttotype" id="texttotype">'.str_replace('\n', "&#10;", $exerciseToEdit->texttotype).'</textarea><br>'.
	 '<br><input name="button" onClick="return clClick()" type="submit" value="'.get_string('fconfirm', 'mootyper').'">'.
     '</form>';
echo $OUTPUT->footer();
