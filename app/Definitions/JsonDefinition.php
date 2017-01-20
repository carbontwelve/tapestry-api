<?php

namespace App\Definitions;

class JsonDefinition
{

    protected $id;

    protected $type;

    protected $attributes = [];

    protected $isRelative = false;

    /**
     * @var array|JsonDefinition[]
     */
    protected $relationships = [];

    protected $links = [];

    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function setLink($name, $url)
    {
        $this->links[$name] = $url;
    }

    public function setRelationship(JsonDefinition $relative)
    {
        $relative->isRelative = true;
        $this->relationships[$relative->id] = $relative;
    }

    public function getId()
    {
        return $this->id;
    }

    public function toJsonResponse()
    {
        $tmp = new \stdClass();
        if (count($this->links) > 0) {
            $tmp->links = $this->links;
        }

        $data = [
            'id' => $this->id,
            'type' => $this->type
        ];
        if (count($this->attributes) > 0) {
            $data['attributes'] = $this->attributes;
        }
        if (count($this->relationships) > 0) {
            $data['relationships'] = [];
            foreach ($this->relationships as $relationship) {
                $data['relationships'][$relationship->getId()] = $relationship->toJsonResponse();
            }
        }

        if ($this->isRelative === true) {
            $tmp->data = [];
            foreach ($data as $key => $value) {
                $tmp->data[$key] = $value;
            }
            return $tmp;
        }

        foreach ($data as $key => $value) {
            $tmp->{$key} = $value;
        }
        return $tmp;
    }
}