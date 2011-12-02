<?php

include_once DM_PATH.DS.'classes'.DS.'DMObject.php';

class DMRenderer extends DMObject {
	private $table = '';
	private $fields = array();
	private $data = array();
	private $fields_params = array();
	private $fields_objects = array();
	private $rendered_fields = array();
	private $fields_prefix = '';
	
	function addParams($params) {
		$this->fields_params = array_merge($this->fields_params, $params);
	}

	function afterSaveEvent($key_field_value) {
		if ($this->loadLogicFields() === false) {
			return false;
		}

		foreach($this->fields_objects as $key => $field_obj) {
			if (!isset($this->data[$key])) {
				$this->data[$key] = false;
			}
			$field_obj->setParam('key_field_value', $key_field_value);
			$field_obj->setParam('value', $this->data[$key]);

			if (method_exists($field_obj,'afterSave')) {
				$result = $field_obj->afterSave();
				if(!$result) {
					return $result;
				}
			}
		}
		return true;
	}
	
	function getFields(){
		if ($this->loadLogicFields() === false) {
			return false;
		}

		if (!$this->data) {
			if ($this->renderFields() === false) {
				return false;
			}
		}else {

			foreach($this->data as $row) {
				if ($this->renderFields($row) === false) {
					return false;
				}
			}
		}
		
		return $this->rendered_fields;
	}
	
	function getFieldsData() {
		if ($this->loadLogicFields() === false) {
			return false;
		}

		$key_field = $this->fields_params['key_field'];
		if (isset($this->data[$key_field])) {
			$key_field_value = $this->data[$key_field];
		}else {
			$key_field_value = 0;
		}

		foreach($this->fields_objects as $key => $field_obj) {
			if (!isset($this->data[$this->fields_prefix.$key])) {
				$this->data[$this->fields_prefix.$key] = false;
			}
			$field_obj->setParam('key_field_value', $key_field_value);
			$field_obj->setParam('value', $this->data[$this->fields_prefix.$key]);

			$this->data[$key] = $field_obj->getValue();
			if($field_obj->getParam('use_null_when_empty') && empty($this->data[$key])) {
				$this->data[$key] = null;
			}
		}
		return $this->data;
	}
	
	function loadLogicFields() {
		if ($this->fields_objects) {
			return true;
		}
		
		include DM_PATH.DS.'logic_fields.php';
		
		foreach($this->fields as $key => $field) {
			if (!isset($logic_fields[$field['type']])) {
				$this->errors[] = " '{$field['type']}' is not defined in logic_fields.php ";
				return false;
			}
			$class = $logic_fields[$field['type']]['class'];
			if (!file_exists(DM_PATH.DS.'logic_fields'.DS.$class.'.php')) {
				$this->errors[] = " File for class '{$class}' not found in logic fields ";
				return false;
			}
			
			include_once DM_PATH.DS.'logic_fields'.DS.$class.'.php';
			$field_obj = new $class;
			$fields_params = array_merge($this->fields_params, $field);

			if($this->fields_prefix) {
				$fields_params['name'] = $this->fields_prefix.$fields_params['name'];
			}
			$field_obj->addParams($fields_params);
			$this->fields_objects[$field['name']] = $field_obj;
			unset($field_obj);
		}
		return true;
	}
	
	function renderFields($data_row = array()) {
		$rendered_row = array();

		if ($data_row) {
			$key_field = $this->fields_params['key_field'];
			if (isset($data_row[$key_field])) {
				$key_field_value = $data_row[$key_field];
			}else {
				$key_field_value = 0;
			}
		}

		foreach($this->fields_objects as $key => $field_obj) {
			if ($data_row) {
				$field_obj->setParam('key_field_value', $key_field_value);
				$field_obj->setParam('dara_row', $data_row);
				if (array_key_exists($key, $data_row)) {
					$field_obj->setParam('value', $data_row[$key]);
				}
			}
			$rendered_field = $field_obj->getField();
			if ($rendered_field === false) {
				$this->errors[] = "Can't render field '{$key}'. ".$field_obj->getErrorsStr();
				return false;
			}
			$rendered_row[$key] = $rendered_field;
			unset($rendered_field);
		}

		$this->rendered_fields[] = $rendered_row;
		unset($rendered_row);
	}
	
	function setData($data) {
		$this->data = $data;
	}
	
	function setTable($table) {
		$this->table = $table;
	}
	
	function setFields($fields) {
		$this->fields = $fields;
	}

	function setFieldsPrefix($prefix) {
		$this->fields_prefix = $prefix;
	}
}