# whilesmart/eloquent-library

A polymorphic content library for Laravel: a `Collection` -> `Folder` -> `Asset` hierarchy for
anything an owner keeps and reuses (notes, images, offerings, profiles). Each record is scoped
to an owner (a workspace, organization, user) through
[`whilesmart/eloquent-owner-access`](https://github.com/whilesmartphp/eloquent-owner-access).
Assets are thin, polymorphic envelopes: `kind` is an open string, text kinds use `body`, and
binary kinds carry a URL or reference in `metadata`, so new kinds need no schema change.

## Install

```
composer require whilesmart/eloquent-library
php artisan migrate
```

Routes register automatically under the `api` prefix with `auth:sanctum`. Set
`LIBRARY_REGISTER_ROUTES=false` to mount them yourself.

## Owning model

Add the trait to whatever owns a library:

```php
use Whilesmart\Library\Traits\HasLibrary;

class Workspace extends Model
{
    use HasLibrary;
}

$collection = $workspace->libraryCollections()->create(['name' => 'Brand assets']);

$collection->assets()->create([
    'owner_type' => $workspace->getMorphClass(),
    'owner_id' => $workspace->getKey(),
    'kind' => 'note',
    'title' => 'Tone of voice',
    'body' => 'Friendly and concise.',
]);
```

## Endpoints

All routes are mounted under the configured prefix (`api` by default) and a `library` segment.

| Method | Path | Purpose |
|--------|------|---------|
| GET | `/api/library/collections` | List (filter by owner, `q`) |
| POST | `/api/library/collections` | Create |
| GET/PUT/PATCH/DELETE | `/api/library/collections/{collection}` | Show / update / soft delete |
| GET | `/api/library/folders` | List (filter by owner, `library_collection_id`, `parent_folder_id`) |
| POST | `/api/library/folders` | Create |
| GET/PUT/PATCH/DELETE | `/api/library/folders/{folder}` | Show / update / soft delete |
| GET | `/api/library/assets` | List (filter by owner, `library_collection_id`, `library_folder_id`, `kind`, `q`) |
| POST | `/api/library/assets` | Create |
| GET/PUT/PATCH/DELETE | `/api/library/assets/{asset}` | Show / update / soft delete |

## Kinds and presenters

`kind` is a plain string (`note`, `image`, `offering`, `profile`, ...). A `PresenterRegistry`
resolves the presenter for a kind from `config('library.presenters')` and exposes two shapes:
`present($asset)` (the flat listing entry) and `read($asset)` (the full text payload). Add a
kind by adding a presenter class and registering it; the registry never branches on kind. The
`DefaultAssetPresenter` matches every kind, so keep it last in the list.

```php
$registry = app(Whilesmart\Library\Presenters\PresenterRegistry::class);
$registry->present($asset);
$registry->read($asset);
```

## Configuration

Publish the config to override table names, swap any model for a host subclass, or change the
presenter list:

```
php artisan vendor:publish --tag=library-config
```
