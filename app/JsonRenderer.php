<?php

namespace App;

use \Psr\Http\Message\ResponseInterface;

/**
 * Class JsonRenderer to meet jsonapi.org specification
 * @package App
 */
class JsonRenderer
{

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var array
     */
    private $links = [];

    /**
     * @var array
     */
    private $meta = [];

    public function __construct(array $data, array $errors = [])
    {
        $this->data = $data;
        $this->errors = $errors;
    }

    public function setLinks(array $links)
    {
        $this->links = $links;
    }

    public function setMeta(array $meta)
    {
        $this->meta = $meta;
    }

    /**
     * @param ResponseInterface|\Slim\Http\Response $response
     * @param int $status
     * @return \Slim\Http\Response|\Psr\Http\Message\ResponseInterface
     */
    public function render(ResponseInterface $response, $status = 200)
    {
        $json = new \stdClass();
        $json->jsonapi = [
            'version' => '1.0'
        ];

        if (count($this->links) > 0) {
            $json->links = $this->links;
        }

        $json->data = $this->data;

        if (count($this->errors) > 0) {
            $json->errors = $this->errors;
        }

        if (count($this->meta) > 0) {
            $json->meta = $this->meta;
        }

        return $response->withJson($json, $status);
    }
}