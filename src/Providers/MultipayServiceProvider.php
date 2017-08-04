<?php

namespace MultiPay\Providers;

use Illuminate\Support\ServiceProvider;

class MultiPayServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $path = realpath(__DIR__.'/../../config/multipay.php');
        $this->publishes([$path => config_path('multipay.php')]);
        $this->mergeConfigFrom($path, 'multipay');
    }
}