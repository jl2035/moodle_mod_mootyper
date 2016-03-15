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
 * Library of interface functions and constants for module mootyper
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the mootyper specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    mod
 * @subpackage mootyper
 * @copyright  2012 Jaka Luthar (jaka.luthar@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/** example constant */
//define('NEWMODULE_ULTIMATE_ANSWER', 42);

////////////////////////////////////////////////////////////////////////////////
// Moodle core API                                                            //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the information on whether the module supports a feature
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function mootyper_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_INTRO:         return true;
        default:                        return null;
    }
}

function get_users_of_one_instance($mootyperID)
{
    global $DB, $CFG;
    $params = array();
    $toReturn = array();
    $gradesTblName = $CFG->prefix."mootyper_grades";
    $usersTblName = $CFG->prefix."user";
    $sql = "SELECT DISTINCT ".$usersTblName.".firstname, ".$usersTblName.".lastname, ".$usersTblName.".id".
    " FROM ".$gradesTblName.
    " LEFT JOIN ".$usersTblName." ON ".$gradesTblName.".userid = ".$usersTblName.".id".
    " WHERE (mootyper=".$mootyperID.")";
    if ($grades = $DB->get_records_sql($sql, $params)) {
        return $grades;
	}
    return FALSE;
}

function get_typer_grades_adv($mootyperID, $exerciseID, $userID=0, $orderby=-1, $desc=FALSE)
{
	global $DB, $CFG;
    $params = array();
    $toReturn = array();
    $gradesTblName = $CFG->prefix."mootyper_grades";
    $usersTblName = $CFG->prefix."user";
    $exerTblName = $CFG->prefix."mootyper_exercises";
    $attTblName = $CFG->prefix."mootyper_attempts";
    $sql = "SELECT ".$gradesTblName.".id, ".$usersTblName.".firstname, ".$usersTblName.".lastname, ".$usersTblName.".id as u_id, ".$gradesTblName.".pass, ".
    $gradesTblName.".mistakes, ".$gradesTblName.".timeinseconds, ".$gradesTblName.".hitsperminute, ".$attTblName.".suspicion, ".
    $gradesTblName.".fullhits, ".$gradesTblName.".precisionfield, ".$gradesTblName.".timetaken, ".$exerTblName.".exercisename, ".$gradesTblName.".wpm".
    " FROM ".$gradesTblName.
    " LEFT JOIN ".$usersTblName." ON ".$gradesTblName.".userid = ".$usersTblName.".id".
    " LEFT JOIN ".$exerTblName." ON ".$gradesTblName.".exercise = ".$exerTblName.".id".
    " LEFT JOIN ".$attTblName." ON ".$attTblName.".id = ".$gradesTblName.".attemptid".
    " WHERE (mootyper=".$mootyperID.") AND (exercise=".$exerciseID." OR ".$exerciseID."=0) AND".
    " (".$gradesTblName.".userid=".$userID." OR ".$userID."=0)";
    if($orderby==0 || $orderby ==-1)
		$oby = " ORDER BY ".$gradesTblName.".id";
	else if($orderby==1)
		$oby = " ORDER BY ".$usersTblName.".firstname";
    else if($orderby==2)
		$oby = " ORDER BY ".$usersTblName.".lastname";
	else if($orderby==3)
		$oby = " ORDER BY ".$attTblName.".suspicion";
    else if($orderby==4)
		$oby = " ORDER BY ".$gradesTblName.".mistakes";
	else if($orderby==5)
		$oby = " ORDER BY ".$gradesTblName.".timeinseconds";
    else if($orderby==6)
		$oby = " ORDER BY ".$gradesTblName.".hitsperminute";
	else if($orderby==7)
		$oby = " ORDER BY ".$gradesTblName.".fullhits";
	else if($orderby==8)
		$oby = " ORDER BY ".$gradesTblName.".precisionfield";
	else if($orderby==9)
		$oby = " ORDER BY ".$gradesTblName.".timetaken";
	else if($orderby==10)
		$oby = " ORDER BY ".$exerTblName.".exercisename";
    else if($orderby==11)
		$oby = " ORDER BY ".$gradesTblName.".pass";
	else if($orderby==12)
		$oby = " ORDER BY ".$gradesTblName.".wpm";
    else
		$oby = "";
	$sql .= $oby;
	if($desc)
		$sql .= " DESC";
    if ($grades = $DB->get_records_sql($sql, $params)) {
        return $grades;
	}
    return FALSE;
}

