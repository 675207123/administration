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
use Illuminate\Auth\AuthManager;
use Illuminate\Routing\UrlGenerator;
use Laravel\Passport\Client as PassportClient;
use League\OAuth2\Server\AuthorizationServer;
use Notadd\Foundation\Extension\ExtensionManager;
use Notadd\Foundation\Module\ModuleManager;
use Notadd\Foundation\Passport\Responses\ApiResponse;
use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Foundation\Translation\Translator;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Zend\Diactoros\Response as Psr7Response;

/**
 * Class AdminController.
 */
class AdminController extends Controller
{
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
     * @var \Notadd\Foundation\Translation\Translator
     */
    protected $translator;

    /**
     * @var \Illuminate\Routing\UrlGenerator
     */
    protected $url;

    /**
     * AdminController constructor.
     *
     * @param \League\OAuth2\Server\AuthorizationServer $server
     * @param \Notadd\Foundation\Translation\Translator $translator
     */
    public function __construct(AuthorizationServer $server, Translator $translator)
    {
        parent::__construct();
        $this->client_id = 1;
        $client = PassportClient::query()->findOrFail($this->client_id);
        $this->client_secret = $client->getAttribute('secret');
        $this->translator = $translator;
        $this->url = $this->container->make(UrlGenerator::class);
        $this->server = $server;
    }

    /**
     * Get access token.
     *
     * @param \Illuminate\Auth\AuthManager                      $auth
     * @param \Notadd\Foundation\Passport\Responses\ApiResponse $response
     *
     * @return \Notadd\Foundation\Passport\Responses\ApiResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function access(AuthManager $auth, ApiResponse $response)
    {
        if ($auth->guard('api')->user()) {
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
     * @param \Notadd\Foundation\Extension\ExtensionManager $extension
     * @param \Notadd\Foundation\Module\ModuleManager       $module
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function handle(ExtensionManager $extension, ModuleManager $module)
    {
        $this->share('extensions', $extension->getEnabledExtensions());
        $this->share('modules', $module->repository()->enabled());
        $this->share('translations', json_encode($this->translator->fetch('zh-cn')));

        return $this->view('admin::layout');
    }
}
