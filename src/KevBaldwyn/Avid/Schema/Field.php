<?php namespace KevBaldwyn\Avid\Schema;

use Form;
use Session;

class Field {
	
	private $attributes = array();
	private $table;
	
	public function __construct($field, \KevBaldwyn\Avid\Schema\Table $table) {
		
		$this->table = $table;
		
		$this->attributes['name']      = $field->Field;
		$this->attributes['collation'] = $field->Collation;
		$this->attributes['key']       = $field->Key;
		$this->attributes['default']   = $field->Default;
		$this->attributes['comment']   = $field->Comment;
		
		// eg: varchar(255)
		if(preg_match('/(.*[^(])(\([0-9]*\))/', $field->Type, $match)) {
			$this->attributes['type'] = $match[1];
			$this->attributes['size'] = $match[2];
		}else{
			$this->attributes['type'] = $field->Type;
		}
		
		$this->attributes['input'] = $this->typeToInput();
		
	}
	
	
	public function isPrimaryKey() {
		return ($this->attributes['key'] == 'PRI') ? true : false;
	}
	
	

	/**
	 * This method is kinda a short cut for building forms 
	 * however it's real benefit is for scaffolding large CRUD style forms for editing DB records
	 * where we can build an entire intelligent form based on a Model (table name)
	 *
	 * @todo can we use a custom form macro to make all this super smooth?
	 *
	 * $tableSchema = new Table('table');
	 * $tableSchema->getField('name')->form(array('label' => 'The Name'));
	 */
	public function form($options = array(), $wrapElement = '<div class="control-group :css-error">:label:input:error</div>') {
				
		$field = $this->attributes;
		
		// label
		$label = Form::label($field['name'], ((array_key_exists('label', $options)) ? ucfirst($options['label']) : $this->humanName()));
		
		// remove label - all other options are passed to the form element
		// can we use the label for the error message?
		if(array_key_exists('label', $options)) {
			unset($options['label']);
		}
		
		// input
		switch($this->attributes['input']) {
			
			case 'checkbox' :
					// swap the output format for a checkbox
					if($wrapElement) {
						$wrapElement = str_replace(':label:input', ':input:label', $wrapElement);
					}
					$input  = Form::hidden($field['name'], '0');
					$input .= Form::checkbox($field['name'], 1, null, $options);
				break;
			
			case 'textarea' :
					$input = Form::textarea($field['name'], null, $options);
				break;
			
			case 'select' :
					// get model
					$model = \KevBaldwyn\Avid\Model::modelFromTable($this->modelForRelatedField());
					
					// grab select fields
					$list = $model->selectList();
					
					$input = Form::select($field['name'], $list, null, $options);
				break;
			
			case 'text' :
			default:
					if($field['name'] == 'password') {
						$input = Form::password($field['name'], $options);
					}else{
						$input = Form::text($field['name'], null, $options);
					}
				break;
		}
		
		// error
		$error      = '';
		$errorFlag  = false;
		if(Session::has('errors') && Session::get('errors')->has($field['name'])) {
			$error       = static::error($field['name']);
			$errorFlag   = true;
			$wrapElement = str_replace(':css-error', 'error', $wrapElement);
		}else{
			$wrapElement = str_replace(':css-error', '', $wrapElement);
		}
		
		
		if($wrapElement) {
			$wrapElement = str_replace(':label', $label, $wrapElement);
			$wrapElement = str_replace(':input', $input, $wrapElement);
			$wrapElement = str_replace(':error', $error, $wrapElement);
			return $wrapElement;
		}
		
		return $label . $input . $error; 
		
	}


	public static function error($name) {
		if(Session::has('errors') && Session::get('errors')->has($name)) {
			return '<span class="help-inline"><ul>' . implode('', Session::get('errors')->get($name, '<li>:message</li>'))  . '</ul></span>';
		}
		return '';
	}
	
	
	public function __get($key) {
		return $this->attributes[$key];
	}
	
	
	/**
	 * return a html field type based on the field type
	 *
	 * @todo implement proper date and date-time
	 * @param mixed $type
	 * @return string
	 */
	private function typeToInput() {
		$inputs = array('varchar'  => 'text',
						'tinyint'  => 'checkbox',
						'int'      => 'text',
						'text'     => 'textarea',
						'datetime' => 'text', // to do: implement proper date
						'date'     => 'text'); // to do: implement proper date
				
		$t = (array_key_exists($this->attributes['type'], $inputs)) ? $inputs[$this->attributes['type']] : 'input';
		
		$relatedModelName = $this->modelForRelatedField();
		if($this->attributes['type'] == 'int' && !is_null($relatedModelName)) {
			
			try {
				$model = \KevBaldwyn\Avid\Model::modelFromTable($relatedModelName);
				return 'select';

			}catch(Exception $e) {

				return 'text';

			}
			
		}
		
		return $t;
		
	}
	
	
	private function modelForRelatedField() {
		
		// if the field name is "parent" then it is the same model but a different id
		if($this->attributes['name'] == 'parent') {
			return $this->table->getName();
		}
		
		if(preg_match('/([a-z_]*)_id/', $this->attributes['name'], $match)) {
			return $match[1];
		}else{
			return null;
		}
	}
	
	
	private function humanName() {
		$modelRelation = $this->modelForRelatedField();
		if($modelRelation) {
			$fieldName = $modelRelation;
		}else{
			$fieldName = $this->attributes['name'];
		}
		
		return ucfirst(str_replace('_', ' ', $fieldName));
	}
	
}

