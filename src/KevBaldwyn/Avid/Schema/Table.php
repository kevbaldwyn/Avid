<?php namespace KevBaldwyn\Avid\Schema;

use DB;
use Debugger;
use Eloquent;

class Table {
	
	private $table;
	private $fields = array();
	
	
	public function __construct($mixed) {

		if($mixed instanceof Eloquent) {
			$this->table = $mixed->getTable();
		}else{
			$this->table = $mixed;
		}
		
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
	
	
	public function form($ignore = null, $options = array()) {
		$fields = $this->getFields();
		
		$html = '';
		
		foreach($fields as $field) {
			if(!is_array($ignore) || (is_array($ignore) && !in_array($field->name, $ignore))) {
				$html .= $field->form();
			}
		}
		
		return $html;
		
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