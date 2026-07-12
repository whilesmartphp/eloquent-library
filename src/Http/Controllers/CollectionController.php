<?php

namespace Whilesmart\Library\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Whilesmart\Library\Http\Requests\StoreCollectionRequest;
use Whilesmart\Library\Http\Requests\UpdateCollectionRequest;
use Whilesmart\Library\Http\Resources\CollectionResource;
use Whilesmart\Library\Models\Collection;
use Whilesmart\OwnerAccess\Concerns\AuthorizesOwnerController;

class CollectionController extends Controller
{
    use AuthorizesOwnerController;

    public function index(Request $request): JsonResponse
    {
        $query = $this->scopeAccessibleOwners(Collection::query(), $request->user());

        if ($request->filled('owner_type') && $request->filled('owner_id')) {
            $query->where('owner_type', $request->input('owner_type'))
                ->where('owner_id', $request->input('owner_id'));
        }

        if ($request->filled('q')) {
            $term = '%'.strtolower($request->input('q')).'%';
            $query->whereRaw('lower(name) like ?', [$term]);
        }

        $collections = $query->orderByDesc('updated_at')
            ->paginate((int) $request->input('per_page', 25));

        return response()->json([
            'success' => true,
            'data' => CollectionResource::collection($collections)->response()->getData(true),
        ]);
    }

    public function store(StoreCollectionRequest $request): JsonResponse
    {
        $collection = Collection::create($request->validated());

        return response()->json([
            'success' => true,
            'data' => new CollectionResource($collection),
        ], 201);
    }

    public function show(Collection $collection, Request $request): JsonResponse
    {
        $this->authorizeAccessTo($collection, $request->user());

        return response()->json([
            'success' => true,
            'data' => new CollectionResource($collection),
        ]);
    }

    public function update(UpdateCollectionRequest $request, Collection $collection): JsonResponse
    {
        $this->authorizeAccessTo($collection, $request->user());
        $collection->update($request->validated());

        return response()->json([
            'success' => true,
            'data' => new CollectionResource($collection->fresh()),
        ]);
    }

    public function destroy(Collection $collection, Request $request): JsonResponse
    {
        $this->authorizeAccessTo($collection, $request->user());
        $collection->delete();

        return response()->json([
            'success' => true,
            'message' => 'Collection deleted.',
        ]);
    }
}
