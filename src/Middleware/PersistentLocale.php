<?php

namespace Plokko\LocaleManager\Middleware;

use Closure;
use Cookie;
use Illuminate\Http\Request;

class PersistentLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $cookie_name = config('locale-manager.locale_cookie_name');
        $locale = $request->cookie($cookie_name);

        if(config('locale-manager.match_user_preferences',true) && !$locale)
        {
            $locale = $this->getPreferredLocale($request);
        }

        if ($locale)
        {
            \App::setLocale($locale);
        }

        $lc = \App::getLocale();
        setlocale(LC_ALL,$lc,$lc.'_'.strtoupper($lc).'.UTF-8');

        $response = $next($request);
        //Update locale cookie after request ends//
        $lc = \App::getLocale();
		
        Cookie::queue($cookie_name,$lc,0);
        return $response;
    }

    /**
     * @param Request $request
     * @return string
     */
    public function getPreferredLocale(Request $request)
    {
        $accept_languages = $request->server('HTTP_ACCEPT_LANGUAGE');
        $langs = preg_split("/(,|;)/",$accept_languages);

        $allowed_locales = config('locale-manager.allowed_locales',[config('app.locale')]);

        foreach($langs AS $locale)
        {
            if(in_array($locale,$allowed_locales))
                return $locale;
        }
        //Did not matc, return default
        return config('app.locale');
    }
}
