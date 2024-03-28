<?php

namespace Lnch\LaravelBlog\Policies;

use Lnch\LaravelBlog\Contracts\CommentPolicyInterface;
use Lnch\LaravelBlog\Models\Comment;

class CommentPolicy extends BasePolicy implements CommentPolicyInterface
{
    public function update($user, Comment $comment)
    {
        return true;
    }

    public function delete($user, Comment $comment)
    {
        return true;
    }
}