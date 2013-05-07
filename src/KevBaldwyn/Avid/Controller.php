<?php namespace KevBaldwyn\Avid;

use View;
use Debugger;
use Input;
use Redirect;

class Controller extends \Illuminate\Routing\Controllers\Controller {
	

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}
	
	
	/**
	 * return the default model associated with this controller 
	 * based on the name of controller
	 */
	public static function model() {
		$className = str_replace('Controller', '', get_called_class());
		return Model::make($className);
	}
	
	
	
	
	public function create() {
		
	}
	
	
	public function store() {
			
	}
	
	
	/**
	 * form here will PUT (POST) to /controller/{id}/edit which then PUTs (POSTs) to self::update()
	 */
	public function edit($id) {

		$model = static::model()->find($id);
		
		return View::make($model->getTable() . '.edit')
						->nest('form', 'avid::scaffold.edit', array('ignore' => $model->getNotEditable(),
																	 'model'  => $model));
		
	}
	
	
	/**
	 * PUT /controller/{id}
	 */
	public function update($id) {
	
		$model = static::model()->find($id);
		
		$model->fill(Input::all());
		
		if($model->save()) {
			die('saved!');
		}else{
			return Redirect::back()->withInput()->withErrors($model->getErrors());
		}
			
	}
	
	
	public function destroy($id) {
		
	}
	
}

