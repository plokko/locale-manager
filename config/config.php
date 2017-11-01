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
];