function get_grades_average($grads)
{
	$povprecje = array();
	$cnt = count($grads);
	$povprecje['mistakes'] = 0;
	$povprecje['timeinseconds'] = 0;
	$povprecje['hitsperminute'] = 0;
	$povprecje['precision'] = 0;
	foreach($grads as $grade)
	{
		$povprecje['mistakes'] = $povprecje['mistakes'] + $grade->mistakes; 
		$povprecje['timeinseconds']  = $povprecje['timeinseconds'] + $grade->timeinseconds;
	}
	if($cnt != 0){
		$povprecje['mistakes'] = $povprecje['mistakes'] / $cnt;
		$povprecje['timeinseconds'] = $povprecje['timeinseconds'] / $cnt;
	}
	return $povprecje;
}

function get_typergradesfull($s_id, $orderby=-1, $desc=FALSE) {
    global $DB, $CFG;
    $params = array();
    $toReturn = array();
    $gradesTblName = $CFG->prefix."mootyper_grades";
    $usersTblName = $CFG->prefix."user";
    $exerTblName = $CFG->prefix."mootyper_exercises";
    $attTblName = $CFG->prefix."mootyper_attempts";
    $sql = "SELECT ".$gradesTblName.".id, ".$usersTblName.".firstname, ".$usersTblName.".lastname, ".$usersTblName.".id as u_id, ".$attTblName.".suspicion, ".
    $gradesTblName.".mistakes, ".$gradesTblName.".timeinseconds, ".$gradesTblName.".hitsperminute, ".
    $gradesTblName.".fullhits, ".$gradesTblName.".precisionfield, ".$gradesTblName.".timetaken, ".$exerTblName.".exercisename, ".$gradesTblName.".wpm".
    " FROM ".$gradesTblName.
    " LEFT JOIN ".$usersTblName." ON ".$gradesTblName.".userid = ".$usersTblName.".id".
    " LEFT JOIN ".$exerTblName." ON ".$gradesTblName.".exercise = ".$exerTblName.".id".
    " LEFT JOIN ".$attTblName." ON ".$attTblName.".id = ".$gradesTblName.".attemptid".
    " WHERE mootyper=".$s_id;
    if($orderby==0 || $orderby ==-1)
		$oby = " ORDER BY ".$gradesTblName.".id";
	else if($orderby==1)
		$oby = " ORDER BY ".$usersTblName.".firstname";
    else if($orderby==2)
		$oby = " ORDER BY ".$usersTblName.".lastname";
	else if($orderby==3)
		$oby = " ORDER BY ".$attTblName.".suspicion";
    else if($orderby==4)
		$oby = " ORDER BY ".$gradesTblName.".mistakes";
	else if($orderby==5)
		$oby = " ORDER BY ".$gradesTblName.".timeinseconds";
    else if($orderby==6)
		$oby = " ORDER BY ".$gradesTblName.".hitsperminute";
	else if($orderby==7)
		$oby = " ORDER BY ".$gradesTblName.".fullhits";
	else if($orderby==8)
		$oby = " ORDER BY ".$gradesTblName.".precisionfield";
	else if($orderby==9)
		$oby = " ORDER BY ".$gradesTblName.".timetaken";
	else if($orderby==10)
		$oby = " ORDER BY ".$exerTblName.".exercisename";
	else if($orderby==12)
		$oby = " ORDER BY ".$gradesTblName.".wpm";
    else
		$oby = "";
	$sql .= $oby;
	if($desc)
		$sql .= " DESC";
    if ($grades = $DB->get_records_sql($sql, $params)) {
        return $grades;
	}
    return FALSE;
    //select Message.ID, Message.FromUser, User.PhoneNum, User.Nick, Message.Contents, Message.Type from Message left join User on Message.FromUser = User.ID where Message.ID=".$msgID"
}

