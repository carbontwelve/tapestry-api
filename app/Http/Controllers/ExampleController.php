<?php

namespace App\Http\Controllers;

use App\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ExampleController extends BaseController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $jsonResponse = new JsonRenderer([

            'content-types', // foreach content-type show number of items, drafts, etc.
            'filesystem'

        ]);
        $jsonResponse->setLinks([
            'self' => (string)$request->getUri()->getPath()
        ]);
        return $jsonResponse->render($response);
    }
}
