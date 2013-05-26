<?php namespace KevBaldwyn\Avid;

use View;
use Debugger;
use Input;
use Redirect;
use Illuminate\Support\Contracts\MessageProviderInterface;

class Controller extends \Illuminate\Routing\Controllers\Controller {
	
	protected $messages;
	
	protected $viewPath;
	
	public function __construct(MessageProviderInterface $messages) {
		$this->messages = $messages;
		View::share('messages', $messages);
		
		// apply csrf filter to any POSTed actions
		$this->beforeFilter('csrf', array('on' => 'post'));
		
		$this->setViewPath(static::model()->getScaffoldRoute());
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
	
	
	protected function setViewPath($path) {
		$this->viewPath = $path;
	}
	
	
	/**
	 * return the default model associated with this controller 
	 * based on the name of controller
	 */
	public static function model() {
		$className = str_replace('Controller', '', get_called_class());
		return Model::make($className);
	}
	
	
	public function index() {
		
		$model = static::model();
		
		return View::make($this->viewPath . '.index', array('model' => $model))
						->nest('list_html', 'avid::scaffold.index', array('model' => $model,
																	 'list'  => $model->all()));
	}
	
		
	/**
	 * form here will POST to /controller whcih when POSTed will run self::store()
	 */
	public function create() {
		
		$model = static::model();
		
		return View::make($this->viewPath . '.create', array('model' => $model))
						->nest('form', 'avid::scaffold.create', array('ignore' => $model->getNotEditable(),
																	  'model'  => $model));
	}
	
	
	public function store() {
							
		$model = static::model()->create(Input::all());
		
		if(!$model->hasErrors()) {
			$this->messages->add('success', 'The item has been created.')
						   ->flash();
    
			return Redirect::back(static::model()->getScaffoldRoute('index'));
		}else{
			return Redirect::back()->withInput()->withErrors($model->getErrors());
		}
	}
	
	
	/**
	 * form here will PUT (POST) to /controller/{id}/edit which then PUTs (POSTs) to self::update()
	 */
	public function edit($id) {

		$model = static::model()->find($id);
		
		return View::make($this->viewPath . '.edit', array('model' => $model))
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
    
			return Redirect::route(static::model()->getScaffoldRoute('index'));
		}else{
			return Redirect::back()->withInput()->withErrors($model->getErrors());
		}
			
	}
	

	/**
	 * GET /model/id/delete
	 */
	public function delete($id) {

		$model = static::model()->find($id);
		
		// renders form which DELETES (POSTS) to destroy
		return View::make($this->viewPath . '.delete', array('model' => $model))
						->nest('form', 'avid::scaffold.delete', array('model'  => $model));
	}

	
	public function destroy($id) {
	
		$model = static::model()->find($id);
		
		if($model->delete()) {
			$this->messages->add('success', 'The item has been deleted.')
						   ->flash();
    
			return Redirect::route(static::model()->getScaffoldRoute('index'));
		}else{
			return Redirect::back()->withInput()->withErrors($model->getErrors());
		}
		
	}
	
	
	public function missingMethod($parameters) {
    	parent::missingMethod($parameters);    	
    }
	
}

