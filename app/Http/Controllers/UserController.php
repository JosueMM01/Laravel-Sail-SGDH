<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Area;
use App\Models\AdminAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage-users');
        $this->middleware('can:delete-users')->only('destroy');
        $this->middleware('can:assign-super-admin')->only('updateSuperAdmin');
    }

    public function index()
    {
        $users = User::with(['area', 'latestAdminAudit.performedBy'])
            ->latest()
            ->paginate(10);

        $areas = Area::orderBy('nombre')->get(['id', 'nombre']);

        $roleOptions = $this->roleOptions();

        return view('users.index', compact('users', 'areas', 'roleOptions'));
    }

    public function create()
    {
        $areas = Area::all();
        $roleOptions = $this->roleOptions();

        return view('users.create', compact('areas', 'roleOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'rol' => ['required', Rule::in(array_keys($this->roleOptions()))],
            'area_id' => ['nullable', 'exists:areas,id'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'rol' => $this->normalizeRoleValue($request->rol),
            'area_id' => $request->area_id,
            'password' => null, // ¡CLAVE! Esto permite el login solo con Google al inicio
            'is_active' => true,
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario invitado correctamente.');
    }

    public function update(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'No puedes editar tu propio perfil desde esta vista.');
        }

        if ($user->is_super_admin && ! $request->user()->is_super_admin) {
            return back()->with('error', 'Solo otro super administrador puede editar esta cuenta.');
        }

        $roleKeys = array_keys($this->roleOptions());

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

        $payload = [
            'name' => $validated['name'],
            'rol' => $this->normalizeRoleValue($validated['rol']),
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

    public function updateStatus(Request $request, User $user)
    {
        if ($user->is_super_admin) {
            return back()->with('error', 'No puedes desactivar al super administrador.');
        }

            if ($user->is($request->user())) {
            return back()->with('error', 'No puedes cambiar tu propio estado desde esta vista.');
        }

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
                $user->forceFill(['is_super_admin' => true])->save();

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
            $user->forceFill(['is_super_admin' => false])->save();

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

    protected function roleOptions(): array
    {
        return [
            'admin_farmacia' => __('Administrador de farmacia'),
            'personal_area' => __('Personal de área'),
        ];
    }

    protected function normalizeRoleValue(?string $role): ?string
    {
        if ($role === null) {
            return null;
        }

        $normalized = Str::slug(str_replace(['_', '-'], ' ', strtolower($role)));

        return match ($normalized) {
            'administrador', 'admin-farmacia' => 'admin_farmacia',
            'personal-area' => 'personal_area',
            default => $role,
        };
    }

    protected function describeAuditChanges(User $user, array $payload): array
    {
        $changes = [];

        foreach ($payload as $field => $newValue) {
            $original = $user->getOriginal($field);

            if ($field === 'rol') {
                $originalNormalized = $this->normalizeRoleValue($original);

                if ($originalNormalized === $newValue) {
                    continue;
                }

                $changes[$field] = [
                    'old' => $this->displayRole($original),
                    'new' => $this->displayRole($newValue),
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

        $normalized = $this->normalizeRoleValue($role);

        return $this->roleOptions()[$normalized] ?? Str::headline($role);
    }
}
