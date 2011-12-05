<?php

class DBMysql {
	protected static $_instance = null;
	protected $user;
	protected $pass;
	protected $dbhost;
	protected $dbname;
	protected $dbh = null;
	protected $query = '';
	protected $result = null;
	protected $error = '';
	
	public static function getInstance() {
		if (null === self::$_instance) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function __construct() {
		$this->dbhost = DM_DB_HOST;
		$this->dbname = DM_DB_NAME;
		$this->user = DM_DB_USER;
		$this->pass = DM_DB_PASS;
	}
	
	protected function connect() {
		if($this->dbh) {
			return true;
		}
		$this->dbh = mysql_connect($this->dbhost, $this->user, $this->pass);
		if(!is_resource($this->dbh)) {
			die("Can not connect to db");
		}
		if(!mysql_select_db($this->dbname,  $this->dbh)){
			die("Can not select db");
		}
	}
	
	public function escape($escape_value) {
		$this->connect();
		if (is_array($escape_value)) {
			foreach($escape_value as $key => $value) {
				$escape_value[$key] = mysql_real_escape_string($value, $this->dbh);
			}
		}else {
			$escape_value = mysql_real_escape_string($escape_value, $this->dbh);
		}
		return $escape_value;
	}

	public function getArrays() {
		if (!$this->query()) {
			return false;
		}
		$retval = array();
		while($row = mysql_fetch_assoc($this->result) ) {
			$retval[] = $row;
		}
		return $retval;
	}
	
	public function getArray() {
		if (!$this->query()) {
			return false;
		}
		$retval = mysql_fetch_assoc($this->result);
		if ($retval === false) {
			return null;
		}
		return $retval;
	}
	
	public function getDBConnection() {
		return $this->dbh;
	}
	
	public function getError() {
		return $this->error;
	}

	public function getObjects() {
		if (!$this->query()) {
			return false;
		}
		$retval = array();
		while($row = mysql_fetch_object($this->result) ) {
			$retval[] = $row;
		}
		return $retval;
	}

	public function getObject() {
		if (!$this->query()) {
			return false;
		}
		$retval = mysql_fetch_object($this->result);
		if ($retval === false) {
			return null;
		}
		return $retval;
	}
	
	public function getResult() {
		if (!$this->query()) {
			return false;
		}
		$row = mysql_fetch_row($this->result);
		if ($row === false) {
			return null;
		}
		return $row[0];
	}
	
	public function insertid() {
		return mysql_insert_id( );
	}

	public function query() {
		$this->result = mysql_query($this->query,  $this->dbh);
		if(!$this->result)  {
			$this->error = mysql_error($this->dbh).'Query:'.$this->query;
			return false;
		}
		return true;
	}
	
	public function setQuery($query) {
		$this->connect();
		$this->query = $query;
	}
}
