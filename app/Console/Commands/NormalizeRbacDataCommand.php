<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Models\User;
use App\Services\App\Role\RoleProvisioningService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class NormalizeRbacDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sollu:normalize-rbac';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Normalize RBAC production data to multi-tenant structure';

    /**
     * Execute the console command.
     */
    public function handle(RoleProvisioningService $provisioningService)
    {
        $this->info('Starting RBAC multi-tenant normalization...');

        $businesses = Business::all();
        $this->withProgressBar($businesses, function (Business $business) use ($provisioningService) {
            // 1. Provision default tenant roles for this business
            $provisioningService->provision($business);

            // 2. Assign root user to owner role in this business
            $rootUser = $business->users()->where('is_root_user', true)->first();
            if ($rootUser) {
                setPermissionsTeamId($business->id);
                // Ensure root user gets the Owner role
                if (! $rootUser->hasRole('owner')) {
                    $rootUser->assignRole('owner');
                }
            }

            // 3. Fix pivot tables for all users in this business
            DB::table('model_has_roles')
                ->whereIn('model_id', $business->users()->pluck('id'))
                ->update(['business_id' => $business->id]);

            DB::table('model_has_permissions')
                ->whereIn('model_id', $business->users()->pluck('id'))
                ->update(['business_id' => $business->id]);
        });

        $this->newLine();

        // 4. Delete all old global roles (where business_id is null)
        $globalRolesCount = Role::whereNull('business_id')->count();
        if ($globalRolesCount > 0) {
            $this->warn("Deleting {$globalRolesCount} old global roles...");
            Role::whereNull('business_id')->delete();
        }

        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));

        $this->info('RBAC normalization completed successfully!');
    }
}
