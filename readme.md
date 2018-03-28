This redis queue driver works just like the standard Laravel redis queue driver, however, it prevents from the same job to be queued multiple times.

## INSTALLATION

Create a new connection in *config/queue.php*

    'my_unique_name' => [
        'driver' => 'unique',
        'connection' => 'default',
        'queue' => 'default',
        'retry_after' => 90,
    ],

If the connection is not the default, you will need to specify the connection when dispatching the job:

    dispatch(new UniqueJob)->onConnection('my_unique_name');

### Using Horizon

Set up a worker configuration:

    'worker_name' => [
        'connection' => 'unique',
        'queue' => ['default'],
        'balance' => 'auto',
        'processes' => 16,
        'tries' => 3,
    ],

### Using artisan (via supervisor or another method)
Specify the connection name used in *config/queue.php*

    php artisan queue:work my_unique_name
