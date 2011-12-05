<?php
include_once(DM_PATH.DS."logic_fields".DS."LogicField.php");
include_once(DM_PATH.DS."html_fields".DS."HTMLRadio.php");

class RadioLogicField extends LogicField {
	function getField() {
		$value = $this->getParam('value');
		$field_obj = new HTMLRadio();
		$field_obj->addParams($this->params);
		$field = $field_obj->getField();

		return $field;
	}
	
	function getValue() {
		$value = $this->getParam('value');
		return htmlspecialchars($value, ENT_QUOTES);
	}

}
