<?php namespace KevBaldwyn\Avid;

use Eloquent;
use Validator;
use Doctrine\Common\Inflector\Inflector;

class Model extends Eloquent {
	
	protected $guarded = array('id');
	
	protected $errors;
	
	protected $validationKey = null;
	protected static $validationRules = array();
	
	protected $nameField = 'name';
	
	
	public static function boot() {
		
		parent::boot();
		
		static::saving(function($model) {
			
			return $model->validate();
				
		});
		
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
	
	
	public static function modelFromTable($tableName) {
		$name = Inflector::classify(Inflector::singularize($tableName));
		return new $name;
	}
	
	
	/**
	 * @todo build in query options
	 */
	public function selectList($options = array()) {
		
		$blank = (array_key_exists('blank', $options)) ?: 'Choose an option';
		$key   = (array_key_exists('key', $options)) ?: 'id';
		$value = (array_key_exists('value', $options)) ?: $this->nameField;
		
		$initial = array(null => $blank);
		$list    = $this->lists($value, $key);
		
		return array_merge($initial, $list);
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