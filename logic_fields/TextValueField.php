<?php
include_once(DM_PATH.DS."logic_fields".DS."LogicField.php");

class TextValueField extends LogicField {
	function getField() {
		$value = $this->getParam('value');

		return $value;
	}
	function getValue() {
		$value = $this->getParam('value');
		return htmlspecialchars($value, ENT_QUOTES);
	}
}
