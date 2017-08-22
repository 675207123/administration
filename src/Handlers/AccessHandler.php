<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-08-17 15:22
 */

namespace Notadd\Administration\Handlers;

use Illuminate\Auth\AuthManager;
use Illuminate\Container\Container;
use Laravel\Passport\Client as PassportClient;
use League\OAuth2\Server\AuthorizationServer;
use Notadd\Foundation\Routing\Abstracts\Handler;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Zend\Diactoros\Response as Psr7Response;

/**
 * Class AccessHandler.
 */
class AccessHandler extends Handler
{
    /**
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * @var int
     */
    protected $client_id;

    /**
     * @var string
     */
    protected $client_secret;

    /**
     * @var \League\OAuth2\Server\AuthorizationServer
     */
    protected $server;

    /**
     * TokenHandler constructor.
     *
     * @param \Illuminate\Auth\AuthManager              $auth
     * @param \Illuminate\Container\Container           $container
     * @param \League\OAuth2\Server\AuthorizationServer $server
     */
    public function __construct(AuthManager $auth, Container $container, AuthorizationServer $server)
    {
        parent::__construct($container);
        $this->auth = $auth;
        $this->client_id = 1;
        $client = PassportClient::query()->findOrFail($this->client_id);
        $this->client_secret = $client->getAttribute('secret');
        $this->server = $server;
    }

    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    protected function execute()
    {
        $this->request->offsetSet('grant_type', 'client_credentials');
        $this->request->offsetSet('client_id', $this->client_id);
        $this->request->offsetSet('client_secret', $this->client_secret);
        $this->request->offsetSet('scope', '*');
        $request = (new DiactorosFactory)->createRequest($this->request);
        $back = $this->server->respondToAccessTokenRequest($request, new Psr7Response());
        $back = json_decode((string)$back->getBody(), true);
        if ($this->auth->guard('api')->user() && isset($back['access_token'])) {
            $this->withCode(200)->withData($back)->withMessage('已登录！');
        } else {
            $this->withCode(500)->withError('未登录！');
        }
    }
}
