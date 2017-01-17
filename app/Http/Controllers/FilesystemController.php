<?php

namespace App\Http\Controllers;

use App\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class FilesystemController extends BaseController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        /** @var \League\Flysystem\MountManager $filesystem */
        $filesystem = $this->tapestry[\League\Flysystem\MountManager::class];

        $jsonResponse = new JsonRenderer($filesystem->listContents('cwd://', true));
        $jsonResponse->setLinks([
            'self' => (string)$request->getUri()
        ]);
        return $jsonResponse->render($response);
    }

    public function view(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // ...
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // ...
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // ...
    }

    public function destroy(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // ...
    }
}
