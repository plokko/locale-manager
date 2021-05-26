<?php

namespace Plokko\LocaleManager\Middleware;

use App;
use Closure;
use Cookie;
use Illuminate\Http\Request;
use Plokko\LocaleManager\LocaleManager;

class PersistentLocale
{
    private
        /**
         * @var LocaleManager
         */
        $lm;

    function __construct(){
        $this->lm = App::make(LocaleManager::class);
    }
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        //Get locale
        $locale = $this->lm->getLocale($request);

        if ($locale)
        {
            //Apply locale
            App::setLocale($locale);
        }

        $lc = App::getLocale();
        setlocale(LC_ALL,$lc,$lc.'_'.strtoupper($lc).'.UTF-8');

        $response = $next($request);
        //Update locale cookie after request ends//
        $lc = App::getLocale();

        $this->lm->saveLocalePreferences($lc,$response);
        return $response;
    }

}
