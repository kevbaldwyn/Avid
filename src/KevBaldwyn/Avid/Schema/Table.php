<?php namespace KevBaldwyn\Avid\Schema;

use DB;
use Debugger;

class Table {
	
	private $table;
	private $fields = array();
	
	
	public function __construct($table) {
		$this->table = $table;
		
		$this->readFields();
	}
	
	
	private function readFields() {
		// query 
		$fields = DB::select('SHOW FULL FIELDS FROM ' . $this->table);
		
		// set fields as list of Field objects 
		foreach($fields as $field) {
			$this->fields[$field->Field] = new Field($field);
		}
	}
	
	
	public function getFields() {
		return $this->fields;
	}
	
	
	public function getField($field) {
		if($this->hasField($field)) {
			return $this->fields[$field];
		}else{
			// throw exception
		}
	}
	
	
	public function hasField($field) {
		return array_key_exists($field, $this->fields);
	}
	
	
}