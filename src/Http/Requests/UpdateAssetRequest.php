<?php

namespace Whilesmart\Library\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Whilesmart\OwnerAccess\Concerns\AuthorizesOwnerRequest;

class UpdateAssetRequest extends FormRequest
{
    use AuthorizesOwnerRequest;

    public function authorize(): bool
    {
        return $this->authorizeOwnerOfBoundModel('asset');
    }

    public function rules(): array
    {
        return [
            'library_collection_id' => ['nullable', 'integer'],
            'library_folder_id' => ['nullable', 'integer'],
            'kind' => ['sometimes', 'string', 'max:60'],
            'title' => ['nullable', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'metadata' => ['nullable', 'array'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
