# Laravel Unique Queue

This redis queue driver works just like the standard Laravel redis queue driver, however, it prevents the same job from being queued multiple times.

## Requirements

Needs PHP >= 7.1 to be installed.

Requires `illuminate/redis` and `illuminate/queue`, both `"~5.7"`, `"~6"` or `"~7"`

## Installation

### Require via Composer
```
composer require mlntn/laravel-unique-queue
```


### Configure
Create a new connection in `config/queue.php`

```
return [
    // ...
    'connections' => [
        'my_unique_queue_connection_name' => [
            'driver'      => 'unique',
            'connection'  => 'default',
            'queue'       => env('UNIQUE_QUEUE_NAME', 'my_unique_queue_name'),
            'retry_after' => 90,
        ],
        //...
    ]
];
```

## Implementation

### Implement a uniquely-queueable job

Your job should use the UniquelyQueueable trait and have the getUniqueIdentifier method:

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

        public function getUniqueIdentifier()
        {
            return 'some-unique-identifier';
        }

    }

If the connection is not the default, you will need to specify the connection when dispatching the job:

    dispatch(new UniqueJob)->onConnection('my_unique_queue_connection_name');


### Implement a unique-queueable listener

Just like with a job the listener class should use the UniquelyQueueable trait and make sure you've set the connection and queue:

    <?php

    namespace App\Listeners;

    use Illuminate\Contracts\Queue\ShouldQueue;
    use Mlntn\Queue\Traits\UniquelyQueueable;

    class MyListener implements ShouldQueue {

        public $connection = 'my_unique_queue_connection_name';

        public $queue = 'my_unique_queue_name';

        public function handle($event)
        {
            //
        }

        public function getUniqueIdentifier()
        {
            return 'some-unique-identifier';
        }
    }

## Using Lumen
Lumen handles binding slightly different. Use LumenQueueServiceProvider to enable unique queueing in Lumen.

Register service provider in `app.php`:
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
Specify the connection name used in `config/queue.php`

```
php artisan queue:work my_unique_name
```
