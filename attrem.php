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
 * This file is used to remove a student attempt. Called from gview.php (View All Grades). 
 *
 * @package    mod
 * @subpackage mootyper
 * @copyright  2011 Jaka Luthar (jaka.luthar@gmail.com)
 * @copyright  2016 onwards AL Rachels (drachels@drachels.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');

global $DB;

if(isset($_GET['g'])){
	$gradeID = $_GET['g'];
	$db_grade = $DB->get_record('mootyper_grades', array('id' => $gradeID));
	$DB->delete_records('mootyper_attempts', array('id'=>$db_grade->attempt_id));
	$DB->delete_records('mootyper_grades', array('id'=>$db_grade->id));
}
$mID = $_GET['m_id'];
$cID = $_GET['c_id'];
$webDir = $CFG->wwwroot . '/mod/mootyper/gview.php?id='.$cID.'&n='.$mID;
header('Location: '.$webDir);

?>
