<?php 
function array_to_csv_download($array, $filename = "export.csv", $delimiter=";") {
    header('Content-Type: application/csv');
    header('Content-Disposition: attachement; filename="'.$filename.'";');
	header("Pragma: no-cache");
	header("Expires: 0");
    $f = fopen('php://output', 'w');
    $headings = array(get_string('student', 'mootyper'),
                      get_string('vmistakes', 'mootyper'),
                      get_string('timeinseconds', 'mootyper'),
                      get_string('hitsperminute', 'mootyper'),
                      get_string('fullhits', 'mootyper'),
                      get_string('precision', 'mootyper'),
                      get_string('timetaken', 'mootyper'),
                      get_string('wpm', 'mootyper'));
    fputcsv($f, $headings, $delimiter);
    foreach ($array as $gr) {
		$fields = array($gr->firstname.' '.$gr->lastname, $gr->mistakes, format_time($gr->timeinseconds), format_float($gr->hitsperminute), $gr->fullhits, format_float($gr->precisionfield).'%', date('d. M Y G:i', $gr->timetaken), $gr->wpm);
		fputcsv($f, $fields, $delimiter);		
    }
    fclose($f);
} 

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

$m_id = optional_param('mootyperid', 0, PARAM_INT);
$m_is_exam = optional_param('isexam', 0, PARAM_INT);
if($m_is_exam)
	$grds = get_typergradesfull($m_id, 2, 0);
else
	$grds = get_typer_grades_adv($m_id, 0, 0, 2, 0);

array_to_csv_download($grds, get_string('gradesfilename', 'mootyper'));
