<?php


namespace RadiateCode\LaravelRoutePermission\Facades;


use Illuminate\Support\Facades\Facade;
use RadiateCode\LaravelRoutePermission\Html\Builder;

/**
 * @method static Builder withRolePermissions(string $roleName, array $rolePermissions)
 * @method static string permissionView()
 * @method static string permissionScripts($url = null)
 *
 * @see Builder
 */
class PermissionViewBuilder extends Facade
{
    protected static function getFacadeAccessor(){
        return 'permission.view.builder';
    }
}