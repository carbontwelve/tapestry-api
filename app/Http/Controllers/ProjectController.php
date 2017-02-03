<?php

namespace App\Http\Controllers;

use App\Entity\Project;
use App\Factories\TapestryCoreFactory;
use App\JsonRenderer;
use App\Resources\ProjectResource;
use Slim\Http\Request;
use Slim\Http\Response;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Tapestry\Console\Application;
use Tapestry\Console\DefaultInputDefinition;
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
            'projects' => getArrayCopy($this->projectResource->get())
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

        // @todo if the directory exists then return error or increment the path? e.g. test => test-1...
        mkdir($project->getpath());

        /** @var TapestryCoreFactory $tf */
        $tf = $this->container->get(TapestryCoreFactory::class);
        $tapestry = $tf->build([
            '--site-dir' => $project->getpath()
        ]);

        /** @var Application $cli */
        $cli = $tapestry[Application::class];
        $cli->setAutoExit(false);
        $cli->run(new ArrayInput([
            'command' => 'init',
            '--site-dir' => $project->getpath()
        ], new DefaultInputDefinition), new NullOutput);

        $jsonResponse = new JsonRenderer($project->getArrayCopy());
        $jsonResponse->setLinks([
            'self' => (string)$request->getUri()->getPath()
        ]);
        return $jsonResponse->render($response);
    }

    public function check(Request $request, Response $response, array $args)
    {
        $project = $this->projectResource->findByName($request->getParsedBodyParam('name', null));

        $jsonResponse = new JsonRenderer([
            'exists' => !is_null($project)
        ]);
        $jsonResponse->setLinks([
            'self' => (string)$request->getUri()->getPath()
        ]);
        return $jsonResponse->render($response);
    }
}
