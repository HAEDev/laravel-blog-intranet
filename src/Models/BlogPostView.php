<?php

namespace Lnch\LaravelBlog\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPostView extends Model
{
    protected $fillable = [
        'blog_post_id',
        'user_id',
    ];
}
