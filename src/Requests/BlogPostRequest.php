<?php

namespace Lnch\LaravelBlog\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Lnch\LaravelBlog\Models\BlogPost;
use App\Repositories\Tenants\TenantsRepository;

class BlogPostRequest extends FormRequest
{
    public function messages()
    {
        return ['tags.*.max' => 'Tags may not be greater than 190 characters'];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if(!is_null($this->tags)) {
            $this->merge(['tags' => explode(',', $this->tags)]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];

        $statuses = implode(",", array_keys(BlogPost::statuses()));
        $tenantsRepository = resolve(TenantsRepository::class);

        $rules = array_merge($rules, [
            'title'             => 'required|string|max:190',
            'blog_image_id'     => 'nullable|integer',
            'post_content'      => 'required|max:4294967295',
            'status'            => 'required|in:'.$statuses,
            'format'            => 'sometimes',
            'published_at'      => 'nullable|date',
            'comments_enabled'  => 'required|boolean',
            'tags'              => 'sometimes|array|nullable',
            'tags.*'            => 'string|max:190',
            'is_featured'       => 'sometimes|boolean',
            'attached_files'    => 'sometimes|array|nullable',
            'show_featured'     => 'sometimes|boolean',
            'tenants'           => 'array',
            'tenants.*'         => "in:" . implode(',', $tenantsRepository->getAll()->pluck("id")->toArray()),
        ]);

        $siteId = getBlogSiteID();
        if($this->method() == "POST")
        {
            if ($this->slug)
            {
                $rules = array_merge($rules, [
                    'slug' => "sometimes|alpha_dash|unique:blog_posts,slug,NULL,id,site_id,$siteId",
                ]);
            }
        }
        else if($this->method() == "PATCH")
        {
            if ($this->slug)
            {
                $id = $this->post_id;
                $rules = array_merge($rules, [
                    'slug' => "sometimes|alpha_dash|unique:blog_posts,slug,$id,id,site_id,$siteId",
                ]);
            }
        }

        return $rules;
    }
}
