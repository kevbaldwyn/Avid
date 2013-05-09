<?php namespace KevBaldwyn\Avid;

trait ModelScaffolding {

	use Properties;
	
	
	private function _defaultProperties() {
		return array(
		
			/**
			 * a list of fields not editable
			 * this gets merged with any gaurded properties
			 */
			'notEditable'      => array(),
			
			'errors'           => null,
			'validationKey'    => null,
			'validationRules'  => null,
			'nameField'        => 'name',
			
			/**
			 * a list of attribute names that have different labels
			 * ie: 'parent_id' => 'parent item'
			 */
			'customAttributes' => array()
			
		);
	}
	
	
	protected function _boot(array $properties = array()) {
		
		// set passed options
		$this->_setProperties($properties);
		
		// create default save event
		static::saving(function($model) {
			
			return $model->validateInput();
				
		});
		
	}
	
	
	public static function make($model) {
		// be sure we have the correct model name
		$name = static::modelFromTable($model);
		
		return new $name;
	}
	
	
	public function validateInput() {
		
		// which validation rules are we using, if any?
		if(!empty($this->__get('validationRules'))) {
		
			if(!is_null($this->__get('validationKey'))) {
				$rules = $this->__get('validationRules')[$this->__get('validationKey')];
			}else{
				$rules = $this->__get('validationRules');
			}
			
			$validation = Validator::make($this->attributes, $rules);
			
			if(is_array($this->__get('customAttributes'))) {
				$validation->setAttributeNames($this->__get('customAttributes'));
			}
			
			if($validation->passes()) {
				return true;
			}else{
				$this->__set('errors', $validation->messages());
				return false;
			}
			
		}
		
	}
	
	
	public function getErrors() {
		return $this->__get('errors');
	}
	
	
	public function hasErrors() {
		return (count($this->getErrors()) > 0) ? true : false;
	}
	
	
	public function setValidationKey($key) {
		$this->__set('validationKey', $key);
	}
	
	
	public function getCustomAttributes() {
		return $this->__get('customAttributes');
	}
	
	
	public static function modelFromTable($tableName) {
		$name = Inflector::classify(Inflector::singularize($tableName));
				
		return new $name;
	}
	
	
	public function getNotEditable() {
		return array_merge($this->guarded, $this->__get('notEditable'));
	}
	
	
	/**
	 * @todo build in query options
	 */
	public function selectList($options = array()) {
		
		$blank = (array_key_exists('blank', $options)) ? $options['blank'] : 'Choose an option';
		$key   = (array_key_exists('key', $options)) ? $options['key'] : 'id';
		$value = (array_key_exists('value', $options)) ? $options['value'] : $this->__get('nameField');
		
		$initial = array('null' => $blank);
		$list    = $this->lists($value, $key);

		return $initial + $list;
	}
	
}