<?php

namespace Lnch\LaravelBlog;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Lnch\LaravelBlog\Models\BlogCategory;
use Lnch\LaravelBlog\Models\BlogFile;
use Lnch\LaravelBlog\Models\BlogImage;
use Lnch\LaravelBlog\Models\BlogPost;
use Lnch\LaravelBlog\Models\BlogTag;
use Lnch\LaravelBlog\Policies\BlogCategoryPolicy;
use Lnch\LaravelBlog\Policies\BlogFilePolicy;
use Lnch\LaravelBlog\Policies\BlogImagePolicy;
use Lnch\LaravelBlog\Policies\BlogPostPolicy;
use Lnch\LaravelBlog\Policies\BlogTagPolicy;

class LaravelBlogServiceProvider extends ServiceProvider
{
    protected $policies = [
        BlogTag::class              => BlogTagPolicy::class,
        BlogCategory::class         => BlogCategoryPolicy::class,
        BlogImage::class            => BlogImagePolicy::class,
        BlogPost::class             => BlogPostPolicy::class,
        BlogFile::class             => BlogFilePolicy::class
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Load package migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Publish config files
        $this->publishes([
            __DIR__.'/../config/laravel-blog.php' => config_path('laravel-blog.php'),
        ], 'laravel-blog/config');

        // Publish migration files
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'laravel-blog/migrations');

        // Load package views
        $this->loadViewsFrom(__DIR__.'/Views', 'laravel-blog');

        // Publish view files
        $this->publishes([
            __DIR__.'/Views' => resource_path('views/vendor/laravel-blog'),
        ], 'laravel-blog/views');

        // Publish the public CSS and JS
        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/lnch/laravel-blog'),
        ], 'laravel-blog/public');

        // Register policies
        $this->registerPolicies();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Set up the config if not published
        if ($this->app['config']->get('laravel-blog') === null) {
            $this->app['config']->set('laravel-blog', require __DIR__.'/../config/laravel-blog.php');
        }

        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../config/laravel-blog.php',
            'permission'
        );

        $this->app->bind('laravel-blog', function() {
            return new Models\BlogHelper;
        });

        // Allow routing to work
        include __DIR__.'/Routes/web.php';
    }

    public function registerPolicies()
    {
        foreach ($this->policies as $key => $value) {
            $policy = Gate::getPolicyFor($key);
            if (!$policy) {
                Gate::policy($key, $value);
            }
        }
    }
}
