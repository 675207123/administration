<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-08-17 14:49
 */
namespace Notadd\Administration\Handlers;

use Illuminate\Auth\AuthManager;
use Illuminate\Container\Container;
use Laravel\Passport\Client as PassportClient;
use League\OAuth2\Server\AuthorizationServer;
use Notadd\Administration\Events\Logined;
use Notadd\Foundation\Auth\ThrottlesLogins;
use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Zend\Diactoros\Response as Psr7Response;

/**
 * Class TokenHandler.
 */
class TokenHandler extends Handler
{
    use ThrottlesLogins;

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
        $this->validate($this->request, [
            'name'     => Rule::required(),
            'password' => Rule::required(),
        ], [
            'name.required'     => '用户名必须填写',
            'password.required' => '用户密码必须填写',
        ]);
        if ($this->hasTooManyLoginAttempts($this->request)) {
            $seconds = $this->limiter()->availableIn($this->throttleKey($this->request));
            $message = $this->translator->get('auth.throttle', ['seconds' => $seconds]);
            $this->withCode(403)->withError($message);
        } else {
            if ($this->auth->guard()->attempt($this->request->only([
                'name',
                'password',
            ], $this->request->has('remember', true)))) {
                $this->request->session()->regenerate();
                $this->clearLoginAttempts($this->request);
                $this->request->offsetSet('grant_type', 'password');
                $this->request->offsetSet('client_id', $this->client_id);
                $this->request->offsetSet('client_secret', $this->client_secret);
                $this->request->offsetSet('username', $this->request->offsetGet('name'));
                $this->request->offsetSet('password', $this->request->offsetGet('password'));
                $this->request->offsetSet('scope', '*');
                $request = (new DiactorosFactory)->createRequest($this->request);
                $back = $this->server->respondToAccessTokenRequest($request, new Psr7Response());
                $back = json_decode((string)$back->getBody(), true);
                if (isset($back['access_token']) && isset($back['refresh_token'])) {
                    $data = [
                        'access_token' => $back['access_token'],
                        'name' => $this->guard()->user()->getAttribute('name'),
                    ];
                    $this->container->make('events')->dispatch(new Logined($this->guard()->user()));
                    $this->withCode(200)->withData($data)->withMessage('administration::login.success');
                } else {
                    $this->withCode(500)->withError('administration::login.fail');
                }
            } else {
                $this->withCode(500)->withError('administration::login.fail');
            }
        }
    }

    /**
     * @return \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard|mixed
     */
    public function guard()
    {
        return $this->auth->guard();
    }

    /**
     * @return string
     */
    public function username()
    {
        return 'name';
    }
}
