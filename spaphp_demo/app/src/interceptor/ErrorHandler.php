<?php
/**
 * Created by PhpStorm.
 * User: jomiao
 * Date: 2018/6/6
 * Time: 上午10:13
 */

namespace app\interceptor;

use spaphp\facade\Log;
use spaphp\support\Timer;

class ErrorHandler
{
    /**
     * Handle an incoming request.
     *
     * @param  \spaphp\http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        Timer::start('cgi');
        try {
            $response = $next($request);
            return $response;
        } catch (\Throwable $e) {
            $ret = [
                'code' => $e->getCode() ?: 1,
                'message' => sprintf('%s: %s in %s:%s', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine()),
            ];
            $response = json_encode($ret, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING);
            return $response;
        } finally {
            $used = Timer::stop('cgi');
            Log::info('responsed', ['response' => $response, 'used_ms' => $used]);
        }
    }
}
