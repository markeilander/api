# Laravel 5 Api

Hi, this is a api package for Laravel 5.

### Table of contents

[TOC]

### Step 1: Add the Service Provider

In your `config/app.php` add `Eilander\Api\Providers\ApiServiceProvider:class` to the end of the `providers` array:


```php
<?php
'providers' => [
    ...
    Eilander\Api\Providers\ApiServiceProvider::class,
],

```

### Step 2: Add package to composer.json for autoloading

Add the package to the main `composer.json` for autoloading and run `composer dump-autoload`, like so:

```php
<?php
   "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Eilander\\Api\\": "vendor/eilander/api/src/"
        }
    },
```


```bash
composer dump-autoload
```

### Configuration

There are some API configuration options that you’ll want to overwrite. First, publish the default configuration.

```bash
php artisan vendor:publish
```

This will add a new configuration file to: `config/api.php`.

```php
<?php
return array(

	'version' => 'v1',

	'route' => [
		'v1' => [ 'prefix' => 'api/v1', 'namespace' => 'Api\V1' ]
	],

	'keys' => [
		env('API_KEY', 'secret')
	],

);
```

#### version

This is the current version of your api

#### route

This array of arrays helps when setting up routes in your Laravel `routes.php` file. We adopt a prefix of api/v1 by default.

#### keys

This is the valid list of API keys that authenticate requests. By default we support an environment variable of `API_KEY` which you can set in your .env file.

## Middleware

This package includes a Middleware class

### Eilander\Api\Http\Middleware\AuthenticateApiKey

The **AuthenticateApiKey** Middleware is designed to guard Api routes against unauthorised access. We recommend you include it on all routes as follows, unless you have a public API.

Send a header `x-api-key = secrtekey` with the api request to validate against the routes.

```php
$apiRoute = config('api.route.'.config('api.version')); //change this if other versions are available
Route::group($apiRoute, function() {
	Route::group([‘middleware’ => ‘Eilander\Api\Http\Middleware\AuthenticateApiKey’], function() {
		Route::resource('gebruiker', 'GebruikerController');
		//{{builder_api_routes}}
	});
});
```

## Controllers

We provide a helpful **BaseController** base controller class that includes a `response` method, allowing you to return json responses or get access to the **Eilander\Api\Response** class which offers a variety of helpers methods.

This base controller extend from the base laravel controller in `App\Http\Controllers\Controller`. Don't remove this class.

There are some other controller implementation available that extend the **BaseController*. At the time of writing these are:

**GatewayController** 
Api controller with some default functionality that implements the gateway pattern.
Have a look at `Eilander\Api\Http\Controllers\GatewayController` for all the available methods.

## Responses

The **Eilander/Api/Response** class offers a variety of helper methods and ultimately uses the `Illuminate\Contracts\Routing\ResponseFactory` Laravel class to return a json response with appropriate headers.

You can use the API Response class in your controller by using the `response` helper method:

```php
	public function index()
	{
		$items = new Collection([‘one’,’two’,’three’]);

		// Calling with a single argument returns a json response
		return $this->response($items);
	}
```

or

```php
	public function index()
	{
		$items = new Collection([‘one’,’two’,’three’]);

		// Calling with no argument returns the response object
		return $this->response()->data($items);
	}

	public function find($id)
	{
		$item = Item::find($id);

		if ( ! $item ) {
			// Using the response object you can call helper methods.
			return $this->response()->errorNotFound();
		}

		return $this->response()->data($item);
	}
```

## InternalRequests

The **Eilander/Api/InternalRequests** class handles internal requests against the api.
Say you have an api module and an admin model then the admin model can digest every api endpoint in de api module.

All requests should have a uri to match the endpoint and **can** have params to send with the request.

```php
    InternalRequests::get('api/v1/posts'); // get al posts
    InternalRequests::get('api/v1/posts/123'); // get post with id 123
    InternalRequests::post('api/v1/posts', ['name' => 'Mark', 'age' => 31]); // add post
    InternalRequests::put('api/v1/posts/123', ['age' => 32]); // change post with id 123
    InternalRequests::delete('api/v1/posts/123'); // delete post with id 123
```

That's all, no further configuration needed.