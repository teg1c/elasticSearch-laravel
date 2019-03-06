<?php

namespace teg1c\elasticsearchBuilder\Builder;

use http\Exception\UnexpectedValueException;

class EsParamsBuilder
{
    protected $params = array();
    protected $index = null ;
    protected $type = null ;
    protected $id = null ;
    protected $method = null ;
    protected $body = null ;
    protected $uri = null;
    protected $options = [];

    public function __construct()
    {
    }

    public function setParams($params)
    {
        if (is_object($params) === true) {
            $params = (array) $params;
        }

        $this->checkUserParams($params);
        return $this;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getIndex()
    {
        return $this->index;
    }

    public function setIndex($index)
    {
        if ($index === null) {
            return $this;
        }

        if (is_array($index) === true) {
            $index = array_map('trim', $index);
            $index = implode(',', $index);
        }

        $this->index = urlencode($index);
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        if ($type === null) {
            return $this;
        }

        if (is_array($type) === true) {
            $type = array_map('trim', $type);
            $type = implode(",", $type);
        }

        $this->type = urlencode($type);
        return $this;
    }

    public function setID($docID)
    {
        if (empty($docID)) {
            return $this;
        }

        $this->id = urlencode($docID);
        return $this;
    }

    public function getID()
    {
        return $this->id;
    }

    public function setBody($body)
    {
        if (isset($body) !== true) {
            return $this;
        }
        $this->body = $body;
        return $this;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setMethod($method)
    {
        if (empty($method)) {
            return $this;
        }

        $this->method = $method;
        return $this;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setUri($uri)
    {
        if (empty($uri)) {
            return $this;
        }

        $this->uri = $uri;
        return $this;
    }

    public function getUri()
    {
        return $this->uri;
    }


    private function checkUserParams($params)
    {
        if (empty($params) === true) {
            throw new UnexpectedValueException("输入参数有误!");
        }
    }
}