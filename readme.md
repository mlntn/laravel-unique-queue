This redis queue driver works just like the standard Laravel redis queue driver, however, it prevents the same job from being queued multiple times.

## INSTALLATION

Create a new connection in *config/queue.php*

    'my_unique_name' => [
        'driver' => 'unique',
        'connection' => 'default',
        'queue' => 'default',
        'retry_after' => 90,
    ],

Your jobs should use the UniquelyQueueable trait:

    <?php
    
    namespace App\Jobs;
    
    use Illuminate\Bus\Queueable;
    use Illuminate\Queue\SerializesModels;
    use Illuminate\Queue\InteractsWithQueue;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Foundation\Bus\Dispatchable;
    use Mlntn\Queue\Traits\UniquelyQueueable;
    
    class UniqueJob implements ShouldQueue {
    
        use Dispatchable, InteractsWithQueue, Queueable, UniquelyQueueable, SerializesModels;
    
        /* ... */
    
    }

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
