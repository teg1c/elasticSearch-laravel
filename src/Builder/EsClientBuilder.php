<?php

namespace teg1c\elasticsearchBuilder\Builder;

use teg1c\elasticsearchBuilder\Transport\EsTransport;
use teg1c\elasticsearchBuilder\Client\EsClient;
use teg1c\elasticsearchBuilder\Client\EsOrmClient;
use BadMethodCallException;

class EsClientBuilder
{
    private $hosts;
    private $port ;
    private $ormStatus ;
    private $version;
    private $paramsBuilder;
    private $esTransport;

    public function __construct()
    {
        $this->paramsBuilder = new EsParamsBuilder();
        $this->esTransport = new EsTransport();
        $this->version = "1.0";
    }

    public static function create()
    {
        return new static();
    }


    public function setHosts($hosts)
    {
        $this->hosts = $hosts;
        return $this;
    }

    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    public function setOrmStatus($ormStatus)
    {
        if ($ormStatus === true) {
            $this->ormStatus = true;
        }
        return $this;
    }


    public function build()
    {
        $uri = $this->hosts.":".$this->port;
        $this->paramsBuilder->setUri($uri);
        if ($this->ormStatus === true) {
            return new EsOrmClient($this->esTransport, $this->paramsBuilder);
        }else{
            return new EsClient($this->esTransport, $this->paramsBuilder);
        }
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        throw new BadMethodCallException("该版本中".$this->version." $name 不支持");
    }

    public function getVersion()
    {
        return $this->version;
    }

}