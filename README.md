# plokko/locale-manager


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
To edit the configuration publish it with

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

