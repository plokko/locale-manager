# plokko/locale-manager
This Laravel package manages persistent locales (language settings mantained within the session and stored in cookies) and extends Laravel translations to the fronted with a Javascript/Vue.js interface and command-line utility.

## Installation
Install with composer 
`composer require plokko/locale-manager`
register the provider in your /config/app.php
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
## Persistent locale
Edit your /App/Http/Kernel.php and add `\Plokko\LocaleManager\PersistentLocale` middleware
```php
<?php
   //....
    protected $middlewareGroups = [
        'web' => [
            //............
            //add this line below
            \Plokko\LocaleManager\PersistentLocale::class,
            //...
        ],
//...
```
