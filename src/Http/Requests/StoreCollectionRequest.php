<?php

namespace Whilesmart\Library\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Whilesmart\OwnerAccess\Concerns\AuthorizesOwnerRequest;

class StoreCollectionRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'metadata' => ['nullable', 'array'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