function get_typergradesuser($s_id, $u_id, $orderby=-1, $desc=FALSE) {
    global $DB, $CFG;
    $params = array();
    $toReturn = array();
    $gradesTblName = $CFG->prefix."mootyper_grades";
    $usersTblName = $CFG->prefix."user";
    $exerTblName = $CFG->prefix."mootyper_exercises";
    $attTblName = $CFG->prefix."mootyper_attempts";
    $sql = "SELECT ".$gradesTblName.".id, ".$usersTblName.".firstname, ".$usersTblName.".lastname, ".$attTblName.".suspicion, ".
    $gradesTblName.".mistakes, ".$gradesTblName.".timeinseconds, ".$gradesTblName.".hitsperminute, ".
    $gradesTblName.".fullhits, ".$gradesTblName.".precisionfield, ".$gradesTblName.".pass, ".$gradesTblName.".timetaken, ".$exerTblName.".exercisename, ".$gradesTblName.".wpm".
    " FROM ".$gradesTblName.
    " LEFT JOIN ".$usersTblName." ON ".$gradesTblName.".userid = ".$usersTblName.".id".
    " LEFT JOIN ".$exerTblName." ON ".$gradesTblName.".exercise = ".$exerTblName.".id".
    " LEFT JOIN ".$attTblName." ON ".$attTblName.".id = ".$gradesTblName.".attemptid".
    " WHERE mootyper=".$s_id." AND ".$gradesTblName.".userid=".$u_id;
    if($orderby==0 || $orderby ==-1)
		$oby = " ORDER BY ".$gradesTblName.".id";
	else if($orderby==1)
		$oby = " ORDER BY ".$usersTblName.".firstname";
    else if($orderby==2)
		$oby = " ORDER BY ".$usersTblName.".lastname";
	else if($orderby==3)
		$oby = " ORDER BY ".$attTblName.".suspicion";
    else if($orderby==4)
		$oby = " ORDER BY ".$gradesTblName.".mistakes";
	else if($orderby==5)
		$oby = " ORDER BY ".$gradesTblName.".timeinseconds";
    else if($orderby==6)
		$oby = " ORDER BY ".$gradesTblName.".hitsperminute";
	else if($orderby==7)
		$oby = " ORDER BY ".$gradesTblName.".fullhits";
	else if($orderby==8)
		$oby = " ORDER BY ".$gradesTblName.".precisionfield";
	else if($orderby==9)
		$oby = " ORDER BY ".$gradesTblName.".timetaken";
	else if($orderby==10)
		$oby = " ORDER BY ".$exerTblName.".exercisename";
	else if($orderby==12)
		$oby = " ORDER BY ".$gradesTblName.".wpm";
    else
		$oby = "";
	$sql .= $oby;
	if($desc)
		$sql .= " DESC";
    if ($grades = $DB->get_records_sql($sql, $params)) {
        return $grades;
	}
    return FALSE;
    //select Message.ID, Message.FromUser, User.PhoneNum, User.Nick, Message.Contents, Message.Type from Message left join User on Message.FromUser = User.ID where Message.ID=".$msgID"
}


/**
 * Saves a new instance of the mootyper into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $mootyper An object from the form in mod_form.php
 * @param mod_mootyper_mod_form $mform
 * @return int The id of the newly inserted mootyper record
 */
function mootyper_add_instance(stdClass $mootyper, mod_mootyper_mod_form $mform = null) {
    global $DB;
    $mootyper->timecreated = time();
    return $DB->insert_record('mootyper', $mootyper);
}


function get_exercise_record($eid)
{
	global $DB;
    return $DB->get_record('mootyper_exercises', array('id' => $eid));
}

function exam_already_done($mootyper, $user_id)
{
	global $DB;
	$table = 'mootyper_grades';
    $select = 'userid='.$user_id.' AND mootyper='.$mootyper->id; //is put into the where clause
    $result = $DB->get_records_select($table,$select);
    if(!is_null($result) && count($result) > 0){
		return true;
	}
	return false;
}

