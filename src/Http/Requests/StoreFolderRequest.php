<?php

namespace Whilesmart\Library\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Whilesmart\OwnerAccess\Concerns\AuthorizesOwnerRequest;

class StoreFolderRequest extends FormRequest
{
    use AuthorizesOwnerRequest;

    public function authorize(): bool
    {
        return $this->authorizeOwnerInRequest();
    }

    public function rules(): array
    {
        return [
            'owner_type' => ['required', 'string'],
            'owner_id' => ['required'],
            'library_collection_id' => ['required', 'integer'],
            'parent_folder_id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:200'],
            'metadata' => ['nullable', 'array'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
