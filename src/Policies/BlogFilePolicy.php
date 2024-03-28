<?php

namespace Lnch\LaravelBlog\Policies;

use Lnch\LaravelBlog\Models\BlogFile;

class BlogFilePolicy extends BasePolicy
{
    public function view($user)
    {
        return true;
    }

    public function create($user)
    {
        return true;
    }

    public function edit($user, BlogFile $file)
    {
        return true;
    }

    public function delete($user, BlogFile $file)
    {
        return true;
    }
}
