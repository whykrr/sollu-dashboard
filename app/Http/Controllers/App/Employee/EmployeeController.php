<?php

namespace App\Http\Controllers\App\Employee;

use App\Constants\FlashDataVariable;
use App\Constants\ResourceMessage;
use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\Employee\GetEmployeeRequest;
use App\Http\Requests\App\User\StoreUserRequest;
use App\Http\Requests\App\User\UpdateUserRequest;
use App\Models\Role as ModelsRole;
use App\Models\User;
use App\Services\App\Employee\EmployeeService;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct(protected EmployeeService $employeeService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(GetEmployeeRequest $req, ?User $user = null)
    {
        $users = User::currentBusiness()
            ->selectedOutlet($req->get('outlet'))
            ->filters($req->safe()->only(['search', 'role', 'is_deleted']))
            ->sortable($req->validated('sort', 'created_at'), $req->validated('direction', 'desc'))
            ->with(['roles:id,name,label', 'outlets:id,name'])
            ->paginate($req->validated('perpage', 20))
            ->appends($req->query());

        if ($user) {
            $user->load(['roles:name', 'outlets:id']);
        }

        return inertia('Employee/Index', [
            'users' => $users,
            'params' => $req->validated(),
            'roles' => ModelsRole::where('business_id', $req->user()->business_id)->select('id', 'name', 'label')->get()->map(function ($row) {
                return [
                    'value' => $row->name,
                    'label' => $row->label ?? (RoleEnum::tryFrom($row->name)?->label() ?? $row->name),
                ];
            }),
            'user' => $user,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $this->employeeService->create($request->validated());

        return redirect()->route('employees.index')->with(
            FlashDataVariable::SUCCESS->value,
            ResourceMessage::CREATE_SUCCESS
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->employeeService->update($user, $request->validated());

        return redirect()->back()->with(
            FlashDataVariable::SUCCESS->value,
            ResourceMessage::UPDATE_SUCCESS
        );
    }

    /**
     * Soft deletes the specified resource from storage.
     */
    public function delete(Request $req, User $user)
    {
        $this->employeeService->delete($user);

        return redirect()->back()->with(
            FlashDataVariable::SUCCESS->value,
            ResourceMessage::DELETE_SUCCESS
        );
    }

    /**
     * Restore the specified resource to storage.
     */
    public function restore(Request $req, User $user)
    {
        $this->employeeService->restore($user);

        return redirect()->back()->with(
            FlashDataVariable::SUCCESS->value,
            ResourceMessage::RESTORE_SUCCESS
        );
    }

    /**
     * Permanently destroy the specified resource from storage.
     */
    public function destroy(Request $req, User $user)
    {
        $this->employeeService->destroy($user);

        return redirect()->back()->with(
            FlashDataVariable::SUCCESS->value,
            ResourceMessage::PURGE_SUCCESS
        );
    }
}
