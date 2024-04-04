<?php


namespace RadiateCode\PermissionNameGenerator\Html;

use Illuminate\Support\Facades\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Foundation\Application;
use RadiateCode\PermissionNameGenerator\Permissions;

class Builder
{
    protected $rolePermissions = [];

    protected $roleName = '';

    protected $url = null;

    protected array $permissions = [];

    protected string $view;

    protected array $viewData = [];

    /**
     * Mark or tick the stored role's permissions
     * 
     * @param  string  $roleName
     * @param  array  $rolePermissions
     * @param  string|null  $permissionsSaveUrl // role permissions save url
     *
     * @return $this
     */
    public function markRolePermissions(
        string $roleName,
        array $rolePermissions,
        string $permissionsSaveUrl = null
    ): Builder {
        $this->rolePermissions = $rolePermissions;

        $this->roleName = $roleName;

        $this->url = $permissionsSaveUrl;

        return $this;
    }

    /**
     * Permissions
     *
     * @param array $permissions
     * @return Builder
     */
    public function withPermissions(array $permissions)
    {
        $this->permissions = $permissions;

        return $this;
    }

    /**
     * @param  string  $view
     * @param  array  $data
     *
     * @return Builder
     */
    public function make(string $view, array $data = [])
    {
        $this->view = $view;

        $this->viewData = $data;

        return $this;
    }

    public function render(array $permissionViewData = [], array $permissionScriptData = [])
    {
        return \view($this->view, $this->viewData)
            ->with('permissionCards', $this->cards($permissionViewData))
            ->with('permissionScripts', $this->scripts($permissionScriptData));
    }

    protected function cards(array $permissionViewData = []): string
    {
        $data = array_merge([
            'permissions'     => $this->permissions,
            'roleName'        => $this->roleName,
            'rolePermissions' => $this->rolePermissions,
        ], $permissionViewData);

        return View::make(
            'permission-generator::permission',
            $data
        )->render();
    }

    protected function scripts(array $permissionScriptData = []): string
    {
        $data = array_merge([
            'url' => $this->url,
        ], $permissionScriptData);

        return View::make('permission-generator::scripts', $data)->render();
    }
}
