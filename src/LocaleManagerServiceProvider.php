<?php
namespace plokko\locale-manager;

use Illuminate\Support\ServiceProvider;

class LocaleManagerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        ///--- Load translations ---///
        //$this->loadTranslationsFrom(__DIR__.'/translations', 'locale-manager');
        //this will make it available as trans('locale-manager::file.line')

        ///--- Load routes ---///
        //$this->loadRoutesFrom(__DIR__.'/routes.php');

        ///--- Load views ---///
        //$this->loadViewsFrom(__DIR__.'/views', 'locale-manager');

        ///--- Load migrations ---///
        //$this->loadMigrationsFrom(__DIR__.'/database/migrations');


        ///--- Publish files ---///
        //-- Publish config file --//
        $this->publishes([
            __DIR__.'/config/config.php' => config_path('locale-manager.php'),
        ],'config');

        //-- Publish translation files --//
		/*
        $this->publishes([
            __DIR__.'/translations' => resource_path('lang/vendor/locale-manager'),
        ],'translations');
		//*/
        //-- Publish views --//
		/*
        $this->publishes([
            __DIR__.'/views' => resource_path('views/vendor/locale-manager'),

        ],'views');
		//*/

        //-- Publish migrations --//
		/*
        $this->publishes([
            __DIR__.'/database/migrations/' => database_path('migrations')
        ], 'migrations');
		//*/

        ///--- Console commands ---///
        /*//
        if ($this->app->runningInConsole())
        {
            $this->commands([
                FooCommand::class,
                BarCommand::class,
            ]);
        }
        //*/

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /// Merge default config ///
        $this->mergeConfigFrom(
            __DIR__.'/config/config.php', 'locale-manager'
        );
        ///
    }
}
