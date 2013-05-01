<?php namespace KevBaldwyn\Avid\Schema;

use Debugger;
use Form;

class Field {
	
	private $attributes = array();
	
	public function __construct($field) {
		
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
	 * @todo can we use a custom form macro to make all this super smooth?
	 *
	 * $tableSchema = new Table('table');
	 *
	 * $tableSchema->getField('name')->form(array('label' => 'The Name'));
	 */
	public function form($options = array(), $wrapElement = '<div class="control-group">:label:input:error</div>') {
				
		$field = $this->attributes;
		
		$label = Form::label($field['name'], ((array_key_exists('label', $options)) ?: $this->humanName($field['name'])));
		
		// remove label - all other options are passed to the form element
		// can we use the label for the error message?
		if(array_key_exists('label', $options)) {
			unset($options['label']);
		}
		
		$error = '';
		
		switch($this->attributes['input']) {
			
			case 'checkbox' :
					// swap the output format for a checkbox
					if($wrapElement) {
						$wrapElement = str_replace(':label:input', ':input:label', $wrapElement);
					}
					$input = Form::checkbox($field['name'], 1, null, $options);
				break;
			
			case 'textarea' :
					$input = Form::textarea($field['name'], null, $options);
				break;
			
			case 'select' :
					// get model
					$model = \KevBaldwyn\Avid\Model::modelFromTable($this->modelForRelatedField());
					
					// grab select fields
					$list = $model->selectList();
					
					$input = Form::select($field['name'], $list, $options);
				break;
			
			case 'text' :
			default:
					$input = Form::text($field['name'], null, $options);
				break;
		}
		
		if($wrapElement) {
			$wrapElement = str_replace(':label', $label, $wrapElement);
			$wrapElement = str_replace(':input', $input, $wrapElement);
			$wrapElement = str_replace(':error', $error, $wrapElement);
			return $wrapElement;
		}
		
		return $label . $input . $error; 
		
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
				
		$t = ($inputs[$this->attributes['type']] != null) ? $inputs[$this->attributes['type']] : 'input';
		
		$relatedModelName = $this->modelForRelatedField();
		if($this->attributes['type'] == 'int' && !is_null($relatedModelName)) {
			
			try {
				$model = \KevBaldwyn\Avid\Model::modelFromTable($relatedModelName);
				return 'select';

			}catch(Exception $e) {

				return 'text';

			}
			
			/*
			if(in_array(inflector::pluralize($match[1]), configure::get('app.models'))) {
				$m = model::load(inflector::pluralize($match[1]));
				
				// get the data from the model to put in th elist
				$opts['options'] = $m->selectList();
				$this->data['input'] = 'select';
			}else{
				$this->data['input'] = 'input';
			}
			*/
		}
		
		return $t;
		
	}
	
	
	private function modelForRelatedField() {
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

