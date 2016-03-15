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
 * This is is used to add a new exercise/category.
 *
 * @package    mod
 * @subpackage mootyper
 * @copyright  2011 Jaka Luthar (jaka.luthar@gmail.com)
 * @copyright  2016 onwards AL Rachels (drachels@drachels.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

global $USER, $DB;
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
//$n = optional_param('n', 0, PARAM_INT); // mootyper instance ID - it should be named as the first character of the module

if ($id){
    $course     = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
}
else
    error('You must specify a course_module ID or an instance ID');

require_login($course, true);
$lessonPO = optional_param('lesson', -1, PARAM_INT);
if(isset($_POST['button']))
   $param1 = $_POST['button'];

$context = context_course::instance($id);   

   
//DB insert
if(isset($param1) && get_string('fconfirm', 'mootyper') == $param1 ) 
{
	// $lessonPO = optional_param('lesson', -1, PARAM_INT);
	$texttotypeePO = $_POST['texttotype'];
	// $enamePO = $_POST['exercisename'];
	if($lessonPO == -1)
	{
		$lsnnamePO = $_POST['lessonname'];
		$lsnrecord = new stdClass();
		$lsnrecord->lessonname = $lsnnamePO;
		$lsnrecord->visible = $_POST['visible'];
		$lsnrecord->editable = $_POST['editable'];
		$lsnrecord->authorid = $USER->id;
		$lsnrecord->courseid = $course->id;
		$lesson_id = $DB->insert_record('mootyper_lessons', $lsnrecord, true);
	}
	else
		$lesson_id = $lessonPO;
	
	$snum = get_new_snumber($lesson_id);
	$erecord = new stdClass();
	$erecord->exercisename = "".$snum;
	$erecord->snumber = $snum;
	$erecord->lesson = $lesson_id;
	$erecord->texttotype = str_replace("\r\n", '\n', $texttotypeePO);
	$DB->insert_record('mootyper_exercises', $erecord, false);
	$webDir = $CFG->wwwroot . '/mod/mootyper/exercises.php?id='.$id;
	//header('Location: '.$webDir);
	echo '<script type="text/javascript">window.location="'.$webDir.'";</script>';
	// Trigger module exercise_added event.
	$event = \mod_mootyper\event\exercise_added::create(array(
		'objectid' => $course->id,
		'context' => $context
	));
	$event->trigger();
}

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
$lessonsG = get_typerlessons();
if(has_capability('mod/mootyper:editall', context_course::instance($course->id)))
	$lessons = $lessonsG;
else
{
	$lessons = array();
	foreach($lessonsG as $lsnG)
		if(is_editable_by_me($USER->id, $lsnG['id']))
			$lessons[] = $lsnG;
}
echo '<form method="POST">';
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
if($lessonPO == -1){
	echo '<br><br>...'.get_string('lsnname', 'mootyper').': <input type="text" name="lessonname" id="lessonname"><span style="color:red;" id="namemsg"></span>';
	echo '<br><br>'.get_string('visibility', 'mootyper').': <select name="visible">';
	echo '<option value="2">'.get_string('vaccess2', 'mootyper').'</option>';
	echo '<option value="1">'.get_string('vaccess1', 'mootyper').'</option>';
	echo '<option value="0">'.get_string('vaccess0', 'mootyper').'</option>';
	echo '</select><br><br>'.get_string('editable', 'mootyper').': <select name="editable">';
	echo '<option value="2">'.get_string('eaccess2', 'mootyper').'</option>';
	echo '<option value="1">'.get_string('eaccess1', 'mootyper').'</option>';
	echo '<option value="0">'.get_string('eaccess0', 'mootyper').'</option>';
	echo '</select>';
	
}
?>

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
		if(!isLetter(exercise_text[i]) && !isNumber(exercise_text[i]) && allowed_chars.indexOf(exercise_text[i]) == -1) {
			shown_text += '<span style="color: red;">'+exercise_text[i]+'</span>';
			ok = false;
		}
		else
			shown_text += exercise_text[i];
	}
	if(!ok) {
		document.getElementById('text_holder_span').innerHTML = shown_text;
		return false;
	}
	if(document.getElementById("lessonname").value == "") {
		document.getElementById("namemsg").innerHTML = '<?php echo get_string('reqfield', 'mootyper');?>';
		return false;
	}
	else
		return true;
}
</script>

<?php 
echo '<br><span id="text_holder_span" class=""></span><br>'.get_string('fexercise', 'mootyper').':<br>'.
	 '<textarea rows="4" cols="40" name="texttotype" id="texttotype"></textarea><br>'.
	 '<br><input name="button" onClick="return clClick()" type="submit" value="'.get_string('fconfirm', 'mootyper').'">'.
     '</form>';

echo $OUTPUT->footer();
