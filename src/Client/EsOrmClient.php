<?php

namespace teg1c\elasticsearchBuilder\Client;

use teg1c\elasticsearchBuilder\Transport\EsTransport;
use teg1c\elasticsearchBuilder\Builder\EsParamsBuilder;
use Log;

class EsOrmClient
{
    private $index = null ;
    private $type = null ;
    private $params = null ;

    private $transport;
    private $paramsBuilder ;

    public function __construct(EsTransport $transport, EsParamsBuilder $paramsBuilder)
    {
        $this->transport = $transport;
        $this->paramsBuilder = $paramsBuilder;

    }

    public function from($type)
    {
        if (!empty($type)&&strrpos($type,".")) {
            list($this->index, $this->type) = explode('.',$type);
        }
        return $this;
    }

    public function where($firstParam, $option, $secondParam)
    {
        if (in_array($option, array("=", ">", "<", "like", "%", "=="))) {
            switch ($option) {
                case "=":
                    $this->params = [
                        "query"=>[
                            "bool"=>[
                                "must"=>[
                                    [
                                        "match"=>[
                                            $firstParam => $secondParam
                                        ]
                                    ]

                                ]
                            ]
                        ]
                    ];
                    break;
            }
        }

        return $this ;
    }

    public function search()
    {
        try {
            $result = $this->performRequest($this->paramsBuilder);
        } catch (\Exception $e) {
            throw $e;
        }
        return $result;
    }

    public function update()
    {
        try {
            $result = $this->performRequest($this->paramsBuilder);
        } catch (\Exception $e) {
            throw $e;
        }
        return $result;

    }

    public function delete()
    {
        try {
            $result = $this->performRequest($this->paramsBuilder);
        } catch (\Exception $e) {
            throw $e;
        }
        return $result;
    }

    public function create()
    {
        try {
            $result = $this->performRequest($this->paramsBuilder);
        } catch (\Exception $e) {
            throw $e;
        }
        return $result;
    }

    public function getParamsBuilder()
    {
        return $this->paramsBuilder;
    }

    public function extractArgument(&$params, $arg)
    {
        if (is_object($params) === true) {
            $params = (array) $params;
        }

        if (array_key_exists($arg, $params) === true) {
            $val = $params[$arg];
            unset($params[$arg]);
            return $val;
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