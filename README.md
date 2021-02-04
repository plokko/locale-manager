# Laravel locale-manager
Generates and manage Javascript Laravel localization and current locale

## Installation
Install with composer 

`composer require plokko/locale-manager`

Laravel >=5.5 should auto discover and register the required services.

If you have laravel <5.5 you need to manually register the provider in your /config/app.php
```php
<?php
//...
   'providers' => [
                 //...
                 //-- Add locale manager --//
                 Plokko\LocaleManager\LocaleManagerServiceProvider::class,
                 //...
   ],
//...
```
To edit configuration parameter publish it with
`php artisan vendor:publish --provider="Plokko\LocaleManager\LocaleManagerServiceProvider" --tag="config"`
then edit your *config\locale-manager.php* configuration file.

### Persistent locale
If you want to automatically set the Laravel locale to the user (browser preferred) locale or let the user set a locale (via cookie) you can add the **PersistentLocale** middleware included in this package:
edit your */App/Http/Kernel.php* file and add the `\Plokko\LocaleManager\PersistentLocale` middleware
```php
<?php
   //...
    protected $middlewareGroups = [
        'web' => [
            //...
            //add this line below
            \Plokko\LocaleManager\Middleware\PersistentLocale::class,
            //...
        ],
//...
```

## Javascript initialization

Include the Javascript code in your main app.js file like below:
```javascript
/*** Import translation plugin ***/
import Localization from '../../vendor/plokko/locale-manager/assets/js/Localization';

// Make it globally accessible from the page 
// NOTE: variable name MUST be the same js_class 
window.Localization = new Localization();

// Optional: add global functions
window.trans = function(key,replace){return window.Localization.trans(key,replace);};
window.trans_choice = function(key,number,replace){return window.Localization.trans_choice(key,number,replace);};

// Optional: Vue mixin to make it globally available
Vue.mixin({
    methods:{
        trans: window.trans ,
        trans_choice: window.trans_choice ,
    },
    filters:{
        trans:(s) => window.trans(s),
    }
});

```
