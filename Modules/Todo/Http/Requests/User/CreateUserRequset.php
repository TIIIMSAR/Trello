<?php

namespace Modules\Todo\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequset extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:256'],
            'email' => ['required', 'email'],
            'image_path' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif'],
            'password' => ['required', 'min:3'],
        ];
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
}
