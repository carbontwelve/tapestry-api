<?php

namespace App\Http\Controllers;

use App\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Request;
use Tapestry\Tapestry;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class AuthenticationController extends BaseController
{
    public function handshake(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $jsonResponse = new JsonRenderer([
            'tapestryVersion' => Tapestry::VERSION,
            'projects' => 1
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

        //
        // @todo sign method needs a configured key
        // @todo token id needs to be cached so that a user may revoke it
        // @todo is one hour too short for a jwt? should it not be a longer time?
        //

        $signer = new Sha256();
        $token = (new Builder())->setIssuer('http://127.0.0.1:8080')
                                ->setId('4f1g23a12aa', true)
                                ->setIssuedAt(time())
                                ->setExpiration(time() + 3600)
                                ->set('uid', 1)
                                ->sign($signer, 'testing')
                                ->getToken();

        $jsonResponse = new JsonRenderer([
            'jwt' => (string) $token
        ]);
        $jsonResponse->setLinks([
            'self' => (string)$request->getUri()->getPath()
        ]);
        return $jsonResponse->render($response);
    }
}
