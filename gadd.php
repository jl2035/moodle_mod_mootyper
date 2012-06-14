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
 * @copyright  2011 Jaka Luthar (jaka.luthar@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');
global $DB;
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
$record->pass = 0;
$record->attemptid = $_POST['rpAttId'];
$chcks = $DB->get_records('mootyper_checks', array('attemptid' => $record->attemptid));
$att = $DB->get_record('mootyper_attempts', array('id' => $record->attemptid));
if(suspicion($chcks, $att->timetaken))
{
	$att_new = new stdClass();
	$att_new->id = $att->id;
	$att_new->mootyperid = $att->mootyperid;
	$att_new->userid = $att->userid;
	$att_new->timetaken = $att->timetaken;
	$att_new->inprogress = $att->inprogress;
	$att_new->suspision = 1;
	$DB->update_record('mootyper_attempts', $att_new);
}
$DB->insert_record('mootyper_grades', $record, false);
$webDir = $CFG->wwwroot . '/course/view.php?id='.$_POST['rpCourseId'];
header('Location: '.$webDir);
/*
    $protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') 
                    === FALSE ? 'http' : 'https';
    $host     = $_SERVER['HTTP_HOST'];
    $script   = $_SERVER['SCRIPT_NAME'];
    $params   = $_SERVER['QUERY_STRING'];
    $currentUrl = $protocol . '://' . $host . $script . '?' . $params;
    echo $currentUrl; 
 */
?>
