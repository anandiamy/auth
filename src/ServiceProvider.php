<?php

namespace Laravolt\Auth;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class PackageServiceProvider
 *
 * @package LaraLeague\Auth
 * @see http://laravel.com/docs/5.1/packages#service-providers
 * @see http://laravel.com/docs/5.1/providers
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @see http://laravel.com/docs/5.1/providers#deferred-providers
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @see http://laravel.com/docs/5.1/providers#the-register-method
     * @return void
     */
    public function register()
    {
    }

    /**
     * Application is booting
     *
     * @see http://laravel.com/docs/5.1/providers#the-boot-method
     * @return void
     */
    public function boot()
    {

        $this->registerViews();
        $this->registerMigrations();
        $this->registerSeeds();
        $this->registerAssets();
        $this->registerTranslations();
        $this->registerConfigurations();

        if(! $this->app->routesAreCached() && config('auth.routes')) {
            $this->registerRoutes();
        }
    }

    /**
     * Register the package views
     *
     * @see http://laravel.com/docs/5.1/packages#views
     * @return void
     */
    protected function registerViews()
    {
        // register views within the application with the set namespace
        $this->loadViewsFrom($this->packagePath('resources/views'), 'auth');
        // allow views to be published to the storage directory
        $this->publishes([
            $this->packagePath('resources/views') => base_path('resources/views/vendor/auth'),
        ], 'views');
    }

    /**
     * Register the package migrations
     *
     * @see http://laravel.com/docs/5.1/packages#publishing-file-groups
     * @return void
     */
    protected function registerMigrations()
    {
        $this->publishes([
            $this->packagePath('database/migrations') => database_path('/migrations')
        ], 'migrations');
    }

    /**
     * Register the package database seeds
     *
     * @return void
     */
    protected function registerSeeds()
    {
        $this->publishes([
            $this->packagePath('database/seeds') => database_path('/seeds')
        ], 'seeds');
    }

    /**
     * Register the package public assets
     *
     * @see http://laravel.com/docs/5.1/packages#public-assets
     * @return void
     */
    protected function registerAssets()
    {
        $this->publishes([
            $this->packagePath('resources/assets') => public_path('laravolt/auth'),
        ], 'public');
    }

    /**
     * Register the package translations
     *
     * @see http://laravel.com/docs/5.1/packages#translations
     * @return void
     */
    protected function registerTranslations()
    {
        $this->loadTranslationsFrom($this->packagePath('resources/lang'), 'auth');
    }

    /**
     * Register the package configurations
     *
     * @see http://laravel.com/docs/5.1/packages#configuration
     * @return void
     */
    protected function registerConfigurations()
    {
        $this->mergeConfigFrom(
            $this->packagePath('config/config.php'), 'auth'
        );
        $this->publishes([
            $this->packagePath('config/config.php') => config_path('auth.php'),
        ], 'config');
    }

    /**
     * Register the package routes
     *
     * @warn consider allowing routes to be disabled
     * @see http://laravel.com/docs/5.1/routing
     * @see http://laravel.com/docs/5.1/packages#routing
     * @return void
     */
    protected function registerRoutes()
    {
        $this->app['router']->group([
            'namespace' => __NAMESPACE__ . '\Http\Controllers'
        ], function($router) {
            $router->get('auth/login', 'AuthController@getLogin');
            $router->post('auth/login', 'AuthController@postLogin');
            $router->get('auth/logout', 'AuthController@getLogout');
            $router->get('auth/register', 'AuthController@getRegister');
            $router->post('auth/register', 'AuthController@postRegister');
            $router->get('password/email', 'PasswordController@getEmail');
            $router->post('password/email', 'PasswordController@postEmail');
            $router->get('password/reset/{token}', 'PasswordController@getReset');
            $router->post('password/reset', 'PasswordController@postReset');
            $router->get('auth/activate/{token}', 'AuthController@getActivate');

        });
    }

    /**
     * Loads a path relative to the package base directory
     *
     * @param string $path
     * @return string
     */
    protected function packagePath($path = '')
    {
        return sprintf("%s/../%s", __DIR__ , $path);
    }
}
