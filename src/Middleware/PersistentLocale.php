<?php

namespace Plokko\LocaleManager;

use Closure;
use Illuminate\Http\Request;

class PersistentLocale
{

    const
        session_fieldName='app_locale';

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        $locale = $request->cookie(self::session_fieldName);

        if(config('locales.init_to_preferred_locale',true) && !$locale)
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
        return $response->cookie(self::session_fieldName,\App::getLocale());
    }

    public function getPreferredLocale(Request $request)
    {
        $accept_languages = $request->server('HTTP_ACCEPT_LANGUAGE');
        $langs = preg_split("/(,|;)/",$accept_languages);

        $allowed_locales = config('locales.allowed_locales',['en']);

        foreach($langs AS $locale)
        {
            if(in_array($locale,$allowed_locales))
                return $locale;
        }
        return null;
    }
}
