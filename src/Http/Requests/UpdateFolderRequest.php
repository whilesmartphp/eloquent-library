<?php

namespace Whilesmart\Library\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Whilesmart\OwnerAccess\Concerns\AuthorizesOwnerRequest;

class UpdateFolderRequest extends FormRequest
{
    use AuthorizesOwnerRequest;

    public function authorize(): bool
    {
        return $this->authorizeOwnerOfBoundModel('folder');
    }

    public function rules(): array
    {
        return [
            'library_collection_id' => ['sometimes', 'integer'],
            'parent_folder_id' => ['nullable', 'integer'],
            'name' => ['sometimes', 'string', 'max:200'],
            'metadata' => ['nullable', 'array'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
