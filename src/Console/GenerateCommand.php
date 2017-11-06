<?php
namespace Plokko\LocaleManager\Console;

use Illuminate\Console\Command;

class GenerateCommand extends Command
{
    protected
        $langs;
    private
        $transFiles=null;

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
        }


        foreach($filter AS $trans_id)
        {

            $tree   = explode('.',$trans_id);
            $tr     = trans($trans_id,[],$lang);

            if(count($tree)==1)
            {
                $messages[$trans_id] = $tr;
            }else{
                $leaf = $messages;
                $last_key=array_pop($tree);
                //Descend tree
                foreach($tree AS $k)
                {
                    //if not set create empty array
                    if(!array_key_exists($k,$leaf)){
                        $leaf[$k] = [];
                    }
                    $leaf=$leaf[$k];
                }
                $leaf[$last_key] = $tr;
            }

        }


        return $messages;
    }


    private function getAllTransFiles(){
        if(!$this->transFiles)
        {
            $this->transFiles=[];
            //-- Expose all language files --//
            foreach(glob(resource_path('/lang/'.config('app.fallback_locale').'/*.php')) AS $file)
            {
                $this->transFiles[] = basename($file,'.php');
            }
        }
        return $this->transFiles;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Prepare directory structure //
        $dir = resource_path('/assets/js/vendor/locale-manager');
        @mkdir($dir,null,true);

        foreach($this->langs AS $lang)
        {
            $trans = $this->getTranslations($lang);

            // Prepare js //
            $js = 'trans.load('.json_encode($trans).','.json_encode($lang).');';

            // Save js //
            file_put_contents($dir.'/trans.'.$lang.'.js',$js);
        }

        $this->info('translation cache generated successfully!');
        return true;
    }
}