<?php

namespace Modules\Todo\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequset extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'full_name' => ['nullable', 'string', 'min:3', 'max:256'],
            'email' => ['nullable', 'email'],
            'mobile' => ['nullable', 'digits:11', 'regex:/^[0-9]{11}$/'],
            'role' => ['nullable'],
            'profile_path' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif'],
            'password' => ['nullable'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $errors = $validator->errors()->all();
        $firstError = $errors[0] ?? 'خطایی رخ داد';

        throw new \Illuminate\Validation\ValidationException(
            $validator,
            response()->json([
                'success' => false,
                'message' => 'ورودی‌های شما معتبر نیستند. ' . $firstError,
                'errors' => $errors,
            ], 422)
        );
    }
}
