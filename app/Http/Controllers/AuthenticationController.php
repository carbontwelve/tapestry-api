<?php

namespace App\Http\Controllers;

use App\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthenticationController extends BaseController
{
    public function authenticate(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // ...
    }
}
