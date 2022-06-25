<?php


namespace RadiateCode\PermissionNameGenerator\Html;

use Illuminate\Support\Facades\View;
use RadiateCode\PermissionNameGenerator\Permissions;

class Builder
{
    protected $rolePermissions = [];

    protected $roleName = '';

    protected $manualPermissions = [];

    public function withRolePermissions(
        string $roleName,
        array $rolePermissions
    ): Builder {
        $this->rolePermissions = $rolePermissions;

        $this->roleName = $roleName;

        return $this;
    }

    /**
     * @param  string  $key // key can contain dot to indicate nested level
     * @param  array  $permissions
     *
     * @return $this
     */
    public function addManualPermission(string $key, array $permissions): Builder
    {
        $this->manualPermissions[$key] = $permissions;

        return $this;
    }

    public function view(): string
    {
        return View::make(
            'permission-generator::permission',
            [
                'routes'          => Permissions::make()->withManualPermissions($this->manualPermissions)->get(),
                'roleName'        => $this->roleName,
                'rolePermissions' => $this->rolePermissions,
            ]
        )->render();
    }

    /**
     * @param  null  $url  // role permissions save url
     *
     * @return string
     */
    public function scripts($url = null): string
    {
        return View::make('permission-generator::scripts', ['url' => $url])
            ->render();
    }
}