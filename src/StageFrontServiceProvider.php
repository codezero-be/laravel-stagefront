<?php

namespace CodeZero\StageFront;

use CodeZero\StageFront\Composers\ThrottleTimeRemaining;
use CodeZero\StageFront\Middleware\RedirectIfStageFrontIsEnabled;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;

class StageFrontServiceProvider extends ServiceProvider
{
    /**
     * The package name.
     *
     * @var string
     */
    protected $name = 'stagefront';

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutes();
        $this->loadViews();
        $this->loadViewComposers();
        $this->loadTranslations();
        $this->loadMiddleware();
        $this->registerPublishableFiles();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();
    }

    /**
     * Load package routes.
     *
     * @return void
     */
    protected function loadRoutes()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/routes.php');
    }

    /**
     * Load package views.
     *
     * @return void
     */
    protected function loadViews()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', $this->name);
    }

    /**
     * Load the package view composers.
     *
     * @return void
     */
    protected function loadViewComposers()
    {
        view()->composer('stagefront::429', ThrottleTimeRemaining::class);
    }

    /**
     * Load package translations.
     *
     * @return void
     */
    protected function loadTranslations()
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', $this->name);
    }

    /**
     * Load the package middleware.
     *
     * @return void
     */
    protected function loadMiddleware()
    {
        if (config('stagefront.enabled') === true) {
            app(Kernel::class)->pushMiddleware(
                RedirectIfStageFrontIsEnabled::class
            );
        }
    }

    /**
     * Register the publishable files.
     *
     * @return void
     */
    protected function registerPublishableFiles()
    {
        $this->publishes([
            __DIR__."/../config/{$this->name}.php" => config_path("{$this->name}.php"),
        ], 'config');

        $this->publishes([
            __DIR__."/../resources/views" =>  resource_path("views/vendor/{$this->name}"),
        ], 'views');

        $this->publishes([
            __DIR__."/../resources/lang" =>  resource_path("lang/vendor/{$this->name}"),
        ], 'lang');
    }

    /**
     * Merge published configuration file with
     * the original package configuration file.
     *
     * @return void
     */
    protected function mergeConfig()
    {
        $this->mergeConfigFrom(__DIR__."/../config/{$this->name}.php", $this->name);
    }
}
