<?php

namespace Lnch\LaravelBlog\Models;

use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogPost extends BlogModel
{
    use SoftDeletes;

    const STATUS_DRAFT = "D";
    const STATUS_ACTIVE = "A";

    const FORMAT_STANDARD = "S";
    const FORMAT_VIDEO = "V";

    protected $fillable = [
        'site_id',
        'author_id',
        'title',
        'slug',
        'fb_slug',
        'content',
        'blog_image_id',
        'status',
        'format',
        'is_approved',
        'approved_by',
        'comments_enabled',
        'published_at',
        'is_featured',
        'show_featured'
    ];

    public $dates = [
        'published_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Retrieves all categories associated with this post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(config("laravel-blog.category_model"), "blog_post_categories",
            "blog_post_id", "blog_category_id");
    }

    /**
     * Retrieves all tags associated with this post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(config("laravel-blog.tag_model"), "blog_post_tags",
            "blog_post_id", "blog_tag_id");
    }

    /**
     * Retrieves the User model of the author.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class, "author_id");
    }

    /**
     * Retrieves the BlogImage instance for the chosen featured image.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function featuredImage()
    {
        return $this->belongsTo(config('laravel-blog.image_model'), "blog_image_id");
    }

    /**
     * Retrieves all comments associated with this post.
     *
     * @return array|\Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allComments()
    {
        return config("laravel-blog.comments.enabled")
            ? $this->hasMany(config("laravel-blog.comment_model"), "blog_post_id", "id")
                ->orderby("created_at", "desc")
            : [];
    }

    /**
     * Retrieves all top level comments, without replies
     *
     * @return array|\Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return config("laravel-blog.comments.enabled")
            ? $this->allComments()->where("parent_id", null)
            : [];
    }

    /**
     * Retrieves all views.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function views()
    {
        return $this->hasMany(BlogPostView::class, "blog_post_id", "id");
    }

    public function files()
    {
        return $this->belongsToMany(config('laravel-blog.file_model'), "blog_post_files", "post_id", "file_id")->withPivot('display_name');
    }

    /**
     * Returns an array of available statuses a BlogPost can be in.
     *
     * @return array
     */
    public static function statuses()
    {
        return [
            self::STATUS_DRAFT => "Draft",
            self::STATUS_ACTIVE => "Active"
        ];
    }

    /**
     * Handles creating new tags if they don't exist, and also
     * links the tags to the post via it's relation.
     *
     * @param $tags
     */
    public function syncTags($tags)
    {
        // Create tags if needs be
        $ids = config("laravel-blog.tag_model")::createMany($tags);

        // Sync relations
        $this->tags()->sync($ids);
    }

    /**
     * Retrieves a list of categories that are available to the post.
     * Available is defined as 'not deleted, and not already assigned to
     * the post'.
     *
     * @return mixed
     */
    public function availableCategories()
    {
        return config("laravel-blog.category-model")::whereNotIn(
            "blog_categories.id",
            $this->categories()->pluck("blog_categories.id")->toArray()
        )->get();
    }

    /**
     * Check to see if a post has the given BlogCategory assigned to
     * it.
     *
     * @param BlogCategory $category
     * @return bool
     */
    public function hasCategory(BlogCategory $category)
    {
        return in_array($category->id, $this->categories()->pluck("blog_categories.id")->toArray());
    }

    /**
     * Takes the given slug value and processes it to ensure it will
     * be a valid URL
     *
     * @param $value
     * @return bool|string
     */
    public static function processSlug($value)
    {
        $patterns = [
            '/[^a-zA-Z0-9 -]/',
            '/(\s){2,}/',
            '/\s/',
            '/-{2,}/',
        ];

        $replacements = [
            '',
            ' ',
            '-',
            '-',
        ];

        $slug = strtolower(preg_replace($patterns, $replacements, $value));

        if (strlen($slug) > 50) {
            $slug = substr($slug, 0, 50);
        }

        return $slug;
    }

    /**
     * Returns a brief excerpt of the content of the blog post, up to
     * a certain defined length. For use on the index page primarily
     *
     * @param int $length
     * @return bool|string
     */
    public function getBriefContent($length = 150)
    {
        $content = substr(strip_tags($this->content), 0, $length);

        if (strlen(strip_tags($this->content)) > $length) {
            $content .= "...";
        }

        return html_entity_decode(trim($content), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES);
    }

    /**
     * Returns the URL for the blog post.
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getUrlAttribute()
    {
        $url = url(config("laravel-blog.frontend_route_prefix")."/$this->id/" . $this->slug);
        return $url;
    }

    /**
     * Returns the URL for the blog post.
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getCommentsUrlAttribute()
    {
        $url = url(config("laravel-blog.frontend_route_prefix")."/$this->id/");
        return $url;
    }

    /**
     * Returns a formatted version of the published date
     *
     * @return mixed
     */
    public function getPublishedAttribute()
    {
        return $this->published_at->format("jS F, Y");
    }

    public function getPublishedAtTimestampAttribute()
    {
        return $this->published_at->format("Y-m-d") . "T"
            . $this->published_at->format("H:i");
    }


    /**
     * Returns a formatted
     *
     * @return mixed
     */
    public function getAuthorUrlAttribute()
    {
        return url("blog/author/{$this->author->id}-" . strtolower(str_replace(" ", "-", $this->author->name)));
    }

    /**
     * Returns the amount of comments made on this blog post
     *
     * @return int
     */
    public function getCommentsCountAttribute()
    {
        return count($this->allComments);
    }

    public function isWithinDays($days = 7)
    {
        $date = strtotime("-$days days");
        $postDate = strtotime($this->published_at);

        return $postDate > $date;
    }

    public function isDraft()
    {
        return $this->status == self::STATUS_DRAFT;
    }

    public function isActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    /**
     * Records a user reading this post.
     *
     * @return bool
     */
    public function recordView()
    {
        if (auth()->check()) {

            if ($this->views()
                ->where('blog_post_id', '=', $this->id)
                ->where('user_id', '=', auth()->id())
                ->exists()) {

                $this->views()->where('blog_post_id', '=', $this->id)
                    ->where('user_id', '=', auth()->id())->touch();

            } else {

                $this->views()->create([
                    'blog_post_id' => $this->id,
                    'user_id' => auth()->id(),
                ]);
            }
        }

        return true;
    }
}
