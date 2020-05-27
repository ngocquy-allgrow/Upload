<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use DB;
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        
        $this->enableCacheEvents();
    }

    protected function enableCacheEvents()
    {
        // For debug only
        //if (config('database.debug_slow_queries')) {
           
            //$logger = app('db_log');
            $logger=\Log::channel('database');
            DB::listen(function ($query) use ($logger) {
                
                //if ($query->time > 100) {
                    $sql = $query->sql;

                    foreach ($query->bindings as $key => $binding) {
                        $regex = is_numeric($key)
                        ? "/\\?(?=(?:[^'\\\\']*'[^'\\\\']*')*[^'\\\\']*$)/u"
                        : "/:{$key}(?=(?:[^'\\\\']*'[^'\\\\']*')*[^'\\\\']*$)/u";
                        $sql = preg_replace($regex, sql_value($binding), $sql, 1);
                    }
                    //echo sql_format($sql);
                    $logger->warn('Slow query: ' . PHP_EOL . sql_format($sql) . PHP_EOL . 'Time: ' . $query->time . 'ms');
                //}
            });
        //}
    }
}
