<?php

namespace teg1c\elasticsearchBuilder\Http;

use Log;

class HttpClient
{
    private $ch ;
    private $uri ;
    private $method ;
    private $params;

    public function Get($data)
    {
        $data['method'] = "GET";
        return $this->HttpRequest($data);
    }

    public function Post($data)
    {
        $data['method'] = "POST";
        return $this->HttpRequest($data);
    }

    public function Put($data)
    {
        $data['method'] = "PUT";
        return $this->HttpRequest($data);
    }

    public function Delete($data)
    {
        $data['method'] = "DELETE";
        return $this->HttpRequest($data);
    }

    /**
     * @param $method
     * @param $data ['url']
     * @return array
     */
    public function HttpRequest($data)
    {
        $this->ch = curl_init();
        $uri = $data['uri'];
        try {
            $this->dataValication($data);
        } catch (\Exception $e) {
            return ['code'=>-1, 'msg'=>$e->getMessage()];
        }

        $headers[] ='X-HTTP-Method-Override: '.$data['method'];
        $headers[] ='Content-Type: application/json';
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 500);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $this->method); //设置请求方式
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->params);
        curl_setopt($this->ch, CURLOPT_URL, $this->uri);
        $result = curl_exec($this->ch);
        if(!curl_errno($this->ch)){
            $info = curl_getinfo($this->ch);
        }else{
            return ['code'=>-1, 'msg'=>"请求 $uri 出错: Curl error: ". curl_error($this->ch)];
        }

        curl_close($this->ch);
        return ['code'=>0, 'msg'=>'OK', 'data'=>json_decode($result,true)];
    }

    public function dataValication($data)
    {
        if(!isset($data['uri']) || empty($data['uri'])){
            throw new \Exception("HttpClient Error: Uri不能为空", 4422);
        }else{
            $this->uri = $data['uri'];
        }

        if(!isset($data['params']) || empty($data['params'])){
            $this->params = [];
        }else{
            $this->params = $data['params'];
        }

        if(!isset($data['method']) || empty($data['method'])){
            $this->method = "POST";
        }else{
            $this->method = $data['method'];
        }
    }
}