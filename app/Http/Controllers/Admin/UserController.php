<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Controllers\User\Concerns\ResolvesListPerPage;
use App\Http\Requests\Admin\StoreAdminUserRequest;
use App\Http\Requests\Admin\UpdateAdminUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class UserController extends Controller
{
    use ResolvesListPerPage;

    public function index(Request $request): View|Response
    {
        $filters = [
            'q' => $request->string('q')->trim()->toString(),
            'role' => $request->string('role')->toString(),
            'per_page' => $this->resolvePerPage($request),
        ];

        $users = User::query()
            ->search($filters['q'])
            ->when($filters['role'], fn ($query, string $role) => $query->where('role', $role))
            ->latest()
            ->paginate($filters['per_page'])
            ->withQueryString();

        if ($request->header('X-Intake-Search') === '1') {
            return response()->view('admin.pages.partials.users-results', [
                'users' => $users,
                'filters' => $filters,
            ]);
        }

        $editingUser = null;

        if ($request->filled('edit')) {
            $editingUser = User::query()->find($request->integer('edit'));
        }

        return view('admin.pages.users', [
            'title' => __('app.admin.nav_users'),
            'description' => __('app.admin.users_description'),
            'users' => $users,
            'filters' => $filters,
            'editingUser' => $editingUser,
        ]);
    }

    public function store(StoreAdminUserRequest $request): RedirectResponse
    {
        $user = User::query()->create($request->safe()->only(['name', 'email', 'role', 'password']));

        activity_log(
            'admin.user.created',
            __('app.admin.activity.descriptions.admin.user.created', [
                'name' => $user->name,
                'email' => $user->email,
            ]),
            ['user_id' => $user->id],
        );

        return redirect()
            ->route('admin.users', $this->listQuery($request))
            ->with('success', __('app.admin.users.created_success'));
    }

    public function update(UpdateAdminUserRequest $request, User $user): RedirectResponse
    {
        if ($this->wouldRemoveLastAdmin($user, UserRole::from($request->string('role')->toString()))) {
            return redirect()
                ->route('admin.users', array_merge($this->listQuery($request), ['edit' => $user->id]))
                ->with('error', __('app.admin.users.last_admin_protected'));
        }

        $payload = $request->safe()->only(['name', 'email', 'role']);

        if ($request->filled('password')) {
            $payload['password'] = $request->validated('password');
        }

        $user->update($payload);

        activity_log(
            'admin.user.updated',
            __('app.admin.activity.descriptions.admin.user.updated', [
                'name' => $user->name,
                'email' => $user->email,
            ]),
            ['user_id' => $user->id],
        );

        return redirect()
            ->route('admin.users', $this->listQuery($request))
            ->with('success', __('app.admin.users.updated_success'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()?->id) {
            return redirect()
                ->route('admin.users', $this->listQuery($request))
                ->with('error', __('app.admin.users.cannot_delete_self'));
        }

        if ($this->wouldRemoveLastAdmin($user)) {
            return redirect()
                ->route('admin.users', $this->listQuery($request))
                ->with('error', __('app.admin.users.last_admin_protected'));
        }

        activity_log(
            'admin.user.deleted',
            __('app.admin.activity.descriptions.admin.user.deleted', [
                'name' => $user->name,
                'email' => $user->email,
            ]),
            ['user_id' => $user->id],
        );

        $user->delete();

        return redirect()
            ->route('admin.users', $this->listQuery($request))
            ->with('success', __('app.admin.users.deleted_success'));
    }

    private function wouldRemoveLastAdmin(User $user, ?UserRole $newRole = null): bool
    {
        if (! $user->isAdmin()) {
            return false;
        }

        if ($newRole === UserRole::Admin) {
            return false;
        }

        return User::query()->where('role', UserRole::Admin)->count() <= 1;
    }

    /**
     * @return array<string, mixed>
     */
    private function listQuery(Request $request): array
    {
        return $request->only(['q', 'role', 'per_page', 'page']);
    }
}
