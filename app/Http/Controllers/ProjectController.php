<?php

namespace App\Http\Controllers;

use App\Entity\Project;
use App\JsonRenderer;
use App\Resources\ProjectResource;
use Slim\Http\Request;
use Slim\Http\Response;
use Tapestry\Tapestry;

class ProjectController extends BaseController
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

    public function index(Request $request, Response $response, array $args)
    {
        $jsonResponse = new JsonRenderer([
            'tapestryVersion' => Tapestry::VERSION,
            'projects' => $this->projectResource->get()
        ]);
        $jsonResponse->setLinks([
            'self' => (string)$request->getUri()->getPath()
        ]);
        return $jsonResponse->render($response);
    }

    public function create(Request $request, Response $response, array $args)
    {
        $project = new Project();
        $project->setName($request->getParsedBodyParam('name', null));
        $project->setPath(APP_BASE . '/storage/projects/' . str_slug($project->getName()));
        $this->projectResource->save($project);
    }
}
