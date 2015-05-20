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
 *
*
* @package    local
* @subpackage actividadSocial
* @copyright  2015  Hans Jeria
* 					César Farías
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

require_once(dirname(__FILE__) . '/../../config.php');

global $PAGE, $CFG, $OUTPUT, $DB;

require_login();

$cmid = optional_param('cmid',0,PARAM_INT);
// action = {assign, quiz, resource}
// Desde el bloque nos dicen que contenido se desea ver
$action = optional_param('action','empty',PARAM_TEXT);


$url = new moodle_url('/local/actividadSocial/index.php');
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('standard');

$title ="Social Activity";

$PAGE->set_title($title);
$PAGE->set_heading($title);

echo $OUTPUT->header();
echo $OUTPUT->heading($title);

switch ($action){
	case "assign":		
			echo "<h1>Assing</h1>";
			$params = array(1,1,$cmid);		
			//Traer todas las tareas
			$sql_assing = "SELECT asub.id, a.name, us.firstname, us.lastname, asub.timecreated, asub.timemodified
						 	FROM {course_modules} as cm INNER JOIN {modules} as m ON (cm.module = m.id) 
						   		INNER JOIN {assign} as a ON (a.course = cm.course) 
    					   		INNER JOIN {assign_submission} as asub ON ( asub.assignment = a.id) 
    							INNER JOIN {user} as us ON (us.id = asub.userid) 
						 	WHERE m.name in ('assign') 
								AND cm.visible = ? 
    							AND m.visible = ?
    							AND cm.course = ?
							ORDER BY asub.timemodified DESC,asub.id";
			$lastassings = $DB->get_records_sql($sql_assing, $params);
			$table_assign = new html_table();
			// TODO: Conversar con el cliente si desea mostrar mayor información
			$table_assign->head = array('Name', 'User name', 'Date created','Date modified');
			foreach($lastassings as $assing){
				$timecreated = date('d-m-Y  H:i',$assing->timecreated);
				$timemodified = date('d-m-Y  H:i',$assing->timemodified);
				$table_assign->data[] = array($assing->name,$assing->firstname." ".$assing->lastname, $timecreated,$timemodified);
			}
			echo html_writer::table($table_assign);
			$buttonback = new moodle_url('../../course/view.php', array('id'=>$cmid));
			echo $OUTPUT->single_button($buttonback,"Back");
			
			break;
	case "quiz":		
			echo "<h1>Quiz</h1>";
			$params = array(1,1,$cmid);
			//Traer todas las tareas
			$sql_quiz = "SELECT qatt.id, q.name, us.firstname, us.lastname, qatt.timestart, qatt.timefinish
						 	FROM {course_modules} as cm INNER JOIN {modules} as m ON (cm.module = m.id)
						   		INNER JOIN {quiz} as q ON (q.course = cm.course)
    					   		INNER JOIN {quiz_attempts} as qatt ON ( qatt.quiz = q.id)
    							INNER JOIN {user} as us ON (us.id = qatt.userid)
						 	WHERE m.name in ('quiz')
								AND cm.visible = ?
    							AND m.visible = ?
    							AND cm.course = ?
    					  	ORDER BY qatt.timefinish DESC, qatt.id";
			$lastquiz = $DB->get_records_sql($sql_quiz, $params);
			$table_quiz = new html_table();
			// TODO: Conversar con el cliente si desea mostrar mayor información
			$table_quiz->head = array('Name', 'User name', ' Time start','Time finish');
			foreach($lastquiz as $quiz){
				$timestart = date('d-m-Y  H:i',$quiz->timestart);
				$timefinish = date('d-m-Y  H:i',$quiz->timefinish);
				$table_quiz->data[] = array($quiz->name,$quiz->firstname." ".$quiz->lastname,$timestart ,$timefinish);
			}
			echo html_writer::table($table_quiz);
			$buttonback = new moodle_url('../../course/view.php', array('id'=>$cmid));
			echo $OUTPUT->single_button($buttonback,"Back");
			break;			
	case "resource":
			echo "<h1>Resource</h1>";
			$params = array(1,1,$cmid);
			//Traer todos los recursos que se vieron
			$sql_resources = "SELECT log.id, r.name, us.firstname, us.lastname, log.timecreated
						 	FROM {course_modules} as cm INNER JOIN {modules} as m ON (cm.module = m.id)
						   		INNER JOIN {resource} as r ON (r.course = cm.course)
								INNER JOIN {logstore_standard_log} as log ON (log.objectid = r.id)
								INNER JOIN {user} as us ON (us.id = log.userid)
						 	WHERE m.name in ('resource')
								AND log.objecttable = 'resource'
								AND cm.visible = ?
    							AND m.visible = ?
    							AND cm.course = ?
    					  	ORDER BY log.timecreated DESC, log.id";
			$lastresources = $DB->get_records_sql($sql_resources, $params);
			$table_resource = new html_table();
			// TODO: Conversar con el cliente si desea mostrar mayor información
			$table_resource->head = array('Name', 'User name', 'Time view');
			foreach($lastresources as $resource){
				$timeview = date('d-m-Y  H:i',$resource->timecreated);
				$table_resource->data[] = array($resource->name,$resource->firstname." ".$resource->lastname, $timeview);
			}
			$lastresources = $DB->get_records_sql($sql_resources, $params);
			echo html_writer::table($table_resource);
			$buttonback = new moodle_url('../../course/view.php', array('id'=>$cmid));
			echo $OUTPUT->single_button($buttonback,"Back");
			break;
	default: echo "Invalid action";	
		
}


echo $OUTPUT->footer();

