<?php

namespace Pdik\laravel_signhost;


use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class SignhostServiceProvider extends ServiceProvider
{
	public function boot()
	{
		$this->registerConfigs();
		//$this->registerMigrations();
		$this->registerViews();
//		$this->registerComponents();
//		$this->registerBladeDirectives();
		$this->registerRoutes();


	}

	private function registerConfigs()
	{
		if ($this->app->runningInConsole()) {
			$this->publishes([
				__DIR__ . '/../config/config.php' => config_path('signhost_config.php'),
			], 'laravel_signhost-config');
			$this->publishes([
				__DIR__ . '/../public' => public_path('vendor/Pdik/laravel_signhost'),
			], 'laravel_signhost-assets');
		}
		$this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'signhost_config');

	}

//	private function registerMigrations()
//	{
//		$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
//	}

	private function registerViews()
	{
		$this->loadViewsFrom(__DIR__ . '/../resources/views', 'laravel_signhost');
	}

	private function registerRoutes()
	{
		$router = $this->app->make(Router::class);

		$router->group($this->routeConfiguration(), function () {
			$this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
		});
	}

	private function routeConfiguration(): array
	{
		return [
			'namespace' => 'Pdik\laravel_signhost\Http\Controllers',
			'middleware' => ['web'],
			'as' => 'laravel_signhost::'
		];
	}


}
