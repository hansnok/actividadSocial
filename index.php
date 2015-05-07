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
* @copyright  2015 C�sar Far�as
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/



require_once(dirname(__FILE__) . '/../../config.php'); //obligatorio
require_once($CFG->dirroot.'/local/actividadSocial/forms/form.php');
require_once($CFG->dirroot.'/local/reservasalas/tablas.php');


global $PAGE, $CFG, $OUTPUT, $DB;
require_login();
$url = new moodle_url('/local/actividadSocial/index.php');
$context = context_system::instance();//context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('standard');
$title ="form lindo";
$PAGE->set_title($title);
$PAGE->set_heading($title);
echo $OUTPUT->header();
echo $OUTPUT->heading($title);

$formulario=new simplehtml_form();
if($fromform = $formulario->get_data()){
	echo $fromform->email;
}else{
	
}

$formulario->display();

$table = new html_table();

$table->head = array("Nombre",
					"Año",
					"Color");

$row = new html_table_row(array("Kia Morning",
						"2012",
						"Negro"));
$table->data[] = $row;

$row = new html_table_row(array("Toyota Yaris",
						"2014",
						"Rojo"));
$table->data[] = $row;

$row = new html_table_row(array("Subaru Outback",
						"2009",
						"Gris perla"));
$table->data[] = $row;

$row = new html_table_row(array("Mazda 3",
						"2011",
						"Azul"));
$table->data[] = $row;

echo html_writer::table($table);

echo $OUTPUT->footer();

