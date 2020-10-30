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
        $single_file = config('locale-manager.single_file');
        $file_prefix = config('locale-manager.messagefile_prefix');
        // Prepare directory structure //
        $dir = config('locale-manager.target_path');
        @mkdir($dir,null,true);

        if($single_file){
            $js='';
            foreach($this->langs AS $lang){
                $trans = $this->getTranslations($lang);
                $js.= 'trans.load('.json_encode($trans).','.json_encode($lang).");\n";
            }
            file_put_contents($dir.'/'.$file_prefix.'.js',$js);
        }
        else{
            foreach($this->langs AS $lang)
            {
                $trans = $this->getTranslations($lang);

                // Prepare js //
                $js = 'trans.load('.json_encode($trans).','.json_encode($lang).');';

                // Save js //
                file_put_contents($dir.'/'.$file_prefix.$lang.'.js',$js);
            }
        }
        $this->info('translation files generated successfully!');
        return true;
    }
}