This redis queue driver works just like the standard Laravel redis queue driver, however, it prevents the same job from being queued multiple times.

## REQUIREMENTS

Needs PHP >= 7.1 to be installed.

Requires `illuminate/redis` and `illuminate/queue`, both `"~5.7"`

## INSTALLATION

### Require via Composer
```
composer require mlntn/laravel-unique-queue
```


### Configure
Create a new connection in *config/queue.php*

```
return [
    // ...
    'connections' => [
        'my_unique_name' => [
            'driver'      => 'unique',
            'connection'  => 'default',
            'queue'       => env('UNIQUE_QUEUE_NAME', 'give-me-a-name'),
            'retry_after' => 90,
        ],
        //...
    ]
];
```

## IMPLEMENTATION

### Implement a uniquely-queueable job

Your job should use the UniquelyQueueable trait:

    <?php
    
    namespace App\Jobs;
    
    use Illuminate\Bus\Queueable;
    use Illuminate\Queue\SerializesModels;
    use Illuminate\Queue\InteractsWithQueue;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Foundation\Bus\Dispatchable;
    use Mlntn\Queue\Traits\UniquelyQueueable;
    
    class MyUniqueJob implements ShouldQueue {
    
        use Dispatchable, InteractsWithQueue, Queueable, UniquelyQueueable, SerializesModels;
    
        /* ... */
    
    }

If the connection is not the default, you will need to specify the connection when dispatching the job:

    dispatch(new UniqueJob)->onConnection('my_unique_name');


### Implement a unique-queueable event
Since an Event simply encapsulates a Job, the event class should also use the UniquelyQueueable trait:
```
 <?php

    namespace App\Events;

    use Illuminate\Queue\SerializesModels;
    use Mlntn\Queue\Traits\UniquelyQueueable;

    class MyEvent {

        public function __construct($entityId)
        {
            $this->entityId;
        }

        public function getUniqueIdentifier()
        {
            return $this->entityId;
        }
    }

```

Dispatch event:
```
    event(new MyEvent(123));

```

To specify the queue, the listener has to provide the connection name

```
 <?php

    namespace App\Listeners;

    class MyListener {

        public $connection = env('UNIQUE_QUEUE_NAME');

        // use delay if you need
        public $delay = 10;

        public function handle(MyEvent $event) {
            // what ever you need
        }

    }

```



## Using Lumen
Lumen handles binding slightly different. Use LumenQueueServiceProvider to enable unique queueing in Lumen.

Register service provider in *app.php*:
```
$app->register(Mlntn\Providers\LumenQueueServiceProvider::class);
```


## Using Horizon

Set up a worker configuration:
```
    'worker_name' => [
        'connection' => 'my_unique_name',
        'queue'      => ['default'],
        'balance'    => 'auto',
        'processes'  => 16,
        'tries'      => 3,
    ],
```

## Run Queue Worker
The unique queue behavior acts internally. Run queue worker as known using artisan (via cli, supervisor or another method).
For more detailed information head over to https://github.com/illuminate/queue
Specify the connection name used in *config/queue.php*

```
php artisan queue:work my_unique_name
```
