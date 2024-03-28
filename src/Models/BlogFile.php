<?php

namespace Lnch\LaravelBlog\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class BlogFile extends BlogModel
{
    use SoftDeletes;

    protected $fillable = [
        'site_id',
        'storage_location',
        'path'
    ];

    public $table = "blog_files";

    /**
     * Returns the full path to the location the file is stored.
     *
     * @return string
     */
    public function getPath()
    {
        if ($this->storage_location == "storage")
        {
            return public_path("storage/".config("laravel-blog.files.storage_path") . "/" . $this->path);
        }

        return public_path(config("laravel-blog.files.storage_path") . '/' . $this->path);
    }

    /**
     * Returns the URL of the file.
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getUrl()
    {
        if ($this->storage_location == "storage")
        {
            return url("storage/".config("laravel-blog.files.storage_path") . "/" . $this->path);
        }

        return url(config("laravel-blog.files.storage_path") . "/" . $this->path);
    }
}
