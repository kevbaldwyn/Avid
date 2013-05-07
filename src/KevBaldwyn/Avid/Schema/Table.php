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
			$this->fields[$field->Field] = new Field($field, $this);
		}
	}
	
	
	public function form($ignore = null, $options = array()) {
		$fields = $this->getFields();
		
		$html = '';
		
		foreach($fields as $field) {
			// if this field is not being ignored
			if(!is_array($ignore) || (is_array($ignore) && !in_array($field->name, $ignore))) {
			
				$fieldOptions = array();
				// do we have a csutom name for this field to set as the label?
				if(array_key_exists('customAttributes', $options) && array_key_exists($field->name, $options['customAttributes'])) {
					$fieldOptions['label'] = $options['customAttributes'][$field->name];
				}
				
				$html .= $field->form($fieldOptions);
			}
		}
		
		return $html;
		
	}
	
	
	public function getFields() {
		return $this->fields;
	}
	
	
	public function getName() {
		return $this->table;
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