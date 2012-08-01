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
 * This file replaces the legacy STATEMENTS section in db/install.xml,
 * lib.php/modulename_install() post installation hook and partially defaults.php
 *
 * @package    mod
 * @subpackage mootyper
 * @copyright  2012 Jaka Luthar (jaka.luthar@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Post installation procedure
 *
 * @see upgrade_plugins_modules()
 */
function xmldb_mootyper_install() {
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
	global $CFG;
	$pth = $CFG->dirroot."/mod/mootyper/lessons";
	$res = scandir($pth);
	for($i=0; $i<count($res); $i++)
	{
		if(is_file($pth."/".$res[$i])){
			$fl = $res[$i];
			read_lessons_file($fl);
		}
	}
	$pth2 = $CFG->dirroot."/mod/mootyper/layouts";
	$res2 = scandir($pth2);
	for($j=0; $j<count($res2); $j++)
	{
		if(is_file($pth2."/".$res2[$j]) && ( substr($res2[$j], (strripos($res2[$j], '.') + 1) ) == 'php'))
		{
			$fl2 = $res2[$j];
			add_keyboard_layout($fl2);
		}
	}
}

function add_keyboard_layout($daFile)
{
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
	global $DB, $CFG;
	$theFile = $CFG->dirroot."/mod/mootyper/layouts/".$daFile;
	$wwwFile = $CFG->wwwroot."/mod/mootyper/layouts/".$daFile;
	$record = new stdClass();
	$pikapos = strrpos($daFile, '.');
	$layoutName = substr($daFile, 0, $pikapos);
    $record->filepath = $theFile;
    $record->name = $layoutName;
    $record->jspath = substr($wwwFile, 0, strripos($wwwFile, '.')).'.js';
    $DB->insert_record('mootyper_layouts', $record, true);
}

function read_lessons_file($daFile)
{
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
	global $DB, $CFG;
	$theFile = $CFG->dirroot."/mod/mootyper/lessons/".$daFile;
	//echo $theFile;
	
	$record = new stdClass();
	$pikapos = strrpos($daFile, '.');
	$lessonName = substr($daFile, 0, $pikapos);
	//echo $lessonName;
    $record->lessonname = $lessonName;
    $lesson_id = $DB->insert_record('mootyper_lessons', $record, true);
	$fh = fopen($theFile, 'r');
	$theData = fread($fh, filesize($theFile));
	fclose($fh);
	$haha = "";
	for($i=0; $i<strlen($theData); $i++)
		$haha.=$theData[$i];
	$haha = trim($haha);
	$splitted = explode ('/**/' , $haha);
	for($j=0; $j<count($splitted); $j++)
	{
		$vaja = trim($splitted[$j]);
		$nm = "".($j+1);
		$textToType = "";
		for($k=0; $k<strlen($vaja); $k++)
		{
			$ch = $vaja[$k];
			/*if($ch == "\n")
				$textToType .= '\n';
			else*/
				$textToType .= $ch;
		}
		$erecord = new stdClass();
		$erecord->texttotype = $textToType;
		$erecord->exercisename = $nm;
		$erecord->lesson = $lesson_id;
		$erecord->snumber = $j+1;
		$DB->insert_record('mootyper_exercises', $erecord, false);
	}
}

/**
 * Post installation recovery procedure
 *
 * @see upgrade_plugins_modules()
 */
function xmldb_mootyper_install_recovery() {
}
