<?php

namespace Lnch\LaravelBlog\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogFileRequest extends FormRequest
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
        $rules = [];

        switch ($this->method())
        {
            case 'POST':
                $rules['files'] = 'required|array';
                $rules['files.*'] = 'file';

                $maxSize = config("laravel-blog.files.max_upload_size", 0);
                if (is_numeric($maxSize) && $maxSize > 0)
                {
                    $rules['files.*'] .= "|max:$maxSize";
                }

                break;
        }

        return $rules;
    }
}
