<?php

namespace Plokko\LocaleManager\Middleware;

use Closure;
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
        $this->lm = app()->make(LocaleManager::class);
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
            app()->setLocale($locale);
        }

        $lc = app()->getLocale();
        setlocale(LC_ALL,$lc,$lc.'_'.strtoupper($lc).'.UTF-8');

        $response = $next($request);
        //Update locale cookie after request ends//
        $lc = app()->getLocale();

        $this->lm->saveLocalePreferences($lc,$response);
        return $response;
    }

}
