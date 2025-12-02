<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Area;
use App\Models\AdminAuditLog;
use App\Policies\UserPolicy;
use App\Services\Auth\UserInvitationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct(private readonly UserInvitationService $invitationService)
    {
        $this->middleware('can:manage-users');
        $this->middleware('can:delete-users')->only('destroy');
        $this->middleware('can:assign-super-admin')->only('updateSuperAdmin');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $policy = app(UserPolicy::class);

        $query = User::with(['area', 'latestAdminAudit.performedBy']);

        $query = $policy->scopeViewAny($request->user(), $query);

        $users = $query->latest()->paginate(10);

        $areas = Area::orderBy('nombre')->get(['id', 'nombre']);

        $roleOptions = $this->roleOptions($request->user()?->is_super_admin ?? false);

        return view('users.index', compact('users', 'areas', 'roleOptions'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', User::class);

        $areas = Area::all();
        $roleOptions = $this->roleOptions($request->user()?->is_super_admin ?? false);

        return view('users.create', compact('areas', 'roleOptions'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $roleKeys = array_keys($this->roleOptions($request->user()?->is_super_admin ?? false));

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'rol' => ['required', Rule::in($roleKeys)],
            'area_id' => ['nullable', 'exists:areas,id'],
        ]);

        $role = $this->resolveRole($request->rol);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'rol' => $role?->value,
            'area_id' => $request->area_id,
            'password' => null, // Esto permite el login solo con Google al inicio
            'is_active' => true,
            'is_super_admin' => $role === UserRole::SUPER_ADMIN,
        ]);

        $this->invitationService->send($user, $request->user());
        $this->invitationService->recordAudit($user, $request->user(), 'invitation_sent', [
            'metadata' => [
                'area_id' => $user->area_id,
                'role' => $user->rol,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario invitado correctamente. Se envió un correo con las instrucciones de acceso.');
    }

    public function update(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'No puedes editar tu propio perfil desde esta vista.');
        }

        if ($user->is_super_admin && ! $request->user()->is_super_admin) {
            return back()->with('error', 'Solo otro super administrador puede editar esta cuenta.');
        }

        $this->authorize('update', $user);

        $roleKeys = array_keys($this->roleOptions($user->is_super_admin));

        $validator = Validator::make(
            $request->all(),
            [
                'name' => ['required', 'string', 'max:255'],
                'rol' => ['required', Rule::in($roleKeys)],
                'area_id' => ['nullable', 'exists:areas,id'],
                'is_active' => ['required', 'boolean'],
            ],
            [],
            [
                'name' => __('Nombre'),
                'rol' => __('Rol'),
                'area_id' => __('Área'),
                'is_active' => __('Estado de acceso'),
            ]
        );

        if ($validator->fails()) {
            return back()
                ->withErrors($validator, 'updateUser')
                ->with('edit_user_id', $user->id);
        }

        $validated = $validator->validated();

        $role = $this->resolveRole($validated['rol']);

        $payload = [
            'name' => $validated['name'],
            'rol' => $role?->value,
            'area_id' => $validated['area_id'] ?? null,
            'is_active' => (bool) $validated['is_active'],
        ];

        if ($payload['area_id'] === '') {
            $payload['area_id'] = null;
        }

        if ($payload['area_id'] !== null) {
            $payload['area_id'] = (int) $payload['area_id'];
        }

        if ($user->is_super_admin && ! $payload['is_active']) {
            return back()
                ->with('error', 'No puedes desactivar al super administrador.')
                ->with('edit_user_id', $user->id)
                ->withInput();
        }

        if ($user->is_super_admin && $role !== null && $role !== UserRole::SUPER_ADMIN) {
            return back()
                ->with('error', 'El super administrador debe conservar su rol asignado.')
                ->with('edit_user_id', $user->id)
                ->withInput();
        }

        $changes = $this->describeAuditChanges($user, $payload);

        if (empty($changes)) {
            return back()->with('success', __('Los datos de :name ya estaban actualizados.', ['name' => $user->name]));
        }

        $actingAdmin = $request->user();

        DB::transaction(function () use ($user, $payload, $actingAdmin, $request, $changes) {
            $user->forceFill($payload)->save();

            AdminAuditLog::create([
                'performed_by' => $actingAdmin->id,
                'target_user_id' => $user->id,
                'action' => 'updated_user_profile',
                'metadata' => [
                    'performed_by_email' => $actingAdmin->email,
                    'performed_by_name' => $actingAdmin->name,
                    'changes' => $changes,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ],
            ]);
        });

        return back()->with('success', __('Los datos de :name se actualizaron correctamente.', ['name' => $user->name]));
    }

    public function resendInvitation(Request $request, User $user)
    {
        if ($user->is_super_admin) {
            return back()->with('error', 'Las cuentas de super administrador no usan invitaciones.');
        }

        $this->authorize('update', $user);

        if (! $user->is_active) {
            return back()->with('error', 'Activa la cuenta antes de reenviar la invitación.');
        }

        if (! $user->hasPendingInvitation()) {
            return back()->with('error', 'Este usuario ya completó su registro o no tiene una invitación activa.');
        }

        $this->invitationService->send($user, $request->user());
        $this->invitationService->recordAudit($user, $request->user(), 'invitation_resent', [
            'metadata' => [
                'area_id' => $user->area_id,
                'role' => $user->rol,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Se reenviaron las instrucciones de acceso.');
    }

    public function updateStatus(Request $request, User $user)
    {
        if ($user->is_super_admin) {
            return back()->with('error', 'No puedes desactivar al super administrador.');
        }

        if ($user->is($request->user())) {
            return back()->with('error', 'No puedes cambiar tu propio estado desde esta vista.');
        }

        $this->authorize('updateStatus', $user);

        $validated = $request->validate([
            'status' => ['required', 'in:activate,deactivate'],
        ]);

        $user->forceFill([
            'is_active' => $validated['status'] === 'activate',
        ])->save();

        $message = $user->is_active
            ? __('El usuario :name fue activado.', ['name' => $user->name])
            : __('El usuario :name fue desactivado y ya no podrá iniciar sesión.', ['name' => $user->name]);

        return back()->with('success', $message);
    }

    public function updateSuperAdmin(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'No puedes cambiar tu propio estado de super administrador desde esta vista.');
        }

        $this->authorize('updateSuperAdmin', $user);

        $validator = Validator::make(
            $request->all(),
            [
                'action' => ['required', Rule::in(['promote', 'demote'])],
                'password' => ['required', 'current_password'],
            ],
            [],
            [
                'password' => __('Contraseña'),
            ]
        );

        if ($validator->fails()) {
            return back()
                ->withErrors($validator, 'superAdmin')
                ->with('super_admin_user_id', $user->id);
        }

        $actingAdmin = $request->user();
        $action = $validator->validated()['action'];

        if ($action === 'promote') {
            if ($user->is_super_admin) {
                return back()
                    ->with('error', 'Este usuario ya es super administrador.')
                    ->with('super_admin_user_id', $user->id);
            }

            $superAdminCount = User::where('is_super_admin', true)->count();

            if ($superAdminCount >= 2) {
                return back()
                    ->with('error', 'El límite de super administradores (2) ya fue alcanzado.')
                    ->with('super_admin_user_id', $user->id);
            }

            DB::transaction(function () use ($user, $actingAdmin, $request) {
                $user->forceFill([
                    'is_super_admin' => true,
                    'rol' => UserRole::SUPER_ADMIN->value,
                ])->save();

                AdminAuditLog::create([
                    'performed_by' => $actingAdmin->id,
                    'target_user_id' => $user->id,
                    'action' => 'promoted_to_super_admin',
                    'metadata' => [
                        'performed_by_email' => $actingAdmin->email,
                        'target_email' => $user->email,
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ],
                ]);
            });

            return back()->with('success', __(':name ahora es super administrador.', ['name' => $user->name]));
        }

        if (! $user->is_super_admin) {
            return back()
                ->with('error', 'Este usuario no es super administrador.')
                ->with('super_admin_user_id', $user->id);
        }

        $otherSuperAdminExists = User::where('is_super_admin', true)
            ->whereKeyNot($user->id)
            ->exists();

        if (! $otherSuperAdminExists) {
            return back()
                ->with('error', 'Debe permanecer al menos un super administrador activo.')
                ->with('super_admin_user_id', $user->id);
        }

        DB::transaction(function () use ($user, $actingAdmin, $request) {
            $newRole = $user->role();

            if ($newRole === UserRole::SUPER_ADMIN) {
                $newRole = UserRole::ADMIN_FARMACIA;
            }

            $user->forceFill([
                'is_super_admin' => false,
                'rol' => $newRole?->value ?? UserRole::ADMIN_FARMACIA->value,
            ])->save();

            AdminAuditLog::create([
                'performed_by' => $actingAdmin->id,
                'target_user_id' => $user->id,
                'action' => 'demoted_from_super_admin',
                'metadata' => [
                    'performed_by_email' => $actingAdmin->email,
                    'target_email' => $user->email,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ],
            ]);
        });

        return back()->with('success', __(':name ya no es super administrador.', ['name' => $user->name]));
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->is_super_admin) {
            return back()->with('error', 'No puedes eliminar al super administrador.');
        }

        if ($user->is($request->user())) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta desde esta pantalla.');
        }

        $this->authorize('delete', $user);

        $validator = Validator::make(
            $request->all(),
            ['password' => ['required', 'current_password']],
            [],
            ['password' => __('Contraseña')]
        );

        if ($validator->fails()) {
            return back()
                ->withErrors($validator, 'deleteUser')
                ->with('delete_user_id', $user->id);
        }

        $userName = $user->name;

        $user->delete();

        return back()->with('success', __('El usuario :name fue eliminado.', ['name' => $userName]));
    }

    protected function roleOptions(bool $includeSuper = false): array
    {
        $options = UserRole::forSelection();

        if ($includeSuper) {
            $options = [UserRole::SUPER_ADMIN->value => __('Super administrador')] + $options;
        }

        return $options;
    }

    protected function resolveRole(?string $role): ?UserRole
    {
        if ($role === null) {
            return null;
        }

        return UserRole::fromMixed($role);
    }

    protected function describeAuditChanges(User $user, array $payload): array
    {
        $changes = [];

        foreach ($payload as $field => $newValue) {
            $original = $user->getOriginal($field);

            if ($field === 'rol') {
                $originalRole = $user->role();
                $newRole = $this->resolveRole($newValue);

                if ($originalRole && $newRole && $originalRole === $newRole) {
                    continue;
                }

                $changes[$field] = [
                    'old' => $this->displayRole($originalRole?->value ?? $original),
                    'new' => $this->displayRole($newRole?->value ?? $newValue),
                ];

                continue;
            }

            if ($field === 'is_active') {
                $originalBool = (bool) $original;
                $newBool = (bool) $newValue;

                if ($originalBool === $newBool) {
                    continue;
                }

                $changes[$field] = [
                    'old' => $originalBool ? __('Activo') : __('Desactivado'),
                    'new' => $newBool ? __('Activo') : __('Desactivado'),
                ];

                continue;
            }

            if ((string) ($original ?? '') === (string) ($newValue ?? '')) {
                continue;
            }

            if ($field === 'area_id') {
                $areaIds = array_unique(array_filter([$original, $newValue]));
                $areas = empty($areaIds)
                    ? collect()
                    : Area::whereIn('id', $areaIds)->pluck('nombre', 'id');

                $changes[$field] = [
                    'old' => $original ? ($areas[$original] ?? $original) : null,
                    'new' => $newValue ? ($areas[$newValue] ?? $newValue) : null,
                ];

                continue;
            }

            $changes[$field] = [
                'old' => $original,
                'new' => $newValue,
            ];
        }

        return $changes;
    }

    protected function displayRole(?string $role): ?string
    {
        if ($role === null) {
            return null;
        }

        $enum = $this->resolveRole($role);

        if ($enum && $enum->isAssignableFromPanel()) {
            return $enum->label();
        }

        return Str::headline($role);
    }
}
