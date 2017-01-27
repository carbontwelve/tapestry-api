<?php

namespace App\Http\Controllers;

use App\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Request;
use Tapestry\Tapestry;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class ProjectController extends BaseController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args)
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
}
