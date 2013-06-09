<?php namespace KevBaldwyn\Avid\Providers;

use KevBaldwyn\Avid\FlashMessageBag; 
use Illuminate\Support\ServiceProvider;

class AvidServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('kevbaldwyn/avid');
		require_once(__DIR__.'/../form-macros.php');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		
		\Config::package('kevbaldwyn/avid', __DIR__.'/../../../config');
		
		$app = $this->app;
		
		$this->app->bind(
					'Illuminate\Support\Contracts\MessageProviderInterface', 
					function() use ($app) {
						return new FlashMessageBag($app->make('session'));
					}
		);
		
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('avid');
	}

}