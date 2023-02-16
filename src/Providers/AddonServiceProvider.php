<?php

namespace Addons\ExtendedTaxonomyFieldtype\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class AddonServiceProvider extends ServiceProvider
{
    /**
     * Register any addon services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
    }

    /**
     * Bootstrap any addon services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $addonName = 'ExtendedTaxonomyFieldtype';
        $basePath = dirname(dirname(dirname(__FILE__)));

        if (!File::isDirectory(public_path("addons"))) {
            File::makeDirectory(public_path("addons"));
        }

        if (!File::exists(public_path("addons/{$addonName}"))) {
            // Create symlink
            File::link(
                "{$basePath}/public",
                public_path("addons/{$addonName}")
            );
        }

        \Fusion::asset('/addons/ExtendedTaxonomyFieldtype/js/app.js');
        fieldtypes()->register(\Addons\ExtendedTaxonomyFieldtype\Fieldtypes\ExtendedTaxonomyFieldtype::class);
    }
}