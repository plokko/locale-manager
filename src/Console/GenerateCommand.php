<?php
namespace Plokko\LocaleManager\Console;

use Artisan;
use Illuminate\Console\Command;
use Plokko\LocaleManager\LocaleManager;

class GenerateCommand extends Command
{
    protected
        $lm;

    protected $signature = 'locale-manager:generate';
    protected $description = 'Generate JavaScript translation files';

    public function __construct(LocaleManager $lm)
    {
        parent::__construct();
        $this->lm = $lm;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->lm->generateTranslations();
        $this->info('translation files generated successfully!');
        //Clear view cache
        Artisan::call('view:clear');
        
        return Command::SUCCESS;
    }
}
