<?php

namespace Pantherify\GraphQLGenerator;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Factory as ValidationFactory;
use Pantherify\GraphQLGenerator\Commands\GraphQLGeneratorCommand;

class GraphQLGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'graphql-generator');

        if ($this->app->runningInConsole()) {
            $this->commands([
                GraphQLGeneratorCommand::class
            ]);
        }
    }

    /**
     * Bootstrap services.
     *
     * @param ValidationFactory $validationFactory
     * @param ConfigRepository $configRepository
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot(ValidationFactory $validationFactory, ConfigRepository $configRepository)
    {
        $this->publishes([
            __DIR__ . '/../config/config.php' => $this->app->make('path.config') . DIRECTORY_SEPARATOR . 'graphql-generator.php',
        ], 'config');

        $this->publishes([__DIR__ . '../resources/_lighthouse_directives.graphql' => $configRepository->get('lighthouse.schema.register')], '');

        $this->loadViewsFrom(__DIR__ . '/Views', 'graphql-gen');
    }
}

