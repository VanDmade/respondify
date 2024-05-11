<?php

namespace VanDmade\Respondify;

use Illuminate\Support\ServiceProvider;

class RespondifyServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->publishes([
            __DIR__.'/../config.php' => config_path('respondify.php'),
            __DIR__.'/../languages/en.php' => $this->app->langPath('en/respondify.php'),
        ]);
    }

}