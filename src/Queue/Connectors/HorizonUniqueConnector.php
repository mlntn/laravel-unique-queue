<?php

namespace Mlntn\Queue\Connectors;

use Illuminate\Support\Arr;
use Mlntn\Queue\HorizonUniqueQueue;
use Laravel\Horizon\Connectors\RedisConnector;

class HorizonUniqueConnector extends RedisConnector
{

    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function connect(array $config)
    {
        return new HorizonUniqueQueue(
            $this->redis, $config['queue'],
            Arr::get($config, 'connection', $this->connection),
            Arr::get($config, 'retry_after', 60),
            Arr::get($config, 'block_for', null)
        );
    }

}