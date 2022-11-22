<?php


namespace RadiateCode\PermissionNameGenerator;

use Closure;
use Illuminate\Support\Facades\Cache;
use RadiateCode\PermissionNameGenerator\Enums\Constant;
use RadiateCode\PermissionNameGenerator\Services\ResourcePermissionGenerator;
use RadiateCode\PermissionNameGenerator\Services\RoutePermissionGenerator;

class Permissions
{
    protected $onlyPermissionsNames = [];

    protected $permissions = [];

    protected $splitter;

    public function __construct()
    {
        $this->splitter = config('permission-generator.route-name-splitter-needle');
    }

    public static function make(): Permissions
    {
        return new self();
    }

    public function fromResources(array $resources)
    {
        if ($this->hasCachedPermissions()) {
            return $this;
        }

        $resourceGenerator = (new ResourcePermissionGenerator($resources))->generate();

        $this->permissions = $resourceGenerator['permissions'];
        $this->onlyPermissionsNames = $resourceGenerator['only_permission_names'];

        $this->customPermissions();
        $this->cachePermissions();

        return $this;
    }

    public function fromRoutes()
    {
        if ($this->hasCachedPermissions()) {
            return $this;
        }

        $routePermissionGenerator = (new RoutePermissionGenerator())->generate();

        $this->permissions = $routePermissionGenerator['permissions'];
        $this->onlyPermissionsNames = $routePermissionGenerator['only_permission_names'];

        $this->customPermissions();
        $this->cachePermissions();

        return $this;
    }

    public function get(): array
    {
        return $this->getCachedPermissions();
    }

    public function getOnlyPermissionsNames()
    {
        if (!$this->hasCachedPermissions()) {
            return $this->onlyPermissionsNames;
        }

        return Cache::get(Constant::CACHE_ONLY_PERMISSIONS);
    }

    protected function customPermissions(): Permissions
    {
        $customPermissions = config('permission-generator.custom-permissions');

        if (is_array($customPermissions) && !empty($customPermissions)) {
            foreach ($customPermissions as $key => $permission) {
                // when the permission only contains permission name
                if (array_key_exists(0, $permission) && is_array($permission)) {
                    foreach ($permission as $item) {
                        $this->permissions[$key][] = [
                            'name' => $item,
                            'title' => ucwords(str_replace($this->splitter, ' ', $item)),
                        ];
                    }

                    continue;
                }

                // when permission has valid permission structure (ex: slug, name key available)   
                $this->permissions[$key][] = $permission;
            }
        }

        return $this;
    }

    protected function getCachedPermissions()
    {
        if (!$this->hasCachedPermissions()) {
            return $this->permissions;
        }

        return Cache::get(Constant::CACHE_PERMISSIONS);
    }

    protected function hasCachedPermissions(): bool
    {
        return config('permission-generator.cache-permissions.cacheable')
            && Cache::has(Constant::CACHE_PERMISSIONS);
    }

    protected function cachePermissions()
    {
        if (!$this->hasCachedPermissions() && !empty($this->permissions)) {
            Cache::put(
                Constant::CACHE_PERMISSIONS,
                $this->permissions,
                now()->addDay()
            );

            Cache::put(
                Constant::CACHE_ONLY_PERMISSIONS,
                $this->onlyPermissionsNames,
                now()->addDay()
            );
        }
    }
}
