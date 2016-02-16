<?php
namespace Eilander\Api;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;

class InternalRequest
{
	protected static $token;
	protected static $running = false;
	/**
	 * Dispatch a GET request.
	 *
	 * @return mixed
	 */
	public static function get($uri, array $params = array())
	{
		return self::dispatch('get', $uri, $params);
	}

	/**
	 * Dispatch a POST request.
	 *
	 * @return mixed
	 */
	public static function post($uri, array $params = array())
	{
		return self::dispatch('post', $uri, $params);
	}

	/**
	 * Dispatch a PUT request.
	 *
	 * @return mixed
	 */
	public static function put($uri, array $params = array())
	{
		return self::dispatch('put', $uri, $params);
	}

	/**
	 * Dispatch a DELETE request.
	 *
	 * @return mixed
	 */
	public static function delete($uri, array $params = array())
	{
		return self::dispatch('delete', $uri, $params);
	}

	/**
	 * Dispatch this request.
	 *
	 * @param string $method
	 *
	 * @return mixed
	 */
	public static function dispatch($method, $uri, $params)
	{
		self::$running = true;
		// Save original input.
		$originalInput = Request::input();
		// Create request.
		$request = Request::create($uri, $method, $params);
		Request::replace($request->input());
        // Gebruik csrf token als internal request key
        $request->headers->set('x-internal-key', self::token());
		// Dispatch request.
		//$response = app()->handle($request);
		$response = Route::dispatch($request);
		// Restore original input.
		//Request::replace($originalInput);
		$content = $response->getOriginalContent();
		return empty($content) ? null : $content;
	}

    public static function token()
    {
        if (trim(self::$token) == '') {
            self::$token = str_random(37);
        }
        return self::$token;
    }

    public static function reset()
    {
        self::$token = '';
    }

    public static function running()
    {
        return self::$running;
    }
}