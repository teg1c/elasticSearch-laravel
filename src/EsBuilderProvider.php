<?php

namespace teg1c\elasticsearchBuilder;

use teg1c\elasticsearchBuilder\Builder\EsClientBuilder;
use Illuminate\Support\ServiceProvider;

class EsBuilderProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app['esbuilder'] = $this->app->share(function ($app) {
            return EsClientBuilder::create();
        });
    }

    public function provides()
    {
        return ['esbuilder'];
    }
}
