<?php

include_once dirname(__FILE__).DS.'HTMLField.php';

class HTMLRadio extends HTMLField {
	function getField() {
		$value = $this->getHTMLParam('value');
		
		if($value == ' value="1" ') {
		$field = '<input type="radio" '; 
		$field .= $this->getHTMLParam('name');
		$field .= $this->getHTMLParam('value');
		$field .= $this->getHTMLParam('class', 'css_class');
		$field .= ' checked /> Yes
		<input type="radio" ' . $this->getHTMLParam('name') . ' value="0" /> No'."\n";
		}
		else {
		$field = '<input type="radio" ' . $this->getHTMLParam('name') . ' value="1" /> Yes
		<input type="radio" ' . $this->getHTMLParam('name') . ' value="0" checked /> No'."\n";
		}
		return $field;
	}
} 