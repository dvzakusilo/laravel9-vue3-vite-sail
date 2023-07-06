<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        foreach (config('domains') as $arDomains) {
            if(is_dir($path = base_path().$arDomains['src'] . '/Routes/' . env('APP_API_VERSION')))
            {$this->loadRoutesFrom($path . '/api.php') ;}
            if(is_dir($path = base_path().$arDomains['src'] . '/Migrations'))
            {$this->loadMigrationsFrom($path);}
            if(is_dir($path = base_path().$arDomains['src'] . '/Translations'))
            {$this->loadTranslationsFrom($path, 'Domains');}
            if(is_dir($path = base_path().$arDomains['src'] . '/Views'))
            {$this->loadViewsFrom($path, 'Domains');}
            if(is_dir($path = base_path().$arDomains['src'] . '/Factories'))
            {$this->loadFactoriesFrom($path);}
        }
    }
}
