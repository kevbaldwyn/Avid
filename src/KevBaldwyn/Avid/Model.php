<?php namespace KevBaldwyn\Avid;

use Eloquent;
use Validator;
use Doctrine\Common\Inflector\Inflector;

class Model extends Eloquent {
	
	use ModelScaffolding;

	
	protected $guarded = array('id');
	
	
	public static function boot() {
		
		parent::boot();
		
		$this->_boot();
		
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