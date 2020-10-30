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
import trans from '../../vendor/plokko/locale-manager/assets/js/Trans';

//make it globally accessible from the page
window.trans = trans;
window.trans_choise = (tag,number,args)=>trans(tag,number,args);

//make it globally accessible from all Vue components
Vue.mixin({ methods:{ trans } });
//or as a Vue filter
Vue.mixin({ filters:{ 
    trans:t => trans(t),
    trans_choise:t => trans.choise(t),
} });

```