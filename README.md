# plokko/locale-manager


## Installation
1. Install with composer 
`composer require plokko/locale-manager`
2. register the provider in your /config/app.php
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
3. to edit the configuration publish it with

`php artisan vendor:publish --provider="Plokko\LocaleManager\LocaleManagerServiceProvider" --tag="config"`

## Persistent locale
Edit your /App/Http/Kernel.php and add `\Plokko\LocaleManager\PersistentLocale` middleware
```php
<?php
   //....
    protected $middlewareGroups = [
        'web' => [
            //............
            //add this line below
            \Plokko\LocaleManager\Middleware\PersistentLocale::class,
            //...
        ],
//...
```

