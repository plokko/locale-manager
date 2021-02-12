<?php
namespace Plokko\LocaleManager;

use Blade;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Adapter\Local;
use Plokko\LocaleManager\Console\GenerateCommand;
use Plokko\LocaleManager\LocaleManager;

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

        // Facade accessor
        $this->app->bind(LocaleManager::class, function($app) {
            return new LocaleManager();
        });

        ///Blade directive
        Blade::directive('locales', function ($locale=null) {
            $lm = \App::make(LocaleManager::class);
            $urls = $lm->listLocaleUrls();
            return '<script src="<?php echo optional('.(var_export($urls,true)).')['.($locale?var_export($locale,true):'App::getLocale()').']; ?>" ></script>';
        });
    }

    public function provides()
    {
        return [
            LocaleManager::class,
        ];
    }
}
