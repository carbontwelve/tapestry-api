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

    public function isRelative()
    {
        return $this->isRelative();
    }

    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
        $this->updateRelationships();
    }

    public function setLink($name, $url)
    {
        $this->links[$name] = $url;
        $this->updateRelationships();
    }

    public function unsetLink($name){
        unset($this->links[$name]);
        $this->updateRelationships();
    }

    public function setRelationship(JsonDefinition $relative)
    {
        $relative->isRelative = true;
        $this->relationships[$relative->id] = $relative;
        $this->updateRelationships();
    }

    public function getRelationships()
    {
        return $this->relationships;
    }

    public function getRelationship($id)
    {
        if (! isset($this->relationships[$id])) {
            return null;
        }

        $tmp = $this->relationships[$id];
        $tmp->isRelative = false;
        return $tmp;
    }

    public function unsetRelationship($id)
    {
        unset($this->relationships[$id]);
        $this->updateRelationships();
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
            foreach ($this->relationships as $k => $relationship) {
                if (is_array($relationship)){
                    /**
                     * @var  string $relationshipK
                     * @var  JsonDefinition $relationshipV
                     */
                    foreach ($relationship as $relationshipK => $relationshipV) {
                        $data['relationships'][$k][$relationshipV->getId()] = $relationshipV->toJsonResponse();
                    }
                    continue;
                }

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

    protected function updateRelationships()
    {
        foreach($this->relationships as $relationship) {
            if (isset($this->links['related'])) {
                $relationship->setLink('related', $this->links['related'] . '/' . $relationship->getId());
            }
        }
    }

    /**
     * Apply the $filter closure upon this and return the result as a cloned object.
     * @param \Closure $filter
     * @return JsonDefinition
     * @throws \Exception
     */
    public function apply(\Closure $filter) {
        $clone = clone($this);
        $response = $filter($clone);

        if (! $response instanceof JsonDefinition) {
            throw new \Exception('The closure being applied must return an instance of JsonDefinition.');
        }

        return $response;
    }

}