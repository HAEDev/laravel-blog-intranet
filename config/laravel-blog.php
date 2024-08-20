<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Post Model
    |--------------------------------------------------------------------------
    |
    | The Eloquent model to use as a post
    |
    */

    'post_model' => Lnch\LaravelBlog\Models\BlogPost::class,
    'image_model' => \Lnch\LaravelBlog\Models\BlogImage::class,
    'category_model' => \Lnch\LaravelBlog\Models\BlogCategory::class,
    'file_model' => \Lnch\LaravelBlog\Models\BlogFile::class,
    'tag_model' => \Lnch\LaravelBlog\Models\BlogTag::class,
    'comment_model' => \Lnch\LaravelBlog\Models\Comment::class,

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | By default, routes all routes start with 'blog'. If you want to customise
    | this default routing, change the setting below to match the route prefix
    | you want to use
    |
    */

    'route_prefix' => 'admin/blog',

    'frontend_route_prefix' => 'blog',

    /*
    |--------------------------------------------------------------------------
    | Auth
    |--------------------------------------------------------------------------
    |
    | By default, backend routes will be hidden behind the auth middleware. Use
    | this setting to change that
    |
    */

    'use_auth_middleware' => true,

    /*
    |--------------------------------------------------------------------------
    | Session messages
    |--------------------------------------------------------------------------
    |
    | Choose whether to use the included session toast messages.
    |
    */

    'use_toast_messages' => true,

    /*
    |--------------------------------------------------------------------------
    | Allow Previewing Posts
    |--------------------------------------------------------------------------
    |
    | Decides whether or not to show the preview post button on the admin side
    |
    */

    'allow_post_previewing' => true,

    /*
    |--------------------------------------------------------------------------
    | View Path
    |--------------------------------------------------------------------------
    |
    | The path the views can be found at. You should define this as you would
    | when loading a view in a controller. i.e. "admin.blog"
    |
    */

    'views_path' => 'laravel-blog::',

    /*
    |--------------------------------------------------------------------------
    | Taxonomy
    |--------------------------------------------------------------------------
    |
    | The system is built as a blog. If you'd rather use 'News' Posts, or 'Event'
    | posts for example, this setting will change the default value.
    |
    */

    'taxonomy' => 'Blog',

    /*
    |--------------------------------------------------------------------------
    | Site Model
    |--------------------------------------------------------------------------
    |
    | If you wish to use the blog across multiple 'sites', you will need to
    | define a site model that implements the SiteInterface class provided in
    | the package. You must provide the qualified class name.
    |
    */

    'site_model' => Lnch\LaravelBlog\Models\Site::class,

    'site_primary_key' => 'id',

    /*
    |--------------------------------------------------------------------------
    | Layout Options
    |--------------------------------------------------------------------------
    |
    | By default Laravel Blog uses a very simple built in layout, that provides
    | simple links to the related blog pages. If you wish to use your own layout,
    | set the layout view below. view_layout will be used in the @extends()
    | directive.
    |
    | By default, the view content will be injected into a section named 'content'.
    | To change this, set the @section() you'd like to use instead in the view_content
    | option below.
    |
    | If you wish to use the default options, either leave these values untouched,
    | or comment them out entirely
    |
    */

    'view_layout' => "laravel-blog::layout",
    'view_content' => 'content',

    /*
    |--------------------------------------------------------------------------
    | Frontend Options
    |--------------------------------------------------------------------------
    |
    | All config options related to the way posts, categories, tags and images
    | are displayed on the frontend.
    |
    */

    'frontend' => [

        /*
         * How many posts should be displayed per page on the frontend
         */
        'posts_per_page'    => 10,

    ],

    /*
    |--------------------------------------------------------------------------
    | Post Admin Options
    |--------------------------------------------------------------------------
    |
    | All config options related to the blog posts.
    |
    */

    'posts' => [

        /*
           * The taxonomy will be used in the routes file to define the route
           */
        'taxonomy'          => 'posts',

        /*
         * How many records should be shown on the index page
         */
        'per_page'          => 10,

        /*
         * By default all posts, scheduled or current will be shown together.
         */
        'separate_scheduled' => false
    ],

    /*
    |--------------------------------------------------------------------------
    | Category Admin Options
    |--------------------------------------------------------------------------
    |
    | All config options related to the blog categories.
    |
    */

    'categories' => [

        /*
         * Defines whether or not the feature is enabled on the site or not
         */
        'enabled'           => true,

        /*
         * The taxonomy will be used in the routes file to define the route
         */
        'taxonomy'          => 'categories',

        /*
         * How many records should be shown on the index page
         */
        'per_page'          => 10,

    ],

    /*
    |--------------------------------------------------------------------------
    | Tag Admin Options
    |--------------------------------------------------------------------------
    |
    | All config options related to the blog tags.
    |
    */

    'tags' => [

        /*
         * Defines whether or not the feature is enabled on the site or not
         */
        'enabled'           => true,

        /*
         * The taxonomy will be used in the routes file to define the route
         */
        'taxonomy'          => 'tags',

        /*
         * How many records should be shown on the index page
         */
        'per_page'          => 15,

    ],

    /*
    |--------------------------------------------------------------------------
    | Image Admin Options
    |--------------------------------------------------------------------------
    |
    | All config options related to the blog images.
    |
    */

    'images' => [

        /*
         * Defines whether or not the feature is enabled on the site or not
         */
        'enabled'           => true,

        /*
         * The taxonomy will be used in the routes file to define the route
         */
        'taxonomy'          => 'images',

        /*
         * How many records should be shown on the index page
         */
        'per_page'          => 15,

        /*
         * Storage location. Options are 'public' or 'storage'
         */
        'storage_location'  => 'storage',

        /*
         * Where Blog Images will be stored. Relative to the public directory if the storage_location
         * is set to 'public' or the storage/app/public directory if set to 'storage'
         */
        'storage_path'      => "images/laravel-blog",

        /*
         * The uploaded file will be stored according to this template, followed
         * by it's original extension.
         *
         * Available tags: [date] [datetime] [filename]
         *                  Ymd    Ymd-His
         */
        'filename_format'   => '[datetime]_[filename]',

        /*
         * Maximum size for any individual uploaded image (defined in Kb)
         */
        'max_upload_size'   => 10000

    ],

    /*
    |--------------------------------------------------------------------------
    | File Admin Options
    |--------------------------------------------------------------------------
    |
    | All config options related to the blog files.
    |
    */

    'files' => [

        /*
         * Defines whether or not the feature is enabled on the site or not
         */
        'enabled'           => true,

        /*
         * The taxonomy will be used in the routes file to define the route
         */
        'taxonomy'          => 'files',

        /*
         * How many records should be shown on the index page
         */
        'per_page'          => 15,

        /*
         * Storage location. Options are 'public' or 'storage'
         */
        'storage_location'  => 'storage',

        /*
         * Where Blog Images will be stored. Relative to the public directory if the storage_location
         * is set to 'public' or the storage/app/public directory if set to 'storage'
         */
        'storage_path'      => "files/laravel-blog",

        /*
         * The uploaded file will be stored according to this template, followed
         * by it's original extension.
         *
         * Available tags: [date] [datetime] [filename]
         *                  Ymd    Ymd-His
         */
        'filename_format'   => '[datetime]_[filename]',

        /*
         * Maximum size for any individual uploaded file (defined in Kb)
         */
        'max_upload_size'   => 10000

    ],

    /*
    |--------------------------------------------------------------------------
    | Comment Admin Options
    |--------------------------------------------------------------------------
    |
    | All config options related to the blog comments.
    |
    */

    'comments' => [

        /*
         * Defines whether or not the feature is enabled on the site or not.
         */
        'enabled'           => true,

        /*
         * The taxonomy will be used in the routes file to define the route.
         */
        'taxonomy'          => 'comments',

        /*
         * How many records should be shown on the index page.
         */
        'per_page'          => 15,

        /*
         * Defines if comments require approval before being displayed on the frontend version of a blog post.
         */
        'requires_approval' => true,

        /*
         * Defines whether non-authenticated users (guests) are allowed to comment on blog posts. They will be
         * asked for a name and email to post a comment.
         */
        'allow_guests' => true,

        /*
         * Defines whether comments can have images added to them.
         */
        'allow_images' => false,

    ],

];
