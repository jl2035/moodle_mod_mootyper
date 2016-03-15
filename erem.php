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
 * This file is used to remove an exercise from a category.
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod
 * @subpackage mootyper
 * @copyright  2011 Jaka Luthar (jaka.luthar@gmail.com)
 * @copyright  2016 onwards AL Rachels (drachels@drachels.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');

global $DB;

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
if ($id){
    $course     = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
}
else
    error('You must specify a course_module ID or an instance ID');

$context = context_course::instance($id);

if(isset($_GET['r'])){
	$exerciseID = $_GET['r'];
	$DB->delete_records('mootyper_exercises', array('id'=>$exerciseID));
}
else
{
	$lessonID = $_GET['l'];
	$DB->delete_records('mootyper_exercises', array('lesson' =>$lessonID));
	$DB->delete_records('mootyper_lessons', array('id' => $lessonID));
}

// Trigger module exercise_removed event.
$event = \mod_mootyper\event\exercise_removed::create(array(
	'objectid' => $course->id,
	'context' => $context
));
$event->trigger();

$cID = $_GET['id'];
$webDir = $CFG->wwwroot . '/mod/mootyper/exercises.php?id='.$cID;
header('Location: '.$webDir);

?>
