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

    protected string $source = 'routes';

    protected array $resources = [];

    /**
     * @param  string  $roleName
     * @param  array  $rolePermissions
     * @param  string|null  $permissionsSaveUrl // role permissions save url
     *
     * @return $this
     */
    public function withRolePermissions(
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
     * Permissions generate from route names
     *
     * @return Builder
     */
    public function fromRoutes()
    {
        $this->source = 'routes';

        return $this;
    }

    /**
     * Permissions generate from resources
     *
     * @param array $resources
     * @return Builder
     */
    public function fromResources(array $resources)
    {
        $this->source = 'resources';

        $this->resources = $resources;

        return $this;
    }

    /**
     * @param  string  $view
     * @param  array  $data
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function view(string $view, array $data = [])
    {
        return \view($view, $data)
            ->with('permissionCards', $this->render())
            ->with('permissionScripts', $this->scripts());
    }

    protected function render(array $permissions = []): string
    {

        return View::make(
            'permission-generator::permission',
            [
                'permissions'     => $this->resolvePermissions(),
                'roleName'        => $this->roleName,
                'rolePermissions' => $this->rolePermissions,
            ]
        )->render();
    }

    protected function scripts(): string
    {
        return View::make('permission-generator::scripts', ['url' => $this->url])->render();
    }

    protected function resolvePermissions(): array
    {
        if ($this->source == 'routes') {
            return Permissions::make()->fromRoutes()->get();
        }

        return Permissions::make()->fromResources($this->resources)->get();
    }
}
