<?php

return [
    /**
     * Allowed locales, add all the language you want to use for translations
     */
    'allowed_locales'           =>  ['en'],

    /**
     * Tries to match user's language preferences from Accept-Language headers
     * if disabled uses system default ignoring user preferences.
     */
    'match_user_preferences'    => true,

    /**
     * Cookie name where is stored the current user locale
     */
    'locale_cookie_name'        => 'app_locale',

    /**
     * Filter what translation strings should be exposed in Javascript/Vue.js translation
     * Allowed values are:
     *  - '*' to generate ALL translation files
     *  - An array of translations to convert only the specified translations;
     *    for example use ['validation'] to expose all messages in validation
     *    or specify individual messages  like ['validation.accepted','validation.between.numeric']
     */
    'expose_js_trans'           => '*',

    /**
     * If true will create a single file with all the translation, else it will create a file for each locale (ex. trans.en.js, trans.it.js, trans.es.js...)
     */
    'single_file' => false,

    /**
     * Where the js locale files will be created
     */
    'target_path' => public_path('js/trans'),

    /**
     * Public path of the locale files
     */
    'target_url'  => '/js/trans',

    /**
     * Prefix for the locale files
     */
    'messagefile_prefix' => 'trans',
    
    /**
     * Set to true to enable versioning for auto refreshing client cache
     */
    'versioning' => true,
    
    /**
     * Name of the global Javascript variable that contain Translation class, defaults to Localization (window.Localization) 
     */
    'js_class' => 'Localization',

    /**
     * If true will cache the md5 hash
     */
    'md5_cache' => true,
];
