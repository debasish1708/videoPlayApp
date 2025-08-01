<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class CreateBannerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'post_image' => ['required', 'mimes:jpg,jpeg,png', 'max:2048'], // 2MB max
            'url' => ['required', 'url', 'max:2048'], // Optional link, max 2MB
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'Banner Title',
            'description' => 'Banner Description',
            'post_image' => 'Banner Image',
            'url' => 'Banner URL',
        ];
    }
}
