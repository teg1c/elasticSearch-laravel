<?php

namespace teg1c\elasticsearchBuilder\Transport;

use teg1c\elasticsearchBuilder\Http\HttpClient;
use Log;

class EsTransport
{
    private $uri ;
    private $httpClient ;

    public function __construct()
    {
        $this->httpClient = new HttpClient();
    }

    public function execRequest($method, $uri, $params = null)
    {
        $method = strtolower($method);
        $this->uri = $uri;
        try {
            $result = $this->$method($params);
        } catch (\Exception $e) {
            Log::error("Es Transport Error: ".$e->getMessage());
            throw $e ;
        }

        return $result;
    }

    public function search($params)
    {
        $uri = $this->uri.'/'.$params['index'].'/'.$params['type'].'/_search';
        try {
            $result = $this->httpClient->Post([
                'uri'=>$uri,
                'params'=>json_encode($params['body'])
            ]);
        } catch (\Exception $e) {
            Log::error("Es Transport Search Error: ".$e->getMessage());
            throw $e;
        }

        return $result;
    }

    public function create($params)
    {
        $uri = $this->uri.'/'.$params['index'].'/'.$params['type'].'/'.$params['id'];
        try {
            $result = $this->httpClient->Put([
                'uri'=>$uri,
                'params'=>json_encode($params['body'])
            ]);
        } catch (\Exception $e) {
            Log::error("Es Transport Create Error: ".$e->getMessage());
            throw $e;
        }

        return $result;
    }

    public function update($params)
    {
        $uri = $this->uri.'/'.$params['index'].'/'.$params['type'].'/'.$params['id'];
        try {
            $result = $this->httpClient->Put([
                'uri'=>$uri,
                'params'=>json_encode($params['body'])
            ]);
        } catch (\Exception $e) {
            Log::error("Es Transport Update Error: ".$e->getMessage());
            throw $e;
        }

        return $result;
    }

    public function delete($params)
    {
        $uri = $this->uri.'/'.$params['index'].'/'.$params['type'].'/'.$params['id'];
        try {
            $result = $this->httpClient->Delete([
                'uri'=>$uri
            ]);
        } catch (\Exception $e) {
            Log::error("Es Transport Create Error: ".$e->getMessage());
            throw $e;
        }

        return $result;
    }
}