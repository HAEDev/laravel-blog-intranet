<?php

namespace Lnch\LaravelBlog\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogTagRequest extends FormRequest
{
    public function messages()
    {
        return ['tags.*.unique' => 'Tags must be unique',
            'tags.*.max' => 'Tags may not be greater than 190 characters'];
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
        $this->merge(['tags' => explode(',', $this->tags)]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];

        $siteId = getBlogSiteID();

        switch ($this->method())
        {
            case 'POST':
                $rules['tags'] = 'required|array';
                $rules['tags.*'] = 'string|max:190|unique:blog_tags,name';
                break;
            case 'PATCH':
                $id = $this->tag_id;
                $rules['tag'] = "required|string|unique:blog_tags,name,$id,id,site_id,$siteId|max:190";
                break;
        }

        return $rules;
    }
}
