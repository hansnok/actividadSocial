<?php
//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");

class simplehtml_form extends moodleform {
	//Add elements to form
	public function definition() {
		global $CFG;

		$mform = $this->_form; // Don't forget the underscore!

		$mform->addElement('text', 'email', 'email'); // Add elements to your form
		$mform->setType('email', PARAM_NOTAGS);                   //Set type of element
		$mform->setDefault('email', 'Please enter email');        //Default value
	$buttonarray=array();
$buttonarray[] = &$mform->createElement('submit', 'submitbutton', 'savechanges');
$buttonarray[] = &$mform->createElement('reset', 'resetbutton', 'revert');
$buttonarray[] = &$mform->createElement('cancel');
$mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
$mform->closeHeaderBefore('buttonar');
	}
	
	//Custom validation should be added here
	function validation($data, $files) {
		return array();
	}
}
