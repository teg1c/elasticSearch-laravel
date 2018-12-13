
Elasticsearch for Laravel


### 使用

首先创建Model

```php
use teg1c\elasticsearchBuilder\Model\EsModel;

/**
 * Class AtPerson
 * $host ES IP或URL地址
 * $port ES 端口
 * @package Ethan\EsBuilder\Model
 */

class AtPerson extends EsModel
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

