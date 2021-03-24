<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use App\Models\User;

class Authenticate
{
    protected $auth;
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }
    public function handle($request, Closure $next, $guard = null)
    {
        $validIp = env('IP_VALIDATION');
        $validIpStatus = env('IP_STATUS');
        $ip = explode("|", $validIp);
        $requestIp = $request->ip();
        if (!in_array($requestIp, $ip) && $validIpStatus==TRUE) {
            $res['status'] = false;
            $res['message'] = 'Ip Validation failed';
            return response($res, 401);
        }
        if ($this->auth->guard($guard)->guest()) {
            if ($request->has('API_TOKEN')) {
                try {
                    $token = $request->input('API_TOKEN');
                    $check_token = User::where('API_TOKEN', $token)->first();
                    if (!$check_token) {
                        $res['status'] = false;
                        $res['message'] = 'Unauthorize';
                        return response($res, 401);
                    }
                } catch (\Illuminate\Database\QueryException $ex) {
                    $res['status'] = false;
                    $res['message'] = $ex->getMessage();
                    return response($res, 500);
                }
            } else {
                $res['status'] = false;
                $res['message'] = 'Token Expired!';
                return response($res, 401);
            }
        }
        return $next($request);
    }
}
