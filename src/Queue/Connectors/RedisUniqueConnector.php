<?php

namespace Mlntn\Queue\Connectors;

use Mlntn\Queue\RedisUniqueQueue;
use Illuminate\Queue\Connectors\RedisConnector;

class RedisUniqueConnector extends RedisConnector
{

    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function connect(array $config)
    {
        return new RedisUniqueQueue(
            $this->redis, $config['queue'],
            $config['connection'] ?? $this->connection,
            $config['retry_after'] ?? 60,
            $config['block_for'] ?? null
        );
    }

}