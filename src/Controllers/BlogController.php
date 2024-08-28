<?php

namespace Lnch\LaravelBlog\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Lnch\LaravelBlog\Models\BlogHelper;
use Lnch\LaravelBlog\Models\BlogPost;
use Lnch\LaravelBlog\Models\Comment;

class BlogController extends Controller
{
    /**
     * Display all blog posts.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = BlogHelper::posts();

        return view($this->viewPath."frontend.index", [
            'posts' => $posts
        ]);
    }

    /**
     * Show an individual blog post
     *
     * @param BlogPost $post
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function show($post, $slug)
    {
        $post = $this->postModel->findOrFail($post);

        // Add check for draft posts
        $user = auth()->user();
        if($post->status == BlogPost::STATUS_DRAFT && config("laravel-blog.allow_post_previewing", true)) {
            if(!$user || $user->cannot("view_draft_post", $post)) {
                return redirect(config("laravel-blog.frontend_route_prefix"));
            }
        }

        // Check if comments are enabled, and if a reply ID has been specified
        if(config("laravel-blog.comments.enabled") && request()->get("reply")) {
            $replyTo = $post->comments()->find(request()->get("reply"));
        } else {
            $replyTo = null;
        }

        // Check for correct slug, 301 if not
        if($post->slug !== $slug) {
            return redirect(config("laravel-blog.frontend_route_prefix")."/"
                ."$post->id/$post->slug", 301);
        }

        // Record the view
        $post->recordView();

        return view($this->viewPath."frontend.show", [
            'post' => $post,
            'replyTo' => $replyTo,
        ]);
    }

    public function postComment($post, Request $request)
    {
        $post = $this->postModel->findOrFail($post);

        $rules = [
            'name' => 'sometimes|string|max:200',
            'email' => 'sometimes|email|max:150',
            'comment' => 'required|string|max:65000',
            'parent_id' => 'sometimes',
        ];

        if(config('laravel-blog.comments.allow_images') === true) {
            $rules['image'] = 'nullable|image';
        }

        $this->validate($request, $rules);

        $imagePath = null;

        if($request->image) {
            $imageFileName = $this->uploadFile($request->image);

            $imagePath = config('laravel-blog.images.storage_path') . "/" . $imageFileName;
        }

        $post->comments()->create([
            'name' => $request->name,
            'email' => $request->email,
            'parent_id' => $request->parent_id,
            'user_id' => auth()->id(),
            'body' => $request->comment,
            'image_path' => $imagePath,
            'status' => config("laravel-blog.comments.requires_approval")
                ? Comment::STATUS_PENDING_APPROVAL : Comment::STATUS_APPROVED,
        ]);

        return redirect(blogUrl("$post->id/$post->slug" . "#post-comments", true));
    }

    public function deleteComment($comment)
    {
        $comment = $this->commentModel->findOrFail($comment);

        $this->authorize('delete', $comment);

        if ($comment->replies) {
            foreach ($comment->replies as $reply) {
                $reply->delete();
            }
        }

        if ($comment->image_path) {
            Storage::disk('public')->delete($comment->image_path);
        }

        $comment->delete();

        return redirect()->back();
    }

    public function removeCommentImage($comment)
    {
        $comment = $this->commentModel->findOrFail($comment);

        $this->authorize('update', $comment);

        Storage::disk('public')->delete($comment->image_path);

        $comment->update(['image_path' => null]);

        return redirect()->back();
    }

    private function uploadFile(UploadedFile $file)
    {
        // Create filename
        $originalFilename = $file->getClientOriginalName();

        $patterns = [
            '@\[date\]@is',
            '@\[datetime\]@is',
            '@\[filename\]@is',
        ];

        $matches = [
            date("Ymd"),
            date("Ymd-His"),
            str_replace(" ", "_", $originalFilename),
        ];

        $filenamePattern = config("laravel-blog.images.filename_format", "[datetime]_[filename]");
        $filename = preg_replace($patterns, $matches, $filenamePattern);

        $storageLocation = config("laravel-blog.images.storage_location");

        // Upload file
        if ($storageLocation == "public")
        {
            $destinationPath = public_path(config("laravel-blog.images.storage_path"));
        }
        else if($storageLocation == "storage")
        {
            $destinationPath = storage_path("app/public/".config("laravel-blog.images.storage_path"));
        }
        else
        {
            throw new \Exception("images.storage_path has not been properly defined");
        }

        $file->move($destinationPath, $filename);

        return $filename;
    }
}
