<?php namespace Eilander\Api\Providers;

use Illuminate\Http\Response;
use Illuminate\Support\ServiceProvider;
use League\Fractal\Manager;
use Eilander\Api\Api;

class ApiServiceProvider extends ServiceProvider {

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
        $this->publishes([
            __DIR__.'/../Config/api.php' => config_path('api.php'),
        ]);

        app('Illuminate\Contracts\Routing\ResponseFactory')->macro('api', function()
        {
            return app('api');
        });
        if (env('APP_ENV') == 'local' && env('APP_DEBUG')) {
            \DB::enableQueryLog();
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../Config/api.php', 'api'
        );

        $this->app->bindShared('Eilander\Api\Api', function()
        {
            $config = app('config')->get('api', []);
            $response = app('Eilander\Api\Response');
            $request = app('Illuminate\Http\Request');

            return new Api($config, $request, $response);
        });

        $this->app->bind('api', 'Eilander\Api\Api');
    }

}
