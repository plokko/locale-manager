<?php

namespace Plokko\LocaleManager;

use Closure;
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

        $locale = $request->cookie(config('locale-manger.locale_cookie_name'));

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
        return $response->cookie(config('locale-manger.locale_cookie_name'),\App::getLocale());
    }

    public function getPreferredLocale(Request $request)
    {
        $accept_languages = $request->server('HTTP_ACCEPT_LANGUAGE');
        $langs = preg_split("/(,|;)/",$accept_languages);

        $allowed_locales = config('locale-manger.allowed_locales',[config('locale')]);

        foreach($langs AS $locale)
        {
            if(in_array($locale,$allowed_locales))
                return $locale;
        }
        return null;
    }
}
