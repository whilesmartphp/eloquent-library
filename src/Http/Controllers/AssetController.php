<?php

namespace Whilesmart\Library\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Whilesmart\Library\Http\Requests\StoreAssetRequest;
use Whilesmart\Library\Http\Requests\UpdateAssetRequest;
use Whilesmart\Library\Http\Resources\AssetResource;
use Whilesmart\Library\Models\Asset;
use Whilesmart\OwnerAccess\Concerns\AuthorizesOwnerController;

class AssetController extends Controller
{
    use AuthorizesOwnerController;

    public function index(Request $request): JsonResponse
    {
        $query = $this->scopeAccessibleOwners(Asset::query(), $request->user());

        if ($request->filled('owner_type') && $request->filled('owner_id')) {
            $query->where('owner_type', $request->input('owner_type'))
                ->where('owner_id', $request->input('owner_id'));
        }

        if ($request->filled('library_collection_id')) {
            $query->where('library_collection_id', $request->input('library_collection_id'));
        }

        if ($request->filled('library_folder_id')) {
            $query->where('library_folder_id', $request->input('library_folder_id'));
        }

        if ($request->filled('kind')) {
            $query->ofKind($request->input('kind'));
        }

        if ($request->filled('q')) {
            $term = '%'.strtolower($request->input('q')).'%';
            $query->where(function ($q) use ($term) {
                $q->whereRaw('lower(title) like ?', [$term])
                    ->orWhereRaw('lower(description) like ?', [$term]);
            });
        }

        $assets = $query->orderByDesc('updated_at')
            ->paginate((int) $request->input('per_page', 25));

        return response()->json([
            'success' => true,
            'data' => AssetResource::collection($assets)->response()->getData(true),
        ]);
    }

    public function store(StoreAssetRequest $request): JsonResponse
    {
        $asset = Asset::create($request->validated());

        return response()->json([
            'success' => true,
            'data' => new AssetResource($asset),
        ], 201);
    }

    public function show(Asset $asset, Request $request): JsonResponse
    {
        $this->authorizeAccessTo($asset, $request->user());

        return response()->json([
            'success' => true,
            'data' => new AssetResource($asset),
        ]);
    }

    public function update(UpdateAssetRequest $request, Asset $asset): JsonResponse
    {
        $this->authorizeAccessTo($asset, $request->user());
        $asset->update($request->validated());

        return response()->json([
            'success' => true,
            'data' => new AssetResource($asset->fresh()),
        ]);
    }

    public function destroy(Asset $asset, Request $request): JsonResponse
    {
        $this->authorizeAccessTo($asset, $request->user());
        $asset->delete();

        return response()->json([
            'success' => true,
            'message' => 'Asset deleted.',
        ]);
    }
}
