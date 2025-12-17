<?php

namespace App\Http\Controllers;

use App\Actions\User\StoreUserAction;
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

class UserController extends Controller
{
    public function index()
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                Collection::wrap($value)->each(function ($value) use ($query) {
                    $query
                        ->orWhere('name', 'LIKE', "%{$value}%")
                        ->orWhere('email', 'LIKE', "%{$value}%");
                });
            });
        });

        $clients = QueryBuilder::for(User::class)
            ->defaultSort('email')
            ->allowedSorts(['email', 'name', 'created_at'])
            ->allowedFilters(['name', $globalSearch])
            ->paginate()
            ->withQueryString();

        return Inertia::render('Users/Index', [
            'users' => $clients,
        ])->table(function (InertiaTable $table) {
            $table
                ->withGlobalSearch()
                ->defaultSort('email')
                ->column(key: 'email', searchable: true, sortable: true, canBeHidden: false)
                ->column(key: 'name', searchable: true, sortable: true, canBeHidden: false)
                ->column(key: 'created_at', searchable: false, sortable: true, canBeHidden: false)
                ->column(label: 'Actions');
        });
    }

    public function edit(User $user)
    {
        return Inertia::render('Users/Edit', [
            'user' => new UserResource($user->load('clients', 'groups')),
            'roleEditable' => Auth::user()->id !== $user->id,
            'clients' => Client::all(),
            'groups' => Group::all(),
        ]);
    }

    public function update(User $user, UpdateUserRequest $request)
    {
        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        if (Auth::user()->id === $user->id) {
            unset($data['role']);
        }

        $user->update($data);

        $user->clients()->detach();
        $user->groups()->detach();

        foreach ($data['clients'] as $client) {
            $client = Client::where('name', $client)->first();
            $user->clients()->syncWithoutDetaching($client);
        }

        foreach ($data['groups'] as $group) {
            $group = Group::where('name', $group)->first();
            $user->groups()->syncWithoutDetaching($group);
        }

        return Redirect::route('users.edit', $user->id);
    }

    public function create()
    {
        return Inertia::render('Users/Create');
    }

    public function store(StoreUserRequest $request, StoreUserAction $action)
    {
        $data = $request->validated();

        $user = $action->handle($data);

        return Redirect::route('users.edit', $user->id);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return Redirect::route('users.index');
    }
}
