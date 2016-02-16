<?php namespace Eilander\Api;

use Illuminate\Contracts\Routing\ResponseFactory as Factory;
use Illuminate\Contracts\Support\MessageBag;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Response {

    const STATUS_OK = SymfonyResponse::HTTP_OK;
    const STATUS_NOT_FOUND = SymfonyResponse::HTTP_NOT_FOUND;
    const STATUS_INTERNAL_ERROR = SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR;
    const STATUS_UNKNOWN_ERROR = SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR;
    const STATUS_BAD_REQUEST = SymfonyResponse::HTTP_BAD_REQUEST;
    const STATUS_UNAUTHORIZED = SymfonyResponse::HTTP_UNAUTHORIZED;
    const STATUS_FORBIDDEN = SymfonyResponse::HTTP_FORBIDDEN;
    const STATUS_OFFLINE = SymfonyResponse::HTTP_SERVICE_UNAVAILABLE;
    const STATUS_GONE = SymfonyResponse::HTTP_GONE;
    const STATUS_VALIDATION_ERROR = 400;
    const STATUS_METHOD_NOT_ALLOWED = SymfonyResponse::HTTP_METHOD_NOT_ALLOWED;

    const ERROR_DEFAULT = 10;
    const ERROR_NOT_FOUND = 20;
    const ERROR_ENDPOINT_MISSING = 21;
    const ERROR_ENDPOINT_INACTIVE = 22;
    const ERROR_METHOD_NOT_ALLOWED = 23;
    const ERROR_SSL = 31;
    const ERROR_UNAUTHORIZED = 32;
    const ERROR_FORBIDDEN = 33;
    const ERROR_REQUEST = 40;

	/**
     * @var \Illuminate\Contracts\Routing\ResponseFactory
     */
    protected $response;
    protected $timer;

	public function __construct(Factory $response)
	{
		$this->response = $response;
        $this->timer = false;
	}

	public function make($content = '', $code = 200, $headers = [])
	{
		// add timer data
		if (is_array($content)) {
	        $time = round((microtime(TRUE) - LARAVEL_START), 2);
	        $content['took'] = $time.' seconds';
	    }

		return $this->response->json($content, $code, $headers);
	}

    public function data($data, $code = 200, $headers = [])
    {
        if ( ! isset($data['data']) ) {
            $data = ['data' => $data];
        }

        return $this->make($data, $code, $headers);
    }

    public function timer()
    {
        $this->timer = true;
        return $this;
    }

	public function error($message, $code = null, $http_code = null)
	{
		if ( is_null($http_code) ) {
			$http_code = self::STATUS_UNKNOWN_ERROR;
		}

		if ( is_null($code) ) {
			$code = self::ERROR_DEFAULT;
		}

		if ( (int) $http_code == 200 ) {
			throw new \Exception('Cannot respond with a status code of 200');
		}

		return $this->make([
			'error' => [
				'code' => $code,
				'http_code' => $http_code,
				'message' => $message
			]
		], $http_code);

	}

	public function errorValidation($errors, $message = 'Validation failed')
	{
		if ( $errors instanceof MessageBag ) {
			$errors->toArray();
		}

		return $this->make([
			'error' => [
				'code' => self::ERROR_REQUEST,
				'http_code' => self::STATUS_VALIDATION_ERROR,
				'message' => $message,
				'fields' => $errors,
			],
		], self::STATUS_VALIDATION_ERROR);
	}

	public function errorForbidden($message = 'Access Forbidden')
	{
		return $this->error($message, self::ERROR_FORBIDDEN, self::STATUS_FORBIDDEN);
	}

	public function errorUnauthorized($message = 'Unauthorized Access')
	{
		return $this->error($message, self::ERROR_UNAUTHORIZED, self::STATUS_UNAUTHORIZED);
	}

	public function errorNotFound($message = 'Resource Not Found')
	{
		return $this->error($message, self::ERROR_NOT_FOUND, self::STATUS_NOT_FOUND);
	}

    public function errorMethod($message = 'Missing method')
    {
        return $this->error($message, self::ERROR_ENDPOINT_MISSING, self::STATUS_NOT_FOUND);
    }
    public function errorMethodNotAllowed($message = 'Method not allowed')
    {
        return $this->error($message, self::ERROR_METHOD_NOT_ALLOWED, self::STATUS_METHOD_NOT_ALLOWED);
    }

	public function errorEndpointGone($message = 'You need to update to the latest version')
	{
		return $this->error($message, self::ERROR_ENDPOINT_INACTIVE, self::STATUS_GONE);
	}

	public function errorSSL($message = 'SSL is required')
	{
		return $this->error($message, self::ERROR_SSL, self::STATUS_BAD_REQUEST);
	}

	public function errorWithRequest($message = 'Bad request')
	{
		return $this->error($message, self::ERROR_REQUEST, self::STATUS_BAD_REQUEST);
	}


}
