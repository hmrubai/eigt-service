<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name_bn' => 'required',
            'class_id' => 'required',
            'subject_id' => 'required',
            'chapter_id' => 'required',
            'content_type' => 'required',
            'raw_file' => 'required',
            'status'  => 'integer'
        ];
    }
}
