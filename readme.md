# Json Api formatter for Laravel
A library that will help you format the response easily

## Installation

```shell
composer require hoangdo/laravel-json-api
```

## Usage

### Just normal use it as a middleware

```php
// web.php
Route::get('foo', 'FooController@index')->middleware('json');

// or
Route::middleware('json')->group(function () {
    Route::get('foo', 'FooController@index');
    Route::get('bar', 'BarController@index');
})
```

### If you want to use it global for all api, just add it to the `api` group middleware

```php
// app/Http/Kernel.php
    ...

    protected $middlewareGroups = [
        ...
        'api' => [
            // Add it here
            'json',
            'throttle:60,1',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
        ...
    ];
    ...
```

### If you want to change the middleware alias name, for avoid conflict with another libraries, just fix it by .env

```dotenv
# You can add multiple aliases, separated by ","
JSON_MIDDLEWARE_NAME=json1,json2
```
