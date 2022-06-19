<?php


namespace RadiateCode\PermissionNameGenerator\Html;

use Illuminate\Support\Facades\View;
use RadiateCode\PermissionNameGenerator\Permissions;

class Builder
{
    protected $rolePermissions = [];

    protected $roleName = '';

    public function rolePermissions(string $roleName, array $rolePermissions): Builder
    {
        $this->rolePermissions = $rolePermissions;

        $this->roleName = $roleName;

        return $this;
    }

    public function view(): string
    {
        return View::make('permissions-generator::permission',
            [
                'routes'            => Permissions::get(),
                'roleName'   => $this->roleName,
                'rolePermissions'   => $this->rolePermissions
            ]
        )->render();
    }

    /**
     * @param  null  $url // role permissions save url
     *
     * @return string
     */
    public function scripts($url = null): string
    {
        return View::make('permissions-generator::scripts', ['url' => $url])->render();
    }
}