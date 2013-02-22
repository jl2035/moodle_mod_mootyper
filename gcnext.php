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
 * @package    mod
 * @subpackage mootyper
 * @copyright  2012 Jaka Luthar (jaka.luthar@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');
global $DB;
if($_POST['rpAccInput'] >= $_POST['rpGoal']) 
	$passField = 1;
else
	$passField = 0;
$record = new stdClass();
$record->mootyper = $_POST['rpSityperId'];
$record->userid = $_POST['rpUser'];
$record->grade = 0;
$record->mistakes = $_POST['rpMistakesInput'];
$record->timeinseconds = $_POST['rpTimeInput'];
$record->hitsperminute = $_POST['rpSpeedInput'];
$record->fullhits = $_POST['rpFullHits'];
$record->precisionfield = $_POST['rpAccInput'];
$record->timetaken = time();
$record->exercise = $_POST['rpExercise'];
$record->pass = $passField;
$record->attemptid = $_POST['rpAttId'];
//$y = ($record->timeinseconds*100) / 60;
//$wpm = ($_GET['words'] * 100) / $y;
//$record->wpm = $wpm - $record->mistakes;
$record->wpm = ($record->hitsperminute / 5) - $record->mistakes;
$DB->insert_record('mootyper_grades', $record, false);
$webDir = $CFG->wwwroot . '/mod/mootyper/view.php?n='.$_POST['rpSityperId'];
echo '<script type="text/javascript">window.location="'.$webDir.'";</script>';
?>
