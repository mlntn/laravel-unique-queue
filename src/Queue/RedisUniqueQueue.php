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

        $tracker = $this->getTrackerName($queue);

        $data = json_decode($payload, true);

        $exists = $connection->hexists($tracker, $data['uniqueIdentifier']);

        if ($exists) {
            return null;
        }

        if (parent::pushRaw($payload, $queue, $options)) {
            $connection->hset($tracker, $data['uniqueIdentifier'], $data['id']);
        }
    }

    /**
     * Create a payload for an object-based queue handler.
     *
     * @param  mixed  $job
     * @return array
     */
    protected function createObjectPayload($job,$queue)
    {
        return array_merge([
            'uniqueIdentifier' => isset($job->class)
                ? (new $job->class)->getUniqueIdentifier()
                : $job->getUniqueIdentifier(),
        ], parent::createObjectPayload($job,$queue));
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

        $data = json_decode($job->getRawBody(), true);

        $this->getConnection()->hdel($this->getTrackerName($queue), $data['uniqueIdentifier']);
    }

    protected function getTrackerName($queue)
    {
        return $this->getQueue($queue).':tracker';
    }

}
