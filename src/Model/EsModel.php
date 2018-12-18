<?php

namespace teg1c\elasticsearchBuilder\Model;

use teg1c\elasticsearchBuilder\Builder\EsClientBuilder;
use Illuminate\Http\Request;
use Log;

class EsModel
{
	protected $index = null;
	protected $type = null;
	protected $params = null;
	protected $query = null;
	protected $id = null;
	protected $selectParams = null;
	protected $sourceStatus = true;
	protected $host = null;
	protected $port = null;
	protected $with = [];
	protected $perPage = 15;
	protected $pagesize = 15;
	protected $firstpagesize = 0;
	protected $page = 1;
	protected $sort = [];
	protected $client = null;
	
	public function __construct($index, $type, $host)
	{
		if (!$host) {
			$config     = config('setting.elasticsearch');
			$this->host = $config['host'];
			$this->port = $config['port'];
		} else {
			$this->host = $host['host'];
			$this->port = $host['port'];
		}
		if (!is_array($index)) {
			$index = (array) $index;
		}
		$type = $type ?? $index;
		if (!$type) {
			$type = $index;
		}
		$this->index  = implode(',', $index);
		$this->type   = implode(',', $type);
		$this->client = EsClientBuilder::create()
		                               ->setHosts($this->host)
		                               ->setPort($this->port)
		                               ->setOrmStatus(true)
		                               ->build();
	}
	
	/*public static function build($index, $type = '')
	{
		if (!is_array($index)) {
			$index = (array) $index;
		}
		$type = $type ?? $index;
		if (!$type) {
			$type = $index;
		}
		return new static($index, $type);
	}*/
	
	public function select(...$selectParams)
	{
		if (!empty($selectParams)) {
			$this->setSourceStatus($selectParams);
		}
		
		return $this;
	}
	
	public function orderBy($sort = [])
	{
		$this->sort = $sort;
		return $this;
	}
	
	public function from($from = 0)
	{
		$this->from = $from;
		return $this;
	}
	
	public function fristpagesize($firstpagesize = 0)
	{
		$this->firstpagesize = $firstpagesize;
		return $this;
	}
	
	public function page($page = 1)
	{
		$this->page = $page;
		return $this;
	}
	
	public function pagesize($pagesize = 15)
	{
		$this->pagesize = $pagesize;
		return $this;
	}
	
	public function where($firstParam, $option, $secondParam = null)
	{
		if (in_array($option, [ "=", ">", ">=", "<", "<=", "like", "==" ])) {
			switch ($option) {
				case "=":
					$this->params[] = [
						"match" => [
							$firstParam => $secondParam
						]
					];
					break;
				
				case "like":
					$firstParam     = explode(',', $firstParam);
					$this->params[] = [
						"multi_match" => [
							"query"  => $secondParam,
							"fields" => $firstParam
						]
					];
					break;
				
				case "==":
					$this->params[] = [
						"match_phrase" => [
							$firstParam => $secondParam
						]
					];
					break;
				
				case ">":
					$this->params[] = [
						"range" => [
							$firstParam => [ "gt" => $secondParam ]
						]
					];
					break;
				
				case ">=":
					$this->params[] = [
						"range" => [
							$firstParam => [ "gte" => $secondParam ]
						]
					];
					break;
				
				case "<":
					$this->params[] = [
						"range" => [
							$firstParam => [ "lt" => $secondParam ]
						]
					];
					break;
				
				case "<=":
					$this->params[] = [
						"range" => [
							$firstParam => [ "lte" => $secondParam ]
						]
					];
					break;
			}
		} else {
			$this->params[] = [
				"match" => [
					$firstParam => $option
				]
			];
		}
		
		return $this;
	}
	
	public function transformHits($result)
	{
		$return_hits['data']  = [];
		$page                 = $this->getQuery();
		$return_hits['total'] = 0;
		if (isset($result['hits']['hits']) && count($result['hits']['hits']) > 0) {
			$hits = $result['hits']['hits'];
			foreach ($hits as $k => $v) {
				$v['_source']['index']    = $v['_index'];
				$v['_source']['index_id'] = array_search($v['_index'], (array) $this->index);
				$return_hits['data'][]    = $v['_source'];
			}
//            $return_hits['data'] = arraySequence($return_hits['data'], 'score');
			$return_hits['total'] = $result['hits']['total'];
		}
		$return_hits['page']     = $page['from'] / $page['size'] + 1;
		$return_hits['pageSize'] = $page['size'];
		return $return_hits;
	}
	
	public function get()
	{
		try {
			$this->client->getParamsBuilder()
			             ->setMethod("search")
			             ->setIndex($this->index)
			             ->setType($this->type)
			             ->setBody($this->getQuery());
			
			$result = $this->client->search();
		} catch (\Exception $e) {
			throw $e;
		}
		$result = $this->transformHits($result['data']);
		return $result;
	}
	
	public function find()
	{
		try {
			$this->client->getParamsBuilder()
			             ->setMethod("search")
			             ->setIndex($this->index)
			             ->setType($this->type)
			             ->setBody($this->getQuery());
			
			$result = $this->client->search();
		} catch (\Exception $e) {
			throw $e;
		}
		$result = ( isset($result['data']['hits']['hits']) && !empty($result['data']['hits']['hits']) ) ? $result['data']['hits']['hits'][0]['_source'] : false;
		return $result;
	}
	
	public function delete($id)
	{
		try {
			$this->client->getParamsBuilder()
			             ->setMethod("delete")
			             ->setIndex($this->index)
			             ->setType($this->type)
			             ->setID($id);
			
			$result = $this->client->delete();
		} catch (\Exception $e) {
			throw $e;
		}
		return $result;
	}
	
	
	public function update($data)
	{
		try {
			$this->client->getParamsBuilder()
			             ->setMethod("update")
			             ->setIndex($this->index)
			             ->setType($this->type)
			             ->setID($data['id'])
			             ->setBody($data['params']);
			
			$result = $this->client->update();
		} catch (\Exception $e) {
			throw $e;
		}
		return $result;
	}
	
	public function insert($data)
	{
		try {
			$this->client->getParamsBuilder()
			             ->setMethod("create")
			             ->setIndex($this->index)
			             ->setType($this->type)
			             ->setID($data['id'])
			             ->setBody($data['params']);
			
			$result = $this->client->create();
		} catch (\Exception $e) {
			throw $e;
		}
		return $result;
	}
	
	public function __toString()
	{
		// TODO: Implement __toString() method.
		return "";
	}
	
	/**
	 * @return mixed
	 */
	public function getIndex()
	{
		return $this->index;
	}
	
	/**
	 * @return mixed
	 */
	public function getType()
	{
		return $this->type;
	}
	
	public function setSourceStatus($status)
	{
		if ($status !== true && !empty($status)) {
			$this->sourceStatus = $status;
		}
	}
	
	public function getQuery()
	{
		$page          = (int) $this->page;
		$pageSize      = (int) $this->pagesize;
		$firstpagesize = (int) $this->firstpagesize;
		$from          = ( $page - 1 ) * $pageSize;
		if ($this->firstpagesize) {
			$from = ( ( $page - 2 ) * $pageSize ) + $firstpagesize;
		}
		$this->query = [
			"_source" => $this->sourceStatus,
			"query"   => [
				"bool" => [
					"must" => [
						$this->params
					]
				]
			],
			'sort'    => $this->sort,
			'size'    => $pageSize,
			'from'    => $from,
		];
		
		return $this->query;
	}
}