function get_exercise_from_mootyper($mootyper_id, $lesson_id, $user_id)
{
	global $DB;
	$table = 'mootyper_grades';
    $select = 'userid='.$user_id.' AND mootyper='.$mootyper_id.' AND pass=1'; //is put into the where clause
    $result = $DB->get_records_select($table,$select);
    if(!is_null($result) && count($result) > 0){
		$max = 0;
		//$maxID = 0;
		foreach($result as $grd)
		{
			$exRec = $DB->get_record('mootyper_exercises', array('id' => $grd->exercise));
			$zap_st = $exRec->snumber;
			if($zap_st > $max){
				$max = $zap_st;
				//$maxID = $exRec->id;
			}
		}
		return $DB->get_record('mootyper_exercises', array('snumber' => ($max+1), 'lesson' => $lesson_id));
	}
	else
		return $DB->get_record('mootyper_exercises', array('snumber' => 1, 'lesson' => $lesson_id));
}

function jget_mootyper_record($sid)
{
	//get_record('mootyper', array('id' => $n));
	global $DB;
    return $DB->get_record('mootyper', array('id' => $sid));
}
/**
 * Updates an instance of the mootyper in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $mootyper An object from the form in mod_form.php
 * @param mod_mootyper_mod_form $mform
 * @return boolean Success/Fail
 */
function mootyper_update_instance(stdClass $mootyper, mod_mootyper_mod_form $mform = null) {
    global $DB;
    $mootyper->timemodified = time();
    //$old = $DB->get_record('mootyper', array('id' => $mootyper->instance));
    $mootyper->id = $mootyper->instance;
    return $DB->update_record('mootyper', $mootyper);
}

/**
 * Removes an instance of the mootyper from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function mootyper_delete_instance($id) {
	global $DB;
    $mootyper = $DB->get_record('mootyper', array('id' => $id), '*', MUST_EXIST);
    mootyper_delete_all_grades($mootyper);
    if (! $mootyper = $DB->get_record('mootyper', array('id' => $id))) {
        return false;
    }
    mootyper_delete_all_checks($id);
    $DB->delete_records('mootyper_attempts', array('mootyperid' => $id));
    $DB->delete_records('mootyper', array('id' => $mootyper->id));
    return true;
}

function mootyper_delete_all_checks($m_id)
{
	global $DB;
	$rcs = $DB->get_records('mootyper_attempts', array('mootyperid' => $m_id));
	foreach($rcs as $at)
		$DB->delete_records('mootyper_checks', array('attemptid' => $at->id));
}

function mootyper_delete_all_grades($mootyper) {
    //global $CFG, $DB;
    global $DB;
    //require_once($CFG->dirroot . '/mod/mootyper/locallib.php');
    //question_engine::delete_questions_usage_by_activities(new qubaids_for_yyyyyy($yyyyyyyyyyyy->id));
    $DB->delete_records('mootyper_grades', array('mootyper' => $mootyper->id));
}





/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return stdClass|null
 */
function mootyper_user_outline($course, $user, $mod, $mootyper) {

    $return = new stdClass();
    $return->time = 0;
    $return->info = 'This is an outline.';
    return $return;
}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param stdClass $course the current course record
 * @param stdClass $user the record of the user we are generating report for
 * @param cm_info $mod course module info
 * @param stdClass $mootyper the module instance record
 * @return void, is supposed to echp directly
 */
function mootyper_user_complete($course, $user, $mod, $mootyper) {
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in mootyper activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 */
function mootyper_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link mootyper_print_recent_mod_activity()}.
 *
 * @param array $activities sequentially indexed array of objects with the 'cmid' property
 * @param int $index the index in the $activities to use for the next record
 * @param int $timestart append activity since this time
 * @param int $courseid the id of the course we produce the report for
 * @param int $cmid course module id
 * @param int $userid check for a particular user's activity only, defaults to 0 (all users)
 * @param int $groupid check for a particular group's activity only, defaults to 0 (all groups)
 * @return void adds items into $activities and increases $index
 */
function mootyper_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0) {
}

