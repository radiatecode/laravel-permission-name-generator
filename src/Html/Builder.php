<?php


namespace RadiateCode\LaravelRoutePermission\Html;


use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use RadiateCode\LaravelRoutePermission\PermissibleRoutes;

class Builder
{
    protected $rolePermissions = [];

    protected $roleName = '';

    public function permissionButtons(): HtmlString
    {
        $permissionButtons = config('route-permission.permission-buttons');

        $html = '<div class="col-md-12 col-lg-12 col-sm-12">
                    <div class="card">
                        <div class="card-footer">
                            <div class="permission-buttons" style="float: right !important">
                            '.(implode(' ', $permissionButtons)).'
                            </div>
                        </div>
                    </div>
                </div>';

        return new HtmlString($html);
    }

    public function withRolePermissions(string $roleName, array $rolePermissions): Builder
    {
        $this->rolePermissions = $rolePermissions;

        $this->roleName = $roleName;

        return $this;
    }

    public function permissionView(): string
    {
        $permissionButtons = config('route-permission.permission-buttons');

        return View::make('route-permission::permission',
            [
                'routes'            => PermissibleRoutes::getRoutes(),
                'roleName'   => $this->roleName,
                'rolePermissions'   => $this->rolePermissions,
                'permissionButtons' => $permissionButtons,
            ]
        )->render();
    }

    public function permissionScripts($url = null): string
    {
        return View::make('route-permission::scripts', ['url' => $url])->render();
    }
}