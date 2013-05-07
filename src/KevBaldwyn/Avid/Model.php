<?php namespace KevBaldwyn\Avid;

use Eloquent;
use Validator;
use Doctrine\Common\Inflector\Inflector;

class Model extends Eloquent {
	
	protected $guarded = array('id');
	
	/**
	 * a list of fields not editable
	 * this gets merged with any gaurded properties
	 */
	protected $notEditable = array();
	
	protected $errors;
	
	protected $validationKey = null;
	protected static $validationRules = array();
	
	protected $nameField = 'name';
	
	/**
	 * a list of attribute names that have different labels
	 * ie: 'parent_id' => 'parent item'
	 */
	protected $customAttributes = array();
	
	
	public static function boot() {
		
		parent::boot();
		
		static::saving(function($model) {
			
			return $model->validate();
				
		});
		
	}
	
	
	public static function make($model) {
		// be sure we have the correct model name
		$name = static::modelFromTable($model);
		
		return new $name;
	}
	
	
	public function validate() {
		
		// which validation rules are we using, if any?
		if(!empty(static::$validationRules)) {
		
			if(!is_null($this->validationKey)) {
				$rules = static::$validationRules[$this->validationKey];
			}else{
				$rules = static::$validationRules;
			}
			
			$validation = Validator::make($this->attributes, $rules);
			
			if(is_array($this->customAttributes)) {
				$validation->setAttributeNames($this->customAttributes);
			}
			
			if($validation->passes()) {
				return true;
			}else{
				$this->errors = $validation->messages();
				return false;
			}
			
		}
		
	}
	
	
	public function getErrors() {
		return $this->errors;
	}
	
	
	public function hasErrors() {
		return (count($this->getErrors() > 0)) ? true : false;
	}
	
	
	public function setValidationKey($key) {
		$this->validationKey = $key;
	}
	
	
	public function getCustomAttributes() {
		return $this->customAttributes;
	}
	
	
	public static function modelFromTable($tableName) {
		$name = Inflector::classify(Inflector::singularize($tableName));
				
		return new $name;
	}
	
	
	public function getNotEditable() {
		return array_merge($this->guarded, $this->notEditable);
	}
	
	
	/**
	 * @todo build in query options
	 */
	public function selectList($options = array()) {
		
		$blank = (array_key_exists('blank', $options)) ? $options['blank'] : 'Choose an option';
		$key   = (array_key_exists('key', $options)) ? $options['key'] : 'id';
		$value = (array_key_exists('value', $options)) ? $options['value'] : $this->nameField;
		
		$initial = array('null' => $blank);
		$list    = $this->lists($value, $key);

		return $initial + $list;
	}
	
	
	public function __call($method, $parameter) {

		$tablenameRegEx = '[0-9a-zA-Z_]*';
	
		$magicMethodRegExMatch = array(
									   array('/^(find)(By|Like)(' . $tablenameRegEx . ')/'),
									   //array('/^(delete)(By|Like)(' . $tablenameRegEx . ')/')
									   );
		
		foreach($magicMethodRegExMatch as $magicMethod) {
			if(preg_match($magicMethod[0], $method, $match)) {

				switch($match[1]) {
					case 'find' :

						$operation = (strtolower($match[2]) == 'by') ? '=' : strtolower($match[2]);

						return $this->where($match[3], $operation, $parameter[0])->get();
						
						break;
				}
			}
		}
		
		return parent::__call($method, $parameter);
		
	}
	
}