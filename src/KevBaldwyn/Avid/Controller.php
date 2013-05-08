<?php namespace KevBaldwyn\Avid;

use View;
use Debugger;
use Input;
use Redirect;
use Illuminate\Support\Contracts\MessageProviderInterface;

class Controller extends \Illuminate\Routing\Controllers\Controller {
	
	protected $messages;
	
	
	public function __construct(MessageProviderInterface $messages) {
		$this->messages = $messages;
		View::share('messages', $messages);
	}
	

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
	
		
	/**
	 * form here will POST to /controller whcih when POSTed will run self::store()
	 */
	public function create() {
		
		$model = static::model();
		
		return View::make($model->getTable() . '.create')
						->nest('form', 'avid::scaffold.create', array('ignore' => $model->getNotEditable(),
																	  'model'  => $model));
	}
	
	
	public function store() {
							
		$model = static::model()->create(Input::all());
		
		if(!$model->hasErrors()) {
			$this->messages->add('success', 'The item has been created.')
						   ->flash();
    
			return Redirect::back();
		}else{
			return Redirect::back()->withInput()->withErrors($model->getErrors());
		}
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
			$this->messages->add('success', 'The item has been saved.')
						   ->flash();
    
			return Redirect::back();
		}else{
			return Redirect::back()->withInput()->withErrors($model->getErrors());
		}
			
	}
	
	
	public function destroy($id) {
		
	}
	
}

