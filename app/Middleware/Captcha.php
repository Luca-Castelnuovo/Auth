<?php

namespace App\Middleware;

use App\Validators\CaptchaValidator;
use CQ\Captcha\hCaptcha;
use CQ\Config\Config;
use CQ\Middleware\Middleware;
use CQ\Response\Json;
use Exception;

class Captcha extends Middleware
{
    /**
     * Validate captcha response.
     *
     * @param $request
     * @param $next
     *
     * @return mixed
     */
    public function handle($request, $next)
    {
        try {
            CaptchaValidator::submit($request->data);
        } catch (Exception $e) {
            return new Json([
                'success' => false,
                'message' => 'Provided data was malformed',
                'data' => $e->getMessage(),
            ], 422);
        }

        if (!hCaptcha::v1(
            Config::get('captcha.secret_key'),
            $request->data->{'h-captcha-response'}
        )) {
            return new Json([
                'success' => false,
                'message' => 'Please complete captcha',
                'data' => [],
            ], 422);
        }

        return $next($request);
    }
}
