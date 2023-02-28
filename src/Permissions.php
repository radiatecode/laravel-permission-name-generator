<?php


namespace RadiateCode\PermissionNameGenerator;

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
        if (!$this->hasCachedPermissions()) {
            ksort($this->permissions);

            $this->sectionPermissions(); // if any

            return $this->permissions;
        }

        return Cache::get(Constant::CACHE_PERMISSIONS);
    }

    public function getOnlyPermissionsNames()
    {
        if (!$this->hasCachedPermissions()) {
            return sort($this->onlyPermissionsNames);
        }

        return Cache::get(Constant::CACHE_ONLY_PERMISSIONS);
    }

    protected function customPermissions(): Permissions
    {
        $customPermissions = config('permission-generator.custom-permissions');

        if (is_array($customPermissions) && !empty($customPermissions)) {
            foreach ($customPermissions as $key => $permission) {
                foreach ($permission as $item) {
                    // when the permission only contains permission name
                    if (!is_array($item)) {
                        $this->permissions[$key][] = [
                            'name' => $item,
                            'text' => ucwords(str_replace($this->splitter, ' ', $item)),
                        ];

                        continue;
                    }

                    // when permission has valid permission structure (ex: slug, name key available)   
                    $this->permissions[$key][] = $item;
                }
            }
        }

        return $this;
    }

    protected function sectionPermissions()
    {
        $permissionsSection = config('permission-generator.permissions-section');

        if (empty($permissionsSection)) {
            return $this;
        }

        $sectionWisePermissions = [];

        foreach ($permissionsSection as $section => $permissions) {
            foreach ($permissions as $permission) {
                $sectionWisePermissions[$section]['section'] = str_replace(['\'', '/', '"', ',', ';', '<', '>', '.', '_', '-', ':'], ' ', $section);
                $sectionWisePermissions[$section]['permissions'][$permission] = $this->permissions[$permission];

                unset($this->permissions[$permission]);
            }

            ksort($sectionWisePermissions[$section]['permissions']);
        }

        $this->permissions = array_merge($sectionWisePermissions, $this->permissions);

        return $this;
    }

    protected function hasCachedPermissions(): bool
    {
        return config('permission-generator.cache-permissions')
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
