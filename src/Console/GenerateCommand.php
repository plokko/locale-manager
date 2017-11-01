<?php
namespace Plokko\LocaleManager\Console;

use Illuminate\Console\Command;

class GenerateCommand extends Command
{
    protected
        $langs;
    private
        $allMessages=null;

    protected $signature = 'locale-manager:generate';
    protected $description = 'Regenerate javascript translation files';

    public function __construct()
    {
        parent::__construct();
        $this->langs = config('locale-manager.allowed_locales');
    }

    private function getTranslations($lang){
        $messages=[];
        $filter = config('locale-manager.expose_js_trans');

        if(!$filter||$filter==='*')
        {
            $filter = $this->getAllTransFiles();

            foreach($filter AS $trans_id)
            {
                $messages[$trans_id] = trans($trans_id,[],$lang);
            }

        }else{
            //TODO: filter trans
        }

        return $messages;
    }


    private function getAllTransFiles(){
        if(!$this->allMessages)
        {
            $this->allMessages=[];
            //-- Expose all language files --//
            foreach(glob(resource_path('/lang/'.config('app.fallback_locale').'/*.php')) AS $file)
            {
                $this->allMessages[] = basename($file,'.php');
            }
        }
        return $this->allMessages;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        foreach($this->langs AS $lang)
        {
            $tr = $this->getTranslations($lang);

            // Prepare directory structure //
            $dir = resource_path('/assets/js/vendor/jstrans');
            @mkdir($dir,null,true);

            // Prepare js //
            $js='trans.load('.json_encode($tr).','.json_encode($lang).');';

            // Save js //
            file_put_contents($dir.'/messages.'.$lang.'.js',$js);
        }

        $this->info('translation cache generated successfully!');
        return true;
    }
}