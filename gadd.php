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
 * @subpackage sityper
 * @copyright  2011 Jaka Luthar (jaka.luthar@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
global $DB;
$record = new stdClass();
$record->sityper = $_POST['rpSityperId'];
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
//http://localhost/moodle/course/view.php?id=2
$DB->insert_record('sityper_grades', $record, false);

/*$mg = new stdClass();
$mg->itemid = $_POST['rpSityperId'];
$mg->userid = $_POST['rpUser'];
$mg->rawgrade = $_POST['rpAccInput'];
$mg->rawgrademax = 100;
$mg->rawgrademin = 0;
$mg->rawscaleid = 1;
$mg->usermodified = time();
$mg->finalgrade = $_POST['rpAccInput'];
$mg->hidden = 0;
$mg->locked = 0;
$mg->locktime = 0;
$mg->exported = 0;
$mg->overriden = 0;
$mg->excluded = 0;
$mg->feedback = '-';
$mg->feedbackformat = 0;
$mg->information = 'MooTyper';
$mg->informationformat = 0;
$mg->timecreated = time();
$mg->timemodified = time();
$DB->insert_record('grade_grades', $mg, false);*/


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
