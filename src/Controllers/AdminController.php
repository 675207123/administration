<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2016, notadd.com
 * @datetime 2016-11-08 13:51
 */
namespace Notadd\Administration\Controllers;

use Exception;
use Notadd\Foundation\Routing\Responses\ApiResponse;
use Notadd\Foundation\Routing\Abstracts\Controller;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Zend\Diactoros\Response as Psr7Response;

/**
 * Class AdminController.
 */
class AdminController extends Controller
{
    /**
     * Get access token.
     *
     * @param \Notadd\Foundation\Routing\Responses\ApiResponse $response
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function access(ApiResponse $response)
    {
        if ($this->auth->guard('api')->user()) {
            try {
                $this->request->offsetSet('grant_type', 'client_credentials');
                $this->request->offsetSet('client_id', $this->client_id);
                $this->request->offsetSet('client_secret', $this->client_secret);
                $this->request->offsetSet('scope', '*');
                $request = (new DiactorosFactory)->createRequest($this->request);
                $back = $this->server->respondToAccessTokenRequest($request, new Psr7Response());
                $back = json_decode((string)$back->getBody(), true);
                if (isset($back['access_token'])) {
                    return $response->withParams([
                        'status' => 'success',
                    ])->withParams($back)->generateHttpResponse();
                }
            } catch (Exception $exception) {
                return $response->withParams([
                    'code'    => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'trace'   => $exception->getTraceAsString(),
                ])->generateHttpResponse();
            }
        }

        return $response->withParams([
            'status'  => 'error',
            'message' => 'Please Login !',
        ])->generateHttpResponse();
    }

    /**
     * Return index content.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function handle()
    {
        $this->share('translations', json_encode($this->translator->fetch('zh-cn')));

        return $this->view('admin::layout');
    }
}