/**
 * Prints single activity item prepared by {@see mootyper_get_recent_mod_activity()}

 * @return void
 */
function mootyper_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function mootyper_cron () {
    return true;
}

/**
 * Returns an array of users who are participanting in this mootyper
 *
 * Must return an array of users who are participants for a given instance
 * of mootyper. Must include every user involved in the instance,
 * independient of his role (student, teacher, admin...). The returned
 * objects must contain at least id property.
 * See other modules as example.
 *
 * @param int $mootyperid ID of an instance of this module
 * @return boolean|array false if no participants, array of objects otherwise
 */
function mootyper_get_participants($mootyperid) {
    return false;
}

/**
 * Returns all other caps used in the module
 *
 * @example return array('moodle/site:accessallgroups');
 * @return array
 */
function mootyper_get_extra_capabilities() {
    return array();
}

////////////////////////////////////////////////////////////////////////////////
// Gradebook API                                                              //
////////////////////////////////////////////////////////////////////////////////

/**
 * Is a given scale used by the instance of mootyper?
 *
 * This function returns if a scale is being used by one mootyper
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $mootyperid ID of an instance of this module
 * @return bool true if the scale is used by the given mootyper instance
 */
function mootyper_scale_used($mootyperid, $scaleid) {
    /**global $DB;

     @example 
    if ($scaleid and $DB->record_exists('mootyper', array('id' => $mootyperid, 'grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }*/
    return false;
}

/**
 * Checks if scale is being used by any instance of mootyper.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param $scaleid int
 * @return boolean true if the scale is used by any mootyper instance
 */
function mootyper_scale_used_anywhere($scaleid) {
    /**global $DB;

     @example 
    if ($scaleid and $DB->record_exists('mootyper', array('grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }*/
    return false;
}

/**
 * Creates or updates grade item for the give mootyper instance
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $mootyper instance object with extra cmidnumber and modname property
 * @return void
 */
function mootyper_grade_item_update(stdClass $mootyper) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    /** @example */
    $item = array();
    $item['itemname'] = clean_param($mootyper->name, PARAM_NOTAGS);
    $item['gradetype'] = GRADE_TYPE_VALUE;
    $item['grademax']  = $mootyper->grade;
    $item['grademin']  = 0;

    grade_update('mod/mootyper', $mootyper->course, 'mod', 'mootyper', $mootyper->id, 0, null, $item);
}

/**
 * Update mootyper grades in the gradebook
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $mootyper instance object with extra cmidnumber and modname property
 * @param int $userid update grade of specific user only, 0 means all participants
 * @return void
 */
function mootyper_update_grades(stdClass $mootyper, $userid = 0) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    /** @example */
    $grades = array(); // populate array of grade objects indexed by userid

    grade_update('mod/mootyper', $mootyper->course, 'mod', 'mootyper', $mootyper->id, 0, $grades);
}

////////////////////////////////////////////////////////////////////////////////
// File API                                                                   //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */
function mootyper_get_file_areas($course, $cm, $context) {
    return array();
}

/**
 * Serves the files from the mootyper file areas
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @return void this should never return to the caller
 */
function mootyper_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload) {
    global $DB, $CFG;

    if ($context->contextlevel != CONTEXT_MODULE) {
        send_file_not_found();
    }

    require_login($course, true, $cm);

    send_file_not_found();
}

////////////////////////////////////////////////////////////////////////////////
// Navigation API                                                             //
////////////////////////////////////////////////////////////////////////////////

/**
 * Extends the global navigation tree by adding mootyper nodes if there is a relevant content
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $navref An object representing the navigation tree node of the mootyper module instance
 * @param stdClass $course
 * @param stdClass $module
 * @param cm_info $cm
 */
//function mootyper_extend_navigation(navigation_node $navref, stdclass $course, stdclass $module, cm_info $cm) {
//}

/**
 * Extends the settings navigation with the mootyper settings
 *
 * This function is called when the context for the page is a mootyper module. This is not called by AJAX
 * so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav {@link settings_navigation}
 * @param navigation_node $mootypernode {@link navigation_node}
 */
function mootyper_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $mootypernode=null) {
}
