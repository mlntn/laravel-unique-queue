<?php

namespace Mlntn\Providers;

use Mlntn\Queue\Connectors\RedisUniqueConnector;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\ServiceProvider;

/**
 * LumenQueueServiceProvider
 *
 * Lumen binds queue factory onto "queue" key, differently to Laravel.
 * See Laravel\Lumen\Application::registerQueueBindings()
 *
 * @package Mlntn\Providers
 */
class LumenQueueServiceProvider extends ServiceProvider
{
    /**
     * Register unique queuing using Mlntn\Queue\Connectors\RedisUniqueConnector
     *
     * @return void
     */
    public function register()
    {
        // Make sure $app['redis'] is available
        $this->app->resolving('queue', function (QueueManager $manager) {
            $manager->addConnector('unique', function () {
                return new RedisUniqueConnector($this->app['redis']);
            });
        });
    }
}