<?php

namespace App\Http\Controllers;

use App\Http\Requests\Groups\StoreGroupRequest;
use App\Http\Requests\Groups\UpdateGroupRequest;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Client;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GroupController extends Controller
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

        $groups = QueryBuilder::for(Group::class)
            ->defaultSort('name')
            ->allowedSorts(['name', 'created_at'])
            ->allowedFilters(['name', $globalSearch])
            ->paginate()
            ->withQueryString();

        return Inertia::render('Groups/Index', [
            'groups' => $groups,
        ])->table(function (InertiaTable $table) {
            $table
                ->withGlobalSearch()
                ->defaultSort('name')
                ->column(key: 'name', searchable: true, sortable: true, canBeHidden: false)
                ->column(key: 'created_at', searchable: false, sortable: true, canBeHidden: false)
                ->column(label: 'Actions');
        });
    }

    public function edit(Group $group)
    {
        return Inertia::render('Groups/Edit', [
            'group' => $group,
        ]);
    }

    public function update(Group $group, UpdateGroupRequest $request)
    {
        $data = $request->validated();

        $group->update($data);

        return Redirect::route('groups.edit', $group->id);
    }

    public function create()
    {
        return Inertia::render('Groups/Create');
    }

    public function store(StoreGroupRequest $request)
    {
        $data = $request->validated();

        Group::create($data);

        return Redirect::route('groups.index');
    }

    public function destroy(Group $group)
    {
        $group->delete();

        return Redirect::route('groups.index');
    }
}
