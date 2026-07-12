<?php

namespace Whilesmart\Library\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Whilesmart\Library\Http\Requests\StoreFolderRequest;
use Whilesmart\Library\Http\Requests\UpdateFolderRequest;
use Whilesmart\Library\Http\Resources\FolderResource;
use Whilesmart\Library\Models\Folder;
use Whilesmart\OwnerAccess\Concerns\AuthorizesOwnerController;

class FolderController extends Controller
{
    use AuthorizesOwnerController;

    public function index(Request $request): JsonResponse
    {
        $query = $this->scopeAccessibleOwners(Folder::query(), $request->user());

        if ($request->filled('owner_type') && $request->filled('owner_id')) {
            $query->where('owner_type', $request->input('owner_type'))
                ->where('owner_id', $request->input('owner_id'));
        }

        if ($request->filled('library_collection_id')) {
            $query->where('library_collection_id', $request->input('library_collection_id'));
        }

        if ($request->filled('parent_folder_id')) {
            $query->where('parent_folder_id', $request->input('parent_folder_id'));
        }

        $folders = $query->orderBy('sort_order')
            ->paginate((int) $request->input('per_page', 25));

        return response()->json([
            'success' => true,
            'data' => FolderResource::collection($folders)->response()->getData(true),
        ]);
    }

    public function store(StoreFolderRequest $request): JsonResponse
    {
        $folder = Folder::create($request->validated());

        return response()->json([
            'success' => true,
            'data' => new FolderResource($folder),
        ], 201);
    }

    public function show(Folder $folder, Request $request): JsonResponse
    {
        $this->authorizeAccessTo($folder, $request->user());

        return response()->json([
            'success' => true,
            'data' => new FolderResource($folder),
        ]);
    }

    public function update(UpdateFolderRequest $request, Folder $folder): JsonResponse
    {
        $this->authorizeAccessTo($folder, $request->user());
        $folder->update($request->validated());

        return response()->json([
            'success' => true,
            'data' => new FolderResource($folder->fresh()),
        ]);
    }

    public function destroy(Folder $folder, Request $request): JsonResponse
    {
        $this->authorizeAccessTo($folder, $request->user());
        $folder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Folder deleted.',
        ]);
    }
}
