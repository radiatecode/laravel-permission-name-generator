<?php


namespace RadiateCode\PermissionNameGenerator;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use RadiateCode\PermissionNameGenerator\Enums\Constant;
use RadiateCode\PermissionNameGenerator\Services\GeneratePermissionTitle;
use RadiateCode\PermissionNameGenerator\Services\RoutePermissionGenerator;
use RadiateCode\PermissionNameGenerator\Services\ResourcePermissionGenerator;

class Permissions
{
    protected $onlyPermissionsNames = [];

    protected $permissions = [];

    protected $splitter;

    protected string $panel = 'user';

    protected array $excludePermissions = [];

    public function __construct()
    {
        $this->splitter = config('permission-generator.route-name-splitter-needle');
    }

    public static function make(): Permissions
    {
        return new self();
    }

    public function panel(string $panel)
    {
        $this->panel = $panel;

        return $this;
    }

    public function exclude(array $permissions)
    {
        $this->excludePermissions = $permissions;

        return $this;
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

        return $this;
    }

    public function fromRoutes()
    {
        if ($this->hasCachedPermissions()) {
            return $this;
        }

        $routePermissionGenerator = (new RoutePermissionGenerator($this->panel))->exclude($this->excludePermissions)->generate();

        $this->permissions = $routePermissionGenerator['permissions'];
        $this->onlyPermissionsNames = $routePermissionGenerator['only_permission_names'];

        $this->customPermissions();

        return $this;
    }

    public function get(): array
    {
        if (!$this->hasCachedPermissions()) {
            ksort($this->permissions);

            $this->sectionPermissions();

            $this->cachePermissions();

            return $this->permissions;
        }

        return Cache::get($this->permissionCacheKey());
    }

    public function getOnlyPermissionsNames()
    {
        if (!$this->hasCachedPermissions()) {
            return $this->onlyPermissionsNames;
        }

        return Cache::get($this->permissionNameCacheKey());
    }

    protected function customPermissions(): Permissions
    {
        $customPermissions = config("permission-generator.custom-permissions.panels.{$this->panel}", []);

        if (is_array($customPermissions) && !empty($customPermissions)) {
            foreach ($customPermissions as $key => $permission) {
                foreach ($permission as $item) {
                    // when the permission only contains permission name
                    if (!is_array($item)) {

                        // skip if permission exist
                        if (in_array($item, $this->onlyPermissionsNames)) {
                            continue;
                        }

                        $this->permissions[$key][] = [
                            'name' => $item,
                            'text' => ucwords(str_replace($this->splitter, ' ', $item)),
                        ];

                        $this->onlyPermissionsNames[] = $item;

                        continue;
                    }

                    // skip if permission array format is invalid
                    if (!array_key_exists('name', $item) || !array_key_exists('text', $item)) {
                        continue;
                    }

                    // skip if permission exist
                    if (in_array($item['name'], $this->onlyPermissionsNames)) {
                        continue;
                    }

                    // when permission has valid permission structure (ex: text, name key available)   
                    $this->permissions[$key][] = $item;

                    $this->onlyPermissionsNames[] = $item['name'];
                }
            }
        }

        return $this;
    }

    protected function sectionPermissions()
    {
        $permissionsSection = config("permission-generator.permissions-section.panels.{$this->panel}", []);

        if (empty($permissionsSection)) {
            return $this;
        }

        $sectionWisePermissions = [];

        foreach ($permissionsSection as $section => $permissions) {
            foreach ($permissions as $permissionsTitle) {
                // check is the permissions title is key or class
                if (class_exists($permissionsTitle)) {
                    $permissionsTitleClassInstance = app("\\" . $permissionsTitle);

                    $title = GeneratePermissionTitle::execute($permissionsTitleClassInstance);

                    $permissionsTitle = strtolower(Str::slug($title, "-"));
                }

                if (array_key_exists($permissionsTitle, $this->permissions)) {
                    $sectionWisePermissions[$section]['section'] = str_replace(['\'', '/', '"', ',', ';', '<', '>', '.', '_', '-', ':'], ' ', $section);
                    $sectionWisePermissions[$section]['permissions'][$permissionsTitle] = $this->permissions[$permissionsTitle];

                    unset($this->permissions[$permissionsTitle]);
                }
            }

            if (!empty($sectionWisePermissions)) {
                ksort($sectionWisePermissions[$section]['permissions']);
            }
        }

        $this->permissions = array_merge($sectionWisePermissions, $this->permissions);

        return $this;
    }

    protected function hasCachedPermissions(): bool
    {
        return config('permission-generator.cache-permissions')
            && Cache::has($this->permissionCacheKey());
    }

    protected function cachePermissions()
    {
        if (!$this->hasCachedPermissions() && !empty($this->permissions)) {
            Cache::put(
                $this->permissionCacheKey(),
                $this->permissions,
                now()->addDay()
            );

            Cache::put(
                $this->permissionNameCacheKey(),
                $this->onlyPermissionsNames,
                now()->addDay()
            );
        }
    }

    protected function permissionCacheKey()
    {
        return  Constant::CACHE_PERMISSIONS . ":" . $this->panel;
    }

    protected function permissionNameCacheKey()
    {
        return  Constant::CACHE_ONLY_PERMISSIONS . ":" . $this->panel;
    }
}
