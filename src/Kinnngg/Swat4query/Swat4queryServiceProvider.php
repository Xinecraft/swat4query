<?php namespace Kinnngg\Swat4query;

use Illuminate\Support\ServiceProvider;

use Symfony\Component\Finder\Finder;

use Illuminate\Filesystem\Filesystem;

class Swat4queryServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
            $this->package('kinnngg/swat4query');
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
    $this->app['swat4query'] = $this->app->share(function($app)
            {
             return new Server('127.0.0.1',10483);
            });	
  }

  public function provides()
  {
    return array('swat4query');
  }

}