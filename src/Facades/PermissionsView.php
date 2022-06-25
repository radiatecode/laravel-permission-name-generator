<?php


namespace RadiateCode\PermissionNameGenerator\Facades;


use Illuminate\Support\Facades\Facade;
use RadiateCode\PermissionNameGenerator\Html\Builder;

/**
 * @method static Builder withRolePermissions(string $roleName, array $rolePermissions)
 * @method static Builder addManualPermission(string $key, array $permissions)
 * @method static string view()
 * @method static string scripts($url = null)
 *
 * @see Builder
 */
class PermissionsView extends Facade
{
    protected static function getFacadeAccessor(){
        return 'permission.view.builder';
    }
}