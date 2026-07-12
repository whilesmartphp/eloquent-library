<?php

namespace Whilesmart\Library\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FolderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'owner_type' => $this->owner_type,
            'owner_id' => $this->owner_id,
            'library_collection_id' => $this->library_collection_id,
            'parent_folder_id' => $this->parent_folder_id,
            'name' => $this->name,
            'metadata' => $this->metadata,
            'sort_order' => (int) $this->sort_order,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'deleted_at' => $this->deleted_at?->toIso8601String(),
        ];
    }
}
