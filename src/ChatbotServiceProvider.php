<?php

namespace Neko\Chatbot;

use Neko\Chatbot\Cache\LaravelCache;
use Neko\Chatbot\Container\LaravelContainer;
use Neko\Chatbot\Storages\Drivers\FileStorage;
use Illuminate\Support\ServiceProvider;

class ChatbotServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../assets/config.php' => config_path('botman/config.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../assets/config.php', 'botman.config');

        $this->app->singleton('botman', function ($app) {
            $storage = new FileStorage(storage_path('botman'));

            $botman = BotManFactory::create(
                config('botman', []),
                new LaravelCache(),
                $app->make('request'),
                $storage
            );

            $botman->setContainer(new LaravelContainer($this->app));

            return $botman;
        });
    }
}
