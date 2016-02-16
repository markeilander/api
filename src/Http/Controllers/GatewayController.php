<?php

namespace Eilander\Api\Http\Controllers;

use App\Http\Controllers\Controller;
use Eilander\Api\InternalRequest;
use Eilander\Validator\LaravelValidator as Validator;
use Illuminate\Http\Request;
use Input;

class GatewayController extends Controller
{
    public function index()
    {
        // Calling with a single argument returns a json response
        return $this->returnResult($this->gateway()->all());
    }

    public function store(Request $request)
    {
        // Calling with a single argument returns a json response
        return $this->returnResult($this->gateway()->create($request->all()));
    }

    public function show($id)
    {
        // Calling with a single argument returns a json response
        return $this->returnResult($this->gateway()->show($id));
    }

    public function update($id, Request $request)
    {
        // Calling with a single argument returns a json response
        return $this->returnResult($this->gateway()->update($request->all(), $id));
    }

    public function destroy($id)
    {
        // Calling with a single argument returns a json response
        return $this->returnResult($this->gateway()->delete($id));
    }

    public function paginate()
    {
        // Calling with a single argument returns a json response
        return $this->returnResult($this->gateway()->paginate());
    }

    protected function gateway() {
        if(InternalRequest::running()) {
            $this->gateway->skipPresenter();
        }
        return $this->gateway;
    }

    /**
     * @param $result array/boolean
     */
    private function returnResult($result)
    {
        if(InternalRequest::running()) {
            return $result;
        } else {
            if( $result instanceof Validator){
                return $this->response()->errorValidation($result->messages());
            }
            // add queries in debug context
            if (is_array($result) && env('APP_ENV') == 'local' && env('APP_DEBUG')) {
                $result['queries'] = \DB::getQueryLog();
            }
            return $this->response()->data($result);
        }
    }

    /**
     * @return \Eilander\Api\Response
     */
    private function response()
    {
        return app('api')->response();
    }
}