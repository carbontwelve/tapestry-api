<?php

namespace App\Http\Controllers;

use App\Definitions\File;
use App\Definitions\JsonDefinition;
use App\Definitions\Path;
use App\JsonRenderer;
use App\Resources\ProjectResource;
use Slim\Http\Request;
use Slim\Http\Response;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Finder\SplFileInfo;
use Tapestry\Entities\ContentType;
use Tapestry\Modules\Kernel\BootKernel;

class FilesystemController extends BaseController
{
    /**
     * @var ProjectResource
     */
    private $projectResource;

    /**
     * ProjectController constructor.
     * @param ProjectResource $projectResource
     */
    public function __construct(ProjectResource $projectResource)
    {
        $this->projectResource = $projectResource;
    }

    /**
     * @todo add pagination, for when we have a lot of files to list...
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface|Response
     */
    public function all(Request $request, Response $response, array $args)
    {
        if (! $project = $this->projectResource->get($args['project'])) {
            return $this->abort($response, 'Project Not Found');
        }

        $this->bootProject(new NullOutput(), $project);

        $files = [];

        /** @var \Tapestry\Entities\File $tapestryFile */
        foreach ($this->project['files'] as $tapestryFile) {
            /** @var File $file */
            $file = new File($tapestryFile, $project, $this->container);
            array_push($files, $file->toJsonResponse());
        }

        $jsonResponse = new JsonRenderer($files);
        $jsonResponse->setLinks([
            'self' => (string)$request->getUri()->getPath(),
        ]);
        $jsonResponse->inheritLinks();
        return $jsonResponse->render($response);
    }

    public function path(Request $request, Response $response, array $args)
    {
        if (! $project = $this->projectResource->get($args['project'])) {
            return $this->abort($response, 'Project Not Found');
        }

        $this->bootProject(new NullOutput(), $project);

        $path = (isset($args['id']) ? base64_decode($args['id']) : "");

        if (realpath($this->project->sourceDirectory . DIRECTORY_SEPARATOR . $path) === false){
                return $response->withStatus(404);
        }

        $path = new Path($path, $this->container);
        $path = $path->withPathRelationship()
            ->withProjectFileRelationship();
        $jsonResponse = new JsonRenderer([$path->toJsonResponse()]);
        $jsonResponse->inheritLinks();
        return $jsonResponse->render($response);
    }

    public function view(Request $request, Response $response, array $args)
    {
        if (! $project = $this->projectResource->get($args['project'])) {
            return $this->abort($response, 'Project Not Found');
        }

        $this->bootProject(new NullOutput(), $project);

        /** @var \Tapestry\Entities\File $tapestryFile */
        if (! $tapestryFile = $this->project['files.' . $args['id']]) {
            return $response->withStatus(404);
        }

        /** @var File $file */
        $file = new File($tapestryFile, $project, $this->container);
        $file = $file->withDirectoryRelationship();

        $file = $file->apply(function(JsonDefinition $definition){
            $definition->unsetLink('self');
            return $definition;
        });

        $jsonResponse = new JsonRenderer(['data' => $file->toJsonResponse()]);
        $jsonResponse->setLinks([
            'self' => (string)$request->getUri()->getPath(),
        ]);
        $jsonResponse->inheritLinks();
        return $jsonResponse->render($response);
    }

    public function create(Request $request, Response $response, array $args)
    {
        if (! $project = $this->projectResource->get($args['project'])) {
            return $this->abort($response, 'Project Not Found');
        }

        $this->bootProject(new NullOutput(), $project);

        $file = $request->getParsedBodyParam('file');

        //
        // Validate Content Type
        //
        /** @var ContentType $contentType */
        if (! $contentType = $this->project['content_types.' . $file['attributes']['contentType']]) {
            return $this->abort($response, 'Invalid Content Type');
        }

        //
        // Validate Path
        //
        $path = $this->project->sourceDirectory . DIRECTORY_SEPARATOR . $file['attributes']['path'];
        if (strlen($contentType->getPath()) > 0 && $contentType->getPath() !== '*') {
            if (strpos($file['attributes']['path'], $contentType->getPath()) === false) {
                return $this->abort($response, 'Invalid Path ['. $file['attributes']['path'] .'] for File of Content Type ['. $contentType->getName() .']');
            }
        }

        //
        // Generate the filename if needed
        //
        if (empty($file['attributes']['name']) || $file['attributes']['name'] === 'untitled') {
            $date = new \DateTime('@' . $file['attributes']['date']);
            $file['attributes']['name'] = $date->format('Y-m-d') . '-' . $file['attributes']['frontMatter']['title'];
        }

        $fileName = str_slug($file['attributes']['name'], '-') . '.' . $file['attributes']['ext'];
        $pathName = $path . DIRECTORY_SEPARATOR . $fileName;

        //
        // Check if file already exists
        //
        if (file_exists($pathName)) {
            return $this->abort($response, 'A file with that name already exists.', 409);
        }

        // Touch file and check its writable
        if (file_put_contents($pathName, '') === false) {
            return $this->abort($response, 'File could not be saved.', 500);
        }

        // Reboot Project to pickup the new file
        $this->bootProject(new NullOutput(), $project);
        $splFile = new SplFileInfo($pathName, '', $contentType->getPath() . DIRECTORY_SEPARATOR . $fileName);

        /** @var \Tapestry\Entities\File $tapestryFile */
        if (! $tapestryFile = $this->project['files.' . $this->cleanUid($splFile->getRelativePathname())]) {
            return $this->abort($response, 'There was a problem while saving that file.', 500);
        }

        /** @var File $file */
        $file = new File($tapestryFile, $project, $this->container);
        $file = $file->merge($request->getParsedBodyParam('file'));

        if ($file->save() === false) {
            return $this->abort($response, 'File could not be saved.', 500);
        }

        $jsonResponse = new JsonRenderer(['data' => $file->toJsonResponse()]);
        $jsonResponse->setLinks([
            'self' => (string)$request->getUri()->getPath(),
        ]);
        return $jsonResponse->render($response);
    }

    public function update(Request $request, Response $response, array $args)
    {
        if (! $project = $this->projectResource->get($args['project'])) {
            return $this->abort($response, 'Project Not Found');
        }

        $this->bootProject(new NullOutput(), $project);

        /** @var \Tapestry\Entities\File $tapestryFile */
        if (! $tapestryFile = $this->project['files.' . $args['id']]) {
            return $response->withStatus(404);
        }

        /** @var File $file */
        $file = new File($tapestryFile, $project, $this->container);
        $file = $file->merge($request->getParsedBodyParam('file'));

        if ($file->save() === false) {
            return $this->abort($response, 'File could not be saved.');
        }

        $jsonResponse = new JsonRenderer(['data' => $file->toJsonResponse()]);
        $jsonResponse->setLinks([
            'self' => (string)$request->getUri()->getPath(),
        ]);
        return $jsonResponse->render($response);
    }

    public function destroy(Request $request, Response $response, array $args)
    {
        // ...
    }

    private function cleanUid($uid)
    {
        $uid = str_replace('.', '_', $uid);
        $uid = str_replace(['/', '\\'], '_', $uid);

        return $uid;
    }
}
