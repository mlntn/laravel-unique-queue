<?php

namespace Mlntn\Queue;

use Illuminate\Queue\LuaScripts;
use Illuminate\Queue\Jobs\RedisJob;
use Illuminate\Queue\RedisQueue;

class RedisUniqueQueue extends RedisQueue
{

    /**
     * Push a raw payload onto the queue.
     *
     * @param  string  $payload
     * @param  string  $queue
     * @param  array   $options
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = [])
    {
        $connection = $this->getConnection();

        $data = json_decode($payload, true);

        $exists = $connection->hexists($this->getQueue($queue) . ':tracker', $data['uniqueIdentifier']);

        if ($exists) {
            return null;
        }

        $connection->hset($this->getQueue($queue) . ':tracker', $data['uniqueIdentifier'], $data['id']);

        return parent::pushRaw($payload, $queue, $options);
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param  string  $queue
     * @return \Illuminate\Contracts\Queue\Job|null
     */
    public function pop($queue = null)
    {
        $this->migrate($prefixed = $this->getQueue($queue));

        list($job, $reserved) = $this->retrieveNextJob($prefixed);

        if ($reserved) {
            return new RedisJob(
                $this->container, $this, $job,
                $reserved, $this->connectionName, $queue ?: $this->default
            );
        }
    }

    /**
     * Create a payload for an object-based queue handler.
     *
     * @param  mixed  $job
     * @return array
     */
    protected function createObjectPayload($job)
    {
        return array_merge([
            'uniqueIdentifier' => $job->getUniqueIdentifier(),
        ], parent::createObjectPayload($job));
    }

    /**
     * Delete a reserved job from the queue.
     *
     * @param  string  $queue
     * @param  RedisJob  $job
     * @return void
     */
    public function deleteReserved($queue, $job)
    {
        parent::deleteReserved($queue, $job);

        $this->getConnection()->zrem($this->getQueue($queue).':reserved', $job->getReservedJob());

        $data = json_decode($job->getRawBody(), true);

        $this->getConnection()->hdel($this->getQueue($queue).':tracker', $data['uniqueIdentifier']);
    }

}