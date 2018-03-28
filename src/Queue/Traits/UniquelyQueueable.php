<?php

namespace Mlntn\Queue\Traits;

trait UniquelyQueueable
{

    /**
     * Provides a unique identifier for existence check
     * This should be implemented in each job
     *
     * @return string
     */
    abstract public function getUniqueIdentifier();

}