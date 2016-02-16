<?php 

namespace  Eilander\Api\Http\Middleware;

use Closure;
use Eilander\Api\Api;
use Eilander\Api\InternalRequest;

class AuthenticateApiKey {
    /**
     * @var Api
     */
    private $api;

    /**
     * Create a new filter instance.
     *
     * @param  Api $api
     */
    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $internal = $request->header('x-internal-key');
        $key = $request->header('x-api-key');
        /**
         * Handle internal requests based on csrf token
         */
        if($internal !== null) {
            if($internal === InternalRequest::token()){
                InternalRequest::reset();
                return $next($request);
            }

            return $this->api->response()->errorUnauthorized('Invalid internal key');
        }
        /**
         * Handle normal API requests based on key
         */
        if($key !== null) {
            if ( ! $this->api->auth($key)) {
                return $this->api->response()->errorUnauthorized('Invalid api key');
            }

            return $next($request);
        }
        return $this->api->response()->errorUnauthorized('Invalid key provided');
    }

}