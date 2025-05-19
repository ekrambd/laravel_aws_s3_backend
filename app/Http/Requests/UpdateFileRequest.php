<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFileRequest extends FormRequest
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
            //'bucket_id' => 'required|integer|exists:buckets,id',
            'status' => 'required|in:Public,Private',
            'file_importance' => 'required|in:Low,Medium,High',
            //'storage_class' => 'required|in:STANDARD,INTELLIGENT_TIERING,ONEZONE_IA,GLACIER,DEEP_ARCHIVE',
        ];
    }
}
