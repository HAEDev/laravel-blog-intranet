<?php

namespace Lnch\LaravelBlog\Controllers;

use App\Http\Controllers\Controller as BaseController;
use Lnch\LaravelBlog\Models\BlogCategory;
use Lnch\LaravelBlog\Models\BlogImage;
use Lnch\LaravelBlog\Models\BlogPost;
use Lnch\LaravelBlog\Models\BlogTag;
use Lnch\LaravelBlog\Models\Comment;

class Controller extends BaseController
{
    protected $viewPath;
    protected $routePrefix;
    protected $postModel;
    protected $imageModel;
    protected $categoryModel;
    protected $commentModel;
    protected $tagModel;
    protected $fileModel;

    public function __construct()
    {
        $this->viewPath = config("laravel-blog.views_path");
        if ($this->viewPath) {
            $this->viewPath .= ".";
        }

        $this->routePrefix = config("laravel-blog.route_prefix");
        if ($this->routePrefix && substr($this->routePrefix, -1) !== "/") {
            $this->routePrefix .= "/";
        }

        $model = config('laravel-blog.post_model', BlogPost::class);
        $this->postModel = new $model;

        $model = config('laravel-blog.image_model', BlogImage::class);
        $this->imageModel = new $model;

        $model = config('laravel-blog.category_model', BlogCategory::class);
        $this->categoryModel = new $model;

        $model = config('laravel-blog.comment_model', Comment::class);
        $this->commentModel = new $model;

        $model = config('laravel-blog.tag_model', BlogTag::class);
        $this->tagModel = new $model;

        $model = config('laravel-blog.file_model', BlogFile::class);
        $this->fileModel = new $model;
    }
}
