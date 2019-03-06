<?php

namespace teg1c\elasticsearchBuilder\Client;

use teg1c\elasticsearchBuilder\Transport\EsTransport;
use teg1c\elasticsearchBuilder\Builder\EsParamsBuilder;
use Log;

class EsClient
{
    private $transport;
    private $paramsBuilder ;

    public function __construct(EsTransport $transport, EsParamsBuilder $paramsBuilder)
    {
        $this->transport = $transport;
        $this->paramsBuilder = $paramsBuilder;
    }

    public function search($params = array())
    {
        $index = $this->extractArgument($params, 'index');
        $type = $this->extractArgument($params, 'type');
        $body = $this->extractArgument($params, 'body');

        $this->paramsBuilder->setMethod("search")
                            ->setIndex($index)
                            ->setType($type)
                            ->setBody($body);
        try {
            $result = $this->performRequest($this->paramsBuilder);
        } catch (\Exception $e) {
            throw $e;
        }
        return $result;
    }

    public function create($params = array())
    {
        $index = $this->extractArgument($params, 'index');
        $type = $this->extractArgument($params, 'type');
        $id = $this->extractArgument($params, 'id');
        $body = $this->extractArgument($params, 'body');

        $this->paramsBuilder->setMethod("create")
            ->setIndex($index)
            ->setType($type)
            ->setID($id)
            ->setBody($body);
        try {
            $result = $this->performRequest($this->paramsBuilder);
        } catch (\Exception $e) {
            throw $e;
        }
        return $result;
    }


    public function update($params = array())
    {
        $index = $this->extractArgument($params, 'index');
        $type = $this->extractArgument($params, 'type');
        $id = $this->extractArgument($params, 'id');
        $body = $this->extractArgument($params, 'body');

        $this->paramsBuilder->setMethod("update")
            ->setIndex($index)
            ->setType($type)
            ->setID($id)
            ->setBody($body);
        try {
            $result = $this->performRequest($this->paramsBuilder);
        } catch (\Exception $e) {
            throw $e;
        }
        return $result;
    }

    public function delete($params = array())
    {
        $index = $this->extractArgument($params, 'index');
        $type = $this->extractArgument($params, 'type');
        $id = $this->extractArgument($params, 'id');

        $this->paramsBuilder->setMethod("delete")
            ->setIndex($index)
            ->setType($type)
            ->setID($id);
        try {
            $result = $this->performRequest($this->paramsBuilder);
        } catch (\Exception $e) {
            throw $e;
        }
        return $result;
    }


    public function extractArgument(&$params, $arg)
    {
        if(is_object($params) === true){
            $params = (array) $params ;
        }

        if(array_key_exists($arg, $params) === true){
            $val = $params[$arg];
            unset($params[$arg]);
            return $val ;
        } else {
            return null;
        }
    }

    private function performRequest(EsParamsBuilder $paramsBuilder)
    {
        $method = $paramsBuilder->getMethod();
        $uri = $paramsBuilder->getUri();
        $params = [
            "index" => $paramsBuilder->getIndex(),
            "type" => $paramsBuilder->getType(),
            "id" => $paramsBuilder->getID(),
            "body" =>$paramsBuilder->getBody(),
        ];

        try {
            $result = $this->transport->execRequest($method, $uri, $params);
        } catch (\Exception $e) {
            Log::error("Es Client Error: ".$e->getMessage());
            throw $e;
        }
        return $result;
    }
}