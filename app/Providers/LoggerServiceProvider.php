<?php

/**
 * This file is part of RiverCrane's project.
 * (c) 2018 MotoSearch Team <moto@webike.net>
 *
 * @author    Mai Nhut Tan <mai_tan@webike.jp>
 * @copyright 2018 RiverCrane
 * @package   webike/shopmanager
 * @see       https://redmine.ig.webike.net/gitbucket/moto/ShopManager-Renew
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class LoggerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot()
    {
    }

    /**
     * Register services.
     */
    public function register()
    {
        $this->app->singleton('db_log', function () {
            return \Log::channel('database');
        });
    }
}
