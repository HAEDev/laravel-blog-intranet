<?php

namespace Lnch\LaravelBlog\Contracts;

use Lnch\LaravelBlog\Models\Comment;

interface CommentPolicyInterface
{
    public function update($user, Comment $comment);
    public function delete($user, Comment $comment);
}