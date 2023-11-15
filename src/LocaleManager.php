<?php

namespace Plokko\LocaleManager;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocaleManager
{
    protected
    $locales,
    $prefix,
    $single_file,
    $versioning,
    $target_path,
    $target_url,
    $js_class,
    $versioning_cache;

    private
    $transFiles;

    const LOCALEURLS_CACHE_TAG = 'locale-manager_localeurls';

    function __construct()
    {
        $this->locales = config('locale-manager.allowed_locales');
        $this->prefix = config('locale-manager.messagefile_prefix', 'trans.');
        $this->single_file = config('locale-manager.single_file', false);
        $this->versioning = config('locale-manager.versioning', true);
        $this->target_path = config('locale-manager.target_path');
        $this->target_url = config('locale-manager.target_url');
        $this->js_class = config('locale-manager.js_class');
        $this->versioning_cache = config('locale-manager.versioning_cache');
    }

    /**
     * Get user locale from preferences (cookie) or preferred locale from browser string
     * @param Request|null $request Request, if null default language will be returned
     * @return string Locale
     */
    function getLocale(Request $request = null)
    {
        if ($request) {
            //Try to get selected locale from cookie
            $cookie_name = config('locale-manager.locale_cookie_name');
            $locale = $request->cookie($cookie_name);

            //Try to get selected preferred locale
            if (config('locale-manager.match_user_preferences', true) && !$locale) {
                $locale = $this->getPreferredLocale($request);
            }

            $allowed_locales = config('locale-manager.allowed_locales', [config('app.locale')]);
            // If allowed locale return it
            if ($locale && in_array($locale, $allowed_locales))
                return $locale;
        }
        //Return default locale
        return config('app.locale');
    }

    /**
     * @param Request $request
     * @return string
     */
    public function getPreferredLocale(Request $request)
    {
        //-- Try to get preferred user locale --//
        $accept_languages = $request->server('HTTP_ACCEPT_LANGUAGE');
        $langs = preg_split("/(,|;)/", $accept_languages);

        $allowed_locales = config('locale-manager.allowed_locales', [config('app.locale')]);

        foreach ($langs as $locale) {
            if (in_array($locale, $allowed_locales))
                return $locale;
        }

        //Did not match, return default
        return config('app.locale');
    }

    /**
     * Saves user preference in cookie
     * @param string $locale new locale
     * @param Response|null $response Response to apply
     * @return Response|null
     */
    public function saveLocalePreferences($locale, Response $response = null)
    {
        $cookie_name = config('locale-manager.locale_cookie_name');

        $cookie = Cookie::forever($cookie_name, $locale);
        Cookie::queue($cookie);

        return $response;
    }

    /**
     * Return translation filename for locale
     * @param string $locale
     * @return string
     */
    public function getTransFilename($locale)
    {
        return ($this->single_file) ?
            $this->prefix . '.js' :
            $this->prefix . '.' . $locale . '.js';
    }

    /**
     * Get locale file path
     * @param string $locale
     * @return string
     */
    public function getTransFilePath($locale)
    {
        $fileName = $this->getTransFilename($locale);
        return $this->target_path . '/' . $fileName;
    }

    /**
     * Get locale file URL
     * @param string $locale
     * @return string
     */
    public function getTransUrl($locale)
    {
        $fileName = $this->getTransFilename($locale);
        $url = $this->target_url . '/' . $fileName;
        if ($this->versioning) {
            $url .= '?v=' . $this->getTransFileHash($locale);
        }
        return $url;
    }

    /**
     * Get locale file hash for versioning
     * @param string $locale
     * @return string|null
     */
    public function getTransFileHash($locale)
    {
        $path = $this->getTransFilePath($locale);
        if (!is_file($path)) {
            return null;
        }
        return md5_file($path);
    }


    /**
     * Generate Javascript code to load translations
     * @param array $data translation data
     * @param string $locale translation locale
     * @return string Javascript code
     */
    private function generateJsLoad(array $data, $locale)
    {
        return 'window.' . $this->js_class . '.load(' . json_encode($data) . ',' . json_encode($locale) . ',' . json_encode($this->locales) . ');';
    }

    /**
     * Generate translation files
     */
    function generateTranslations()
    {
        // Prepare directory structure //
        @mkdir($this->target_path, 0755, true);

        if ($this->single_file) {
            $js = '';
            foreach ($this->locales as $locale) {
                $data = $this->getTranslations($locale);
                $js .= $this->generateJsLoad($data, $locale) . "\n";
            }

            $fileName = $this->getTransFilename($locale);
            file_put_contents($this->target_path . '/' . $fileName, $js);
        } else {
            foreach ($this->locales as $locale) {
                $data = $this->getTranslations($locale);

                // Prepare js //
                $js = $this->generateJsLoad($data, $locale);

                // Save js //
                $fileName = $this->getTransFilename($locale);
                file_put_contents($this->target_path . '/' . $fileName, $js);
            }
        }
        //Flush trans cache
        $this->flushCache();
    }

    /**
     * Return all translation present in the system
     * @return string[]
     */
    protected function getAllTransFiles()
    {
        if (!$this->transFiles) {
            $this->transFiles = [];
            //-- Expose all language files --//
            foreach (glob(app()->langPath(config('app.fallback_locale') . '/*.php')) as $file) {
                $this->transFiles[] = basename($file, '.php');
            }
        }
        return $this->transFiles;
    }

    /**
     * Return locale data for specified locale
     * @param string $locale Locale
     * @return array Locale data
     */
    protected function getTranslations($locale)
    {
        $messages = [];
        $filter = config('locale-manager.expose_js_trans');

        if (!$filter || $filter === '*') {
            $filter = $this->getAllTransFiles();
        }

        foreach ($filter as $trans_id) {
            $tree = explode('.', $trans_id);
            $tr = trans($trans_id, [], $locale);

            if (count($tree) == 1) {
                $messages[$trans_id] = $tr;
            } else {
                $leaf = $messages;
                $last_key = array_pop($tree);
                //Descend tree
                foreach ($tree as $k) {
                    //if not set create empty array
                    if (!array_key_exists($k, $leaf)) {
                        $leaf[$k] = [];
                    }
                    $leaf = $leaf[$k];
                }
                $leaf[$last_key] = $tr;
            }
        }

        return $messages;
    }

    function localeUrl($locale = null)
    {
        $request = request();
        if (!$locale) {
            $locale = $this->getLocale($request);
        }
        return $this->getTransUrl($locale);
    }

    /**
     * Return locale URLs with locale as key
     * @return array
     */
    function listLocaleUrls()
    {
        if ($this->versioning_cache && Cache::has(self::LOCALEURLS_CACHE_TAG)) {
            return Cache::get(self::LOCALEURLS_CACHE_TAG);
        }
        $urls = [];
        foreach ($this->locales as $locale) {
            $urls[$locale] = $this->getTransUrl($locale);
        }
        if ($this->versioning_cache) {
            Cache::put(self::LOCALEURLS_CACHE_TAG, $urls);
        }
        return $urls;
    }

    /**
     * Flush data stored into cache
     */
    public function flushCache()
    {
        Cache::pull(self::LOCALEURLS_CACHE_TAG);
    }
}
