<?php

namespace App\Http\Controllers;

use App\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Request;
use Tapestry\Entities\Configuration;
use Tapestry\Tapestry;

class AuthenticationController extends BaseController
{
    public function handshake(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        /** @var Configuration $config */
        $config = $this->tapestry[Configuration::class];

        $jsonResponse = new JsonRenderer([
            'tapestryVersion' => Tapestry::VERSION,
            'siteName' => $config->get('site.title', 'unnamed')
        ]);
        $jsonResponse->setLinks([
            'self' => (string)$request->getUri()->getPath()
        ]);
        return $jsonResponse->render($response);
    }

    /**
     * @param ServerRequestInterface|Request $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface|\Slim\Http\Response
     */
    public function authenticate(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $username = $request->getParsedBodyParam('username', '');
        $password = $request->getParsedBodyParam('password', '');

        if ($username !== 'demo' || $password !== '1234') {
            return $response->withStatus(401);
        }

        $jsonResponse = new JsonRenderer([
            'jwt' => 'hello world!'
        ]);
        $jsonResponse->setLinks([
            'self' => (string)$request->getUri()->getPath()
        ]);
        return $jsonResponse->render($response);
    }
}
