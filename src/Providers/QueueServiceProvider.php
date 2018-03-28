<?php

namespace Mlntn\Providers;

use Mlntn\Queue\Connectors\RedisUniqueConnector;

class QueueServiceProvider extends \Illuminate\Queue\QueueServiceProvider
{

    /**
     * Register the connectors on the queue manager.
     *
     * @param  \Illuminate\Queue\QueueManager  $manager
     * @return void
     */
    public function registerConnectors($manager)
    {
        parent::registerConnectors($manager);

        $this->registerRedisUniqueConnector($manager);
    }

    /**
     * Register the Redis unique queue connector.
     *
     * @param  \Illuminate\Queue\QueueManager  $manager
     * @return void
     */
    protected function registerRedisUniqueConnector($manager)
    {
        $manager->addConnector('unique', function () {
            return new RedisUniqueConnector($this->app['redis']);
        });
    }

}
