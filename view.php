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
global $USER, $CFG;
require_once (dirname(dirname(dirname(__FILE__))) . '/config.php');

require_once (dirname(__FILE__) . '/lib.php');

require_once (dirname(__FILE__) . '/locallib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n = optional_param('n', 0, PARAM_INT); // mootyper instance ID - it should be named as the first character of the module

if ($id) {
    $cm = get_coursemodule_from_id('mootyper', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course) , '*', MUST_EXIST);
    $mootyper = $DB->get_record('mootyper', array('id' => $cm->instance) , '*', MUST_EXIST);
}
elseif ($n) {
    $mootyper = $DB->get_record('mootyper', array('id' => $n), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $mootyper->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('mootyper', $mootyper->id, $course->id, false, MUST_EXIST);
}
else {
    error('You must specify a course_module ID or an instance ID');
}
require_login($course, true, $cm);
$context = context_module::instance($cm->id);
$PAGE->set_url('/mod/mootyper/view.php', array(
    'id' => $cm->id
));
$PAGE->set_title(format_string($mootyper->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->set_cacheable(false);

// $PAGE->set_focuscontrol('tb1');
// $PAGE->add_body_class('mootyper-'.$somevar);
// Output starts here

echo $OUTPUT->header();
echo '<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>';

if ($mootyper->intro) {
    echo $OUTPUT->box(format_module_intro('mootyper', $mootyper, $cm->id) , 'generalbox mod_introbox', 'mootyperintro');
}
if ($mootyper->lesson != NULL) {
    if ($mootyper->isexam) {
        $exercise_ID = $mootyper->exercise;
        $exercise = get_exercise_record($exercise_ID);
        $textToEnter = $exercise->texttotype;
        $insertDir = $CFG->wwwroot . '/mod/mootyper/gadd.php?words=' . str_word_count($textToEnter);
    }
    else {
        $reqiredGoal = $mootyper->requiredgoal;
        $exercise = get_exercise_from_mootyper($mootyper->id, $mootyper->lesson, $USER->id);
        if ($exercise != FALSE) {
            $exercise_ID = $exercise->id;
            $textToEnter = $exercise->texttotype;
        }
        if(isset($textToEnter))
			$insertDir = $CFG->wwwroot . '/mod/mootyper/gcnext.php?words=' . str_word_count($textToEnter);
    }
    if (exam_already_done($mootyper, $USER->id) && $mootyper->isexam) {
        echo get_string('examdone', 'mootyper');
        echo "<br>";
        if (has_capability('mod/mootyper:viewgrades', context_module::instance($cm->id))) {
            $jlnk4 = $CFG->wwwroot . '/mod/mootyper/gview.php?id=' . $id . '&n=' . $mootyper->id;
            echo '<a href="' . $jlnk4 . '">' . get_string('viewgrades', 'mootyper') . '</a><br /><br />';
        }

        if (has_capability('mod/mootyper:viewmygrades', context_module::instance($cm->id))) {
            $jlnk7 = $CFG->wwwroot . "/mod/mootyper/owngrades.php?id=" . $id . "&n=" . $mootyper->id;
            echo '<a href="' . $jlnk7 . '">' . get_string('viewmygrades', 'mootyper') . '</a><br /><br />';
        }
    }
    else if ($exercise != FALSE) {
        echo '<link rel="stylesheet" type="text/css" href="style.css">';
        if ($mootyper->showkeyboard)
			$display_none = false;
        else
			$display_none = true;
		$keyboard_js = get_instance_layout_js_file($mootyper->layout);
		echo '<script type="text/javascript" src="' . $keyboard_js . '"></script>';
        echo '<script type="text/javascript" src="typer.js"></script>';
?>
<div id="mainDiv">
<form name='form1' id='form1' method='post' action='<?php echo $insertDir; ?>'> 
<div id="tipkovnica" style="float: left; text-align:center; margin-left: auto; margin-right: auto;">
<h4><?php
        if (!$mootyper->isexam) echo $exercise->exercisename; ?></h4>
<br />
<div style="float: left; padding-bottom: 10px;" id="textToEnter"></div><br />
<?php
            
        if ($mootyper->showkeyboard)
			$display_none = false;
        else
			$display_none = true;
		$keyboard = get_instance_layout_file($mootyper->layout);
		include ($keyboard);
?>
<br />
    <textarea name="tb1" wrap="off" id="tb1" class="tb1" onfocus="return focusSet(event)"  
            onpaste="return false" onselectstart="return false"
            onCopy="return false" onCut="return false" 
            onDrag="return false" onDrop="return false" autocomplete="off"><?php
        echo get_string('chere', 'mootyper') . '...'; ?></textarea>
                         
</div>
<div id="reportDiv" style="float: right; /*position: relative; right: 90px; top: 35px;*/">
<?php
        if (has_capability('mod/mootyper:viewgrades', context_module::instance($cm->id))) {
            $jlnk4 = $CFG->wwwroot . '/mod/mootyper/gview.php?id=' . $id . '&n=' . $mootyper->id;;
            echo '<a href="' . $jlnk4 . '">' . get_string('viewgrades', 'mootyper') . '</a><br /><br />';
        }

        if (has_capability('mod/mootyper:aftersetup', context_module::instance($cm->id))) {
            $jlnk6 = $CFG->wwwroot . "/mod/mootyper/mod_setup.php?n=" . $mootyper->id . "&e=1";
            echo '<a href="' . $jlnk6 . '">' . get_string('fsettings', 'mootyper') . '</a><br /><br />';
        }

        if (has_capability('mod/mootyper:viewmygrades', context_module::instance($cm->id))) {
            $jlnk7 = $CFG->wwwroot . "/mod/mootyper/owngrades.php?id=" . $id . "&n=" . $mootyper->id;
            echo '<a href="' . $jlnk7 . '">' . get_string('viewmygrades', 'mootyper') . '</a><br /><br />';
        }

?>
<input name='rpCourseId' type='hidden' value='<?php
        echo $course->id; ?>'>
<input name='rpSityperId' type='hidden' value='<?php
        echo $mootyper->id; ?>'>
<input name='rpUser' type='hidden' value='<?php
        echo $USER->id; ?>'>
<input name='rpExercise' type='hidden' value='<?php
        echo $exercise_ID; ?>'>
<input name='rpAttId' type='hidden' value=''>
<input name='rpFullHits' type='hidden' value=''>
<input name='rpGoal' type='hidden' value='<?php
        if (isset($reqiredGoal)) echo $reqiredGoal; ?>'>
    <input name='rpTimeInput' type='hidden'>
    <input name='rpMistakesInput' type='hidden'>
    <input name='rpAccInput' type='hidden'>
    <input name='rpSpeedInput' type='hidden'>
<div id="rdDiv2">
<strong><?php
        echo get_string('rtime', 'mootyper'); ?></strong> <span id="jsTime">0</span> s<br />
<strong><?php
        echo get_string('rprogress', 'mootyper'); ?></strong> <span id="jsProgress"> 0</span><br />
<strong><?php
        echo get_string('rmistakes', 'mootyper'); ?></strong> <span id="jsMistakes">0</span><br />
<strong><?php
        echo get_string('rprecision', 'mootyper'); ?></strong> <span id="jsAcc"> 0</span>%<br />
<strong><?php
        echo get_string('rhitspermin', 'mootyper'); ?></strong> <span id="jsSpeed">0</span><br />
<strong><?php
        echo get_string('wpm', 'mootyper'); ?></strong>: <span id="jsWpm">0</span>
<br />
</div>
<br /><input style="visibility: hidden;" id="btnContinue" name='btnContinue' type="submit" value=<?php
        echo "'" . get_string('fcontinue', 'mootyper') . "'"; ?>> 
</div>

</form>
</div>
<?php
        $textToInit = '';
        for ($it = 0; $it < strlen($textToEnter); $it++) {
            if ($textToEnter[$it] == "\n") $textToInit.= '\n';
            else
            if ($textToEnter[$it] == '"') $textToInit.= '\"';
            else
            if ($textToEnter[$it] == "\\") $textToInit.= '\\';
            else $textToInit.= $textToEnter[$it];
        }

        // initTextToEnter("'.$textToInit.'", 1, 3, 6, 1339333968);
        // (ttext, tinprogress, tmistakes, thits, tprogress, tstarttime)

        $record = get_last_check($mootyper->id);
        if (is_null($record)) {
            echo '<script type="text/javascript">initTextToEnter("' . $textToInit . '", 0, 0, 0, 0, 0, "' . $CFG->wwwroot . '", ' . $mootyper->showkeyboard . ');</script>';
        }
        else {
            echo '<script type="text/javascript">initTextToEnter("' . $textToInit . '", 1, ' . $record->mistakes . ', ' . $record->hits . ', ' . $record->timetaken . ', ' . $record->attemptid . ', "' . $CFG->wwwroot . '", ' . $mootyper->showkeyboard . ');</script>';
        }
    }
    else {
        echo get_string('endlesson', 'mootyper');
        echo "<br />";
        if (has_capability('mod/mootyper:viewgrades', context_module::instance($cm->id))) {
            $jlnk4 = $CFG->wwwroot . '/mod/mootyper/gview.php?id=' . $id . '&n=' . $mootyper->id;
            echo '<a href="' . $jlnk4 . '">' . get_string('viewgrades', 'mootyper') . '</a><br /><br />';
        }
        if (has_capability('mod/mootyper:viewmygrades', context_module::instance($cm->id))) {
            $jlnk7 = $CFG->wwwroot . "/mod/mootyper/owngrades.php?id=" . $id . "&n=" . $mootyper->id;
            echo '<a href="' . $jlnk7 . '">' . get_string('viewmygrades', 'mootyper') . '</a><br /><br />';
        }
    }
}
else {
    if (has_capability('mod/mootyper:setup', context_module::instance($cm->id))) {
        $vaLnk = $CFG->wwwroot . "/mod/mootyper/mod_setup.php?n=" . $mootyper->id;
        echo '<a href="' . $vaLnk . '">' . get_string('fsetup', 'mootyper') . '</a>';
    }
    else
		echo get_string('notreadyyet', 'mootyper');
}

// Trigger module viewed event.
$event = \mod_mootyper\event\course_module_viewed::create(array(
   'objectid' => $mootyper->id,
   'context' => $context
));
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('mootyper', $mootyper);
$event->trigger();

echo $OUTPUT->footer();
