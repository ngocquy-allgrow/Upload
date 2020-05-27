<?php

namespace App\Http\Middleware;

use MicroService\Src\Entity\Json\GetEntity;
use Closure;

class ApiMicroService
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $params_request = $request->get('params_request', false);
        $params_request = json_decode($params_request);
        // count param neu > 0 thi hop le
        // else status 406 va code la 195
        if (!isset($params_request->czt) || (isset($params_request->czt) 
        && $params_request->czt != config('app.key_czt')))
        {
            $get_json = new GetEntity([]);
            $get_json->setHeaderMiddlewareResponse();
            $result   = $get_json->toJson();

            return $result;
        }
        // Delete all unnecessary data
        $request->request->add((array)$params_request);
        $request->request->remove('czt');

        return $next($request);
    }
}
