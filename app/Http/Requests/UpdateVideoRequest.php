<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateVideoRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'access_type_id' => ['required', 'exists:access_types,id'],
            'genre_id' => ['required', 'exists:genres,id'],
            'tags' => ['nullable', 'string'],
            'post_video' => ['nullable', 'file', 'mimes:mp4', 'max:204800'], // 200MB max, optional for updates
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'Video Title',
            'description' => 'Video Description',
            'access_type_id' => 'Access Type',
            'genre_id' => 'Genre',
            'tags' => 'Tags',
            'post_video' => 'Video File',
        ];
    }
}
