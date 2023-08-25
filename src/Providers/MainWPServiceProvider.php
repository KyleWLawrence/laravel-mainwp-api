<?php

namespace KyleWLawrence\MainWP\Providers;

use Illuminate\Support\ServiceProvider;
use KyleWLawrence\MainWP\Services\MainWPService;
use KyleWLawrence\MainWP\Services\NullService;

class MainWPServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider and merge config.
     *
     * @return void
     */
    public function register()
    {
        $packageName = 'mainwp-laravel';
        $configPath = __DIR__.'/../../config/mainwp-laravel.php';

        $this->mergeConfigFrom(
            $configPath, $packageName
        );

        $this->publishes([
            $configPath => config_path(sprintf('%s.php', $packageName)),
        ]);
    }

    /**
     * Bind service to 'MainWP' for use with Facade.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind('MainWP', function () {
            $driver = config('mainwp-laravel.driver', 'api');
            if (is_null($driver) || $driver === 'log') {
                return new NullService($driver === 'log');
            }

            return new MainWPService;
        });
    }
}
