<?php

namespace Lnch\LaravelBlog\Traits;

use Lnch\LaravelBlog\Models\BlogPost;

trait CanViewPosts
{
    public function hasRead($post)
    {
        if (!$post instanceof BlogPost) {
            $post = BlogPost::findOrFail($post);
        }

        return $post->views()->where(['user_id' => $this->id])->exists();
    }

    public function commentsSinceUserLastRead($post)
    {
        if (!$post instanceof BlogPost) {
            $post = BlogPost::findOrFail($post);
        }

        if ($post->views()->where(['user_id' => $this->id])->exists()) {
            $lastRead = $post->views()->select('updated_at')->where(['user_id' => $this->id])->first();
        } else {
            $lastRead = null;
        }

        if ($lastRead == null) {
            return $post->allComments()->count();
        } else {
            return $post->allComments()->where('created_at', '>', $lastRead->updated_at)->count();
        }
    }
}
