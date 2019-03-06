<?php
namespace teg1c\elasticsearchBuilder\Facades;

class EsBuilder extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return 'esbuilder';
    }
}