<?php

namespace HoangDo\JsonApi\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use HoangDo\JsonApi\Format\BasicJsonApiResponseFactory;
use HoangDo\JsonApi\Format\JsonApiResponseFactory;
use HoangDo\JsonApi\Middlewares\JsonResponseMiddleware;

class JsonApiServiceProvider extends ServiceProvider
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../resources/config.php', 'json-api');

        $configs = $this->app['config']->get('json-api');
        /** @var string $middlewareName Can dynamic config middleware name by env
         *  because the middleware may be duplicate register by multiple packages
         *  default name is "json"
         */
        $middlewareName = $configs['middleware_name'];
        $middlewareNames = Arr::wrap($middlewareName);
        foreach ($middlewareNames as $name) {
            if (!empty($name)) {
                $this->app['router']->aliasMiddleware($name, JsonResponseMiddleware::class);
            }
        }

        /**
         * The default configuration will use {@link BasicJsonApiResponseFactory} as response factory
         * All the data will be formatted with the basic formatter.
         */
        $factoryClassName = $configs['response_factory'];
        $this->app->singleton(JsonApiResponseFactory::class, $factoryClassName);
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../resources/config.php' => App::configPath('json-api.php'),
            ]);
        }
    }
}