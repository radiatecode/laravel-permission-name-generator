<?php


namespace RadiateCode\PermissionNameGenerator\Facades;


use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Facade;
use Illuminate\Contracts\Foundation\Application;
use RadiateCode\PermissionNameGenerator\Html\Builder;

/**
 * @method static Builder withRolePermissions(string $roleName, array $rolePermissions, string $permissionsSaveUrl = null)
 * @method static Application|Factory|View view(string $view, array $data = [])
 *
 * @see Builder
 */
class PermissionsView extends Facade
{
    protected static function getFacadeAccessor(){
        return 'permission.view.builder';
    }
}