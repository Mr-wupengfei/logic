<?php

namespace Ykk\Logic\Providers;

use Illuminate\Support\ServiceProvider;

class LogicServiceProvider extends ServiceProvider
{
    /**
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../Resources/Config/logic.php' => config_path('ykk_logic.php')
        ]);

        $this->mergeConfigFrom(__DIR__ . '/../../Resources/Config/logic.php', 'ykk_logic');
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

    }
}
