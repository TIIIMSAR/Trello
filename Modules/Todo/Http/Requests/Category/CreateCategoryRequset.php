<?php

namespace Modules\Todo\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class CreateCategoryRequset extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'slug' => ['required', 'string', 'min:3'],
            'category_id' => ['nullable',]
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
