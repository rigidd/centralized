<?php

namespace App\Http\Controllers;

use App\Http\Requests\Apps\StoreAppRequest;
use App\Http\Requests\Apps\UpdateAppRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AppController extends Controller
{
    public function index()
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                Collection::wrap($value)->each(function ($value) use ($query) {
                    $query
                        ->orWhere('name', 'LIKE', "%{$value}%");
                });
            });
        });

        $clients = QueryBuilder::for(Client::class)
            ->defaultSort('name')
            ->allowedSorts(['name'])
            ->allowedFilters(['name', $globalSearch])
            ->paginate()
            ->withQueryString();

        return Inertia::render('Apps/Index', [
            'apps' => $clients,
        ])->table(function (InertiaTable $table) {
            $table
                ->withGlobalSearch()
                ->defaultSort('name')
                ->column(key: 'name', searchable: true, sortable: true, canBeHidden: false)
                ->column(label: 'Actions');
        });
    }

    public function edit(Client $client)
    {
        return Inertia::render('Apps/Edit', [
            'client' => new ClientResource($client),
        ]);
    }

    public function update(Client $client, UpdateAppRequest $request)
    {
        $urls = collect($request->validated('redirect_urls'))->map(fn ($r) => $r['url'])->toArray();

        $client->update([
            'name' => $request->name,
            'picture' => $request->picture,
            'display' => $request->display,
            'redirect' => implode(',', $urls),
        ]);

        $client->redirectAliases()->delete();

        foreach ($request->validated('redirect_urls') as $redirect) {
            $client->redirectAliases()->create([
                'url' => $redirect['url'],
                'alias' => $redirect['alias'] ?? null,
                'icon' => $redirect['icon'] ?? null,
            ]);
        }

        return Redirect::route('apps.edit', $client->id);
    }

    public function create()
    {
        return Inertia::render('Apps/Create');
    }

    public function store(StoreAppRequest $request)
    {
        $secret = Str::random(64);
        
        $urls = collect($request->validated('redirect_urls'))->map(fn ($r) => $r['url'])->toArray();

        $client = Client::create([
            'name' => $request->validated('name'),
            'picture' => $request->picture,
            'redirect' => implode(',', $urls),
            'personal_access_client' => false,
            'password_client' => false,
            'revoked' => false,
            'secret' => $secret,
            'display' => $request->display,
        ]);

        foreach ($request->validated('redirect_urls') as $redirect) {
            $client->redirectAliases()->create([
                'url' => $redirect['url'],
                'alias' => $redirect['alias'] ?? null,
                'icon' => $redirect['icon'] ?? null,
            ]);
        }

        return Inertia::render('Apps/InitHelp', [
            'client' => new ClientResource($client),
            'secret' => $secret,
        ]);
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return Redirect::route('apps.index');
    }
}
