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
global $DB;
$record = new stdClass();
$st = $_GET['status'];
if($st == 1){
	
	$record->mootyperid = $_GET['mootyperid'];
	$record->userid = $_GET['userid'];
	$record->timetaken = $_GET['time'];
	$record->inprogress = 1;
	$record->suspicion = 0;
    $newID = $DB->insert_record('mootyper_attempts', $record, true);
    echo $newID;
}
else if($st == 2)
{
	$record->attemptid = $_GET['attemptid'];
	$record->mistakes = $_GET['mistakes'];
	$record->hits = $_GET['hits'];
	$record->checktime = time();
	$DB->insert_record('mootyper_checks', $record, false);	
}
else if($st == 3)
{//'id', 0, PARAM_INT);
	$att_id = optional_param('attemptid', 0, PARAM_INT);
	$DB->delete_records('mootyper_checks', array('attemptid' => $att_id));
	$attemptOLD = $DB->get_record('mootyper_attempts', array('id' => $att_id), '*', MUST_EXIST);
	$attemptNEW = new stdClass();
	$attemptNEW->id = $attemptOLD->id;
	$attemptNEW->mootyperid = $attemptOLD->mootyperid;
	$attemptNEW->userid = $attemptOLD->userid;
	$attemptNEW->timetaken = $attemptOLD->timetaken;
	$attemptNEW->inprogress = 0;
	$attemptNEW->suspicion = $attemptOLD->suspicion;
	$DB->update_record('mootyper_attempts', $attemptNEW);
}
?>



