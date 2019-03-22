
Elasticsearch for Laravel


### 使用

composer 安装

`composer require teg1c/elasticsearch-for-laravel`

首先创建Model

```php
use teg1c\elasticsearchBuilder\Model\ElasticsearchModel;

/**
 * Class AtPerson
 * $host ES IP或URL地址
 * $port ES 端口
 * @package teg1c\elasticsearchBuilder\Model
 */

class AtPerson extends ElasticsearchModel
{
    protected $host = "127.0.0.1";
    protected $port = "32800";
}

```

然后使用Model对ES进行CURD操作

搜索
```php

try {
    $result = AtPerson::build('index')
              ->select("user")
              ->where("user",'==',"tegic")
              ->where("title,desc","like","AI")
              ->where("create_time","<","2018-10-05")
              ->page(1)
              ->pagesize(15)
              ->orderBy([
              			'score'  => [ 'order' => 'desc' ],
              			 'is_top' => [ 'order' => 'asc' ],
              			])
              ->get();

} catch (\Exception $e) {
    return ['code'=>-1, 'msg'=>$e->getMessage()];
}

return $result;

```
自定义搜索条件
```$xslt
try {
    $result = AtPerson::build('index')
              >customQuery([
              			'match'=>[
              				'title'=>'家庭教育'
              			]
              		])
              ->get();

} catch (\Exception $e) {
    return ['code'=>-1, 'msg'=>$e->getMessage()];
}

return $result;
```
创建mapping
```
$data = [
			'settings' => [
				'number_of_shards' => 3,
				'number_of_replicas' => 2
			],
			'mappings' => [
				'type' => [//这里设置type
					'_source' => [
						'enabled' => true
					],
					'properties' => [
						'id'            => [
							'type' => 'long',
						],
						'title' => [
							'type' => 'text', // 字段类型为全文检索,如果需要关键字,则修改为keyword,注意keyword字段为整体查询,不能作为模糊搜索
							"analyzer"=> "ik_max_word",//需安装中文分词ik_max_word
							"search_analyzer"=> "ik_max_word",
						],
						'body'  =>  [
							'type'  => 'text',
							"analyzer"=> "ik_max_word",
							"search_analyzer"=> "ik_max_word",
						]
					]
				]
			]
		];
		$res = AtPerson::build('index')->createMapping($data);
```
新增
```php

try {
    $id = 5;
    $data = [
       'id'=>$id,
       'params'=>[
            'user'=>'tegic',
            'title'=>'AI '.str_random(8),
            'desc'=>'AI '.str_random(12)
       ]
    ];
    $result = AtPerson::build('index')->insert($data);
} catch (\Exception $e) {
    return ['code'=>-1, 'msg'=>$e->getMessage()];
}

return $result;

```

更新
```php

try {
    $id = 5;
    $data = [
        'id'=>$id,
        'params'=>[
             'user'=>'tegic',
             'title'=>'AI '.str_random(8),
             'desc'=>'AI '.str_random(12)
        ]
    ];
    $result = AtPerson::build('index')->update($data);
} catch (\Exception $e) {
    return ['code'=>-1, 'msg'=>$e->getMessage()];
}

return $result;

```


删除
```php

try {
    $id = 5;
    $result = AtPerson::build('index')->delete($id);
} catch (\Exception $e) {
    throw $e;
}
     
return $result;

```

