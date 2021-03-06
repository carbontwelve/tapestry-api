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
        if (isset($data['data'])){
            $this->data = $data['data'];
        } else {
            foreach ($data as $key => $value) {
                if ($key === 'data'){
                    array_push($this->data, $value);
                    continue;
                }
                $this->data[$key] = $value;
            }
        }
        $this->errors = $errors;
    }

    public function inheritLinks()
    {
        if (is_array($this->data)) {
            if (count($this->data) === 1 && isset($this->data[0]->links)) {
                $this->links = array_merge($this->links, $this->data[0]->links);
                unset($this->data[0]->links);
            }
            return;
        }

        if (is_object($this->data)) {
            if (isset($this->data->links)) {
                $this->links = array_merge($this->links, $this->data->links);
                unset($this->data->links);
            }
        }
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

        $json->profile = [
            'executionTime' => round((microtime(true) - APP_START), 3) . 's',
            'memoryUsage' => file_size_convert(memory_get_usage(true)),
            'peakMemoryUsage' => file_size_convert(memory_get_peak_usage(true))
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