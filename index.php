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
* @package    local
* @subpackage actividadSocial
* @copyright  2015  Hans Jeria (hansjeria@gmail.com)
* 			  2015 César Farías
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

require_once(dirname(__FILE__) . '/../../config.php');
global $PAGE, $CFG, $OUTPUT, $DB;
require_login();

// Variables enviadas por URL desde el bloque, esto depende del boton donde se hizo blick
// cmid es course module id, es decir, el curso donde el usuario estaba
$cmid = optional_param('cmid',0,PARAM_INT);
// action es que boton apreto el usuario en el bloque, este puede ser action = {assign, quiz, resource}
// por defecto es "empty", es decir que si no se llega desde el bloque el plugin no despliega información
$action = optional_param('action','empty',PARAM_TEXT);

// Construcción de la pagina en formato moodle
$url = new moodle_url('/local/actividadsocial/index.php');
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('standard');
$title = get_string('socac','local_actividadsocial');
$PAGE->set_title($title);
$PAGE->set_heading($title);
echo $OUTPUT->header();

// Depende del boton que hizo click el usuario en el bloque es el case que se ejecutara
switch ($action){
	case "assign":		
			echo html_writer::start_tag("h1").get_string('assign','local_actividadsocial').html_writer::end_tag("h1");
			$params = array(1,1,$cmid);		
			// Trae todas las tareas enviadas, ordenadas desde las mas nueva a las vieja
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
			// Consulta a la base de datos
			$lastassings = $DB->get_records_sql($sql_assing, $params);
			// Construcción de la tabla
			$table_assign = new html_table();
			$table_assign->head = array(get_string('name','local_actividadsocial'), 
										get_string('username','local_actividadsocial'), 
										get_string('cdate','local_actividadsocial'),
										get_string('mdate','local_actividadsocial')
			);
			foreach($lastassings as $assing){
				$timecreated = date('d-m-Y  H:i',$assing->timecreated);
				$timemodified = date('d-m-Y  H:i',$assing->timemodified);
				$table_assign->data[] = array($assing->name,$assing->firstname." ".$assing->lastname, $timecreated,$timemodified);
			}
			// se imprime en pantalla la tabla con lo datos y un boton que permite volver al curso desde el cual se hizo click en el bloque
			echo html_writer::table($table_assign);
			$buttonback = new moodle_url('../../course/view.php', array('id'=>$cmid));
			echo $OUTPUT->single_button($buttonback, get_string('back','local_actividadsocial'));
			break;
			
	case "quiz":		
			echo html_writer::start_tag("h1").get_string('quiz','local_actividadsocial').html_writer::end_tag("h1");
			$params = array(1,1,$cmid);
			//Trae todos lo quizes terminados desde el mas nuevo al mas antiguo
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
			// Consulta a la base de datos
			$lastquiz = $DB->get_records_sql($sql_quiz, $params);
			//Creacion de la tabla
			$table_quiz = new html_table();
			$table_quiz->head = array(get_string('name','local_actividadsocial'),
									get_string('username','local_actividadsocial'),
									get_string('starttime','local_actividadsocial'),
									get_string('end.time','local_actividadsocial')
			);
			foreach($lastquiz as $quiz){
				$timestart = date('d-m-Y  H:i',$quiz->timestart);
				$timefinish = date('d-m-Y  H:i',$quiz->timefinish);
				$table_quiz->data[] = array($quiz->name,$quiz->firstname." ".$quiz->lastname,$timestart ,$timefinish);
			}
			// se imprime en pantalla la tabla con lo datos y un boton que permite volver al curso desde el cual se hizo click en el bloque
			echo html_writer::table($table_quiz);
			$buttonback = new moodle_url('../../course/view.php', array('id'=>$cmid));
			echo $OUTPUT->single_button($buttonback, get_string('back','local_actividadsocial'));
			break;	
					
	case "resource":
			echo html_writer::start_tag("h1").get_string('resources','local_actividadsocial').html_writer::end_tag("h1");
			$params = array(1,1,$cmid);
			//Trae todos lo recursos que fueron descargados ordenados del mas nuevo al mas antiguo
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
			// Consulta a la base de datos
			$lastresources = $DB->get_records_sql($sql_resources, $params);
			// Creacion de la tabla
			$table_resource = new html_table();
			$table_resource->head = array(get_string('name','local_actividadsocial'), 
										get_string('username','local_actividadsocial'), 
										get_string('timeview','local_actividadsocial')
			);
			foreach($lastresources as $resource){
				$timeview = date('d-m-Y  H:i',$resource->timecreated);
				$table_resource->data[] = array($resource->name,$resource->firstname." ".$resource->lastname, $timeview);
			}
			$lastresources = $DB->get_records_sql($sql_resources, $params);
			// se imprime en pantalla la tabla con lo datos y un boton que permite volver al curso desde el cual se hizo click en el bloque
			echo html_writer::table($table_resource);
			$buttonback = new moodle_url('../../course/view.php', array('id'=>$cmid));
			echo $OUTPUT->single_button($buttonback, get_string('back','local_actividadsocial'));
			break;
	// Si no ejecuta ningun case se muestra el mensaje de acceso invalido a la pagina	
	default: echo get_string('invac','local_actividadsocial');	
		
}

echo $OUTPUT->footer();

