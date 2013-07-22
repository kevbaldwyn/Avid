<?php namespace KevBaldwyn\Avid;

use Eloquent;

abstract class Model extends Eloquent {
	
	use ModelScaffolding;

	protected $guarded = array('id');

	private $actualTableFields = array();
	

	public function __construct(array $attributes = array()) {
		parent::__construct($attributes);
		
		$this->_InitModelScaffolding($this->InitModelScaffolding());
	}
	
	
	abstract protected function InitModelScaffolding();
	

	public function hasField($key) {
		if(count($this->actualTableFields) > 0) {
			$fields = $this->actualTableFields;
		}else{
			$fields = DB::raw('SHOW FULL FIELDS FROM ' . $this->getTable())->list('Field');
		}
		return in_array($key, $fields);
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