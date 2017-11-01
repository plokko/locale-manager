<?php
namespace Plokko\LocaleManager;

use Illuminate\Support\ServiceProvider;
use Plokko\LocaleManager\Console\GenerateCommand;

class LocaleManagerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //-- Publish config file --//
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('locale-manager.php'),
        ],'config');

        ///--- Console commands ---///
        if ($this->app->runningInConsole())
        {
            $this->commands([
                GenerateCommand::class,
            ]);
        }
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
            __DIR__.'/../config/config.php', 'locale-manager'
        );
        ///
    }
}
