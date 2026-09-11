<?php

namespace App\Http\Controllers\App\Settings;

use App\Constants\FlashDataVariable;
use App\Constants\ResourceMessage;
use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\Settings\StoreRoleRequest;
use App\Http\Requests\App\Settings\UpdateRoleRequest;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('role.view');

        $search = $request->query('search');

        $roles = Role::where('business_id', $request->user()->business_id)
            ->withCount('users')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('label', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return inertia('Settings/Role/Index', [
            'roles' => $roles,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Role $role)
    {
        $this->authorize('role.view');

        if ($role->business_id !== $request->user()->business_id) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return response()->json([
            'id' => $role->id,
            'label' => $role->label,
            'name' => $role->name,
            'is_default' => $role->is_default,
            'permissions' => $role->permissions()->pluck('name'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        $data = $request->validated();

        $roleName = Str::slug($data['label']);

        // Ensure unique name within business
        $count = 1;
        $originalName = $roleName;
        while (Role::where('business_id', $request->user()->business_id)->where('name', $roleName)->exists()) {
            $roleName = $originalName.'-'.$count;
            $count++;
        }

        $role = Role::create([
            'business_id' => $request->user()->business_id,
            'name' => $roleName,
            'label' => $data['label'],
            'guard_name' => 'business',
            'is_default' => false,
        ]);

        $role->syncPermissions($data['permissions']);

        return redirect()->back()->with(
            FlashDataVariable::SUCCESS->value,
            ResourceMessage::CREATE_SUCCESS
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        // Check if role belongs to this business
        if ($role->business_id !== $request->user()->business_id) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $data = $request->validated();

        // If trying to edit owner, abort
        if ($role->name === RoleEnum::OWNER->value) {
            abort(Response::HTTP_FORBIDDEN, 'Role Owner tidak dapat diubah izinnya.');
        }

        if (! $role->is_default) {
            $role->update([
                'label' => $data['label'],
            ]);
        }

        $role->syncPermissions($data['permissions']);

        return redirect()->back()->with(
            FlashDataVariable::SUCCESS->value,
            ResourceMessage::UPDATE_SUCCESS
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Role $role)
    {
        $this->authorize('role.delete');

        // Check if role belongs to this business
        if ($role->business_id !== $request->user()->business_id) {
            abort(Response::HTTP_FORBIDDEN);
        }

        // Anti-lockout guardrail
        if ($role->is_default) {
            abort(Response::HTTP_FORBIDDEN, 'Role bawaan tidak dapat dihapus.');
        }

        if ($role->users()->count() > 0) {
            abort(Response::HTTP_FORBIDDEN, 'Role masih digunakan oleh pengguna aktif.');
        }

        $role->delete();

        return redirect()->back()->with(
            FlashDataVariable::SUCCESS->value,
            ResourceMessage::DELETE_SUCCESS
        );
    }
}
