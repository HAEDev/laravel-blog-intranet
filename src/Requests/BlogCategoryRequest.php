<?php

namespace Lnch\LaravelBlog\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'description' => 'sometimes|string|nullable'
        ];

        $siteId = getBlogSiteID();

        switch ($this->method())
        {
            case 'POST':
                $rules['name'] = 'required|unique:blog_categories,name,NULL,id,site_id,'.$siteId.',deleted_at,NULL|string|max:190';
                break;
            case 'PATCH':
                $rules['name'] = 'required|unique:blog_categories,name,'.$this->category_id.',id,site_id,'.$siteId.'|string|max:190';
                break;
        }

        return $rules;
    }
}
