<?php

namespace RadiateCode\PermissionNameGenerator\Services;

class ResourcePermissionGenerator
{
    protected array $resources = [];

    public function __construct(array $resources)
    {
        $this->resources = $resources;
    }

    public function generate()
    {
        $actions = config(
            'permission-generator.resource-actions'
        );

        $resourcePermissions = [];

        $onlyPermissionNames = [];

        foreach ($this->resources as $resource => $val) {

            if (is_string($resource)) {
                if (is_array($val) && !empty($val)) {
                    $actions = array_merge($actions, $val);
                }
            } else {
                $resource = $val;
            }

            foreach ($actions as $action) {

                $key = $resource . '-permissions';

                // remove this special char from tag, and make it slugable
                $actionSlug = str_replace(['\'', '/', '"', ',', ';', '<', '>', '.', '_'], '-', $action);

                // remove hyphens 
                $actionTitle = str_replace('-', ' ', $actionSlug);

                // generate permission name
                $permissionName = $actionSlug . '-' . $resource;
                $permissionTitle = ucwords("{$actionTitle} {$resource}");

                $onlyPermissionNames[] = $permissionName;

                $resourcePermissions[$key][] = [
                    'name' => $permissionName,
                    'text' => $permissionTitle,
                ];
            }
        }

        ksort($resourcePermissions);

        return [
            'permissions' => $this->sectionPermissions($resourcePermissions),
            'only_permission_names' => $onlyPermissionNames
        ];
    }

    protected function sectionPermissions($generatedPermissions)
    {
        $permissionsSection = config('permission-generator.permissions-section');

        if (empty($permissionsSection)) {
            return $generatedPermissions;
        }

        $sectionWisePermissions = [];

        foreach ($permissionsSection as $section => $permissions) {
            foreach ($permissions as $permissionsTitleKey) {
                if (array_key_exists($permissionsTitleKey, $generatedPermissions)) {
                    $sectionWisePermissions[$section]['section'] = str_replace(['\'', '/', '"', ',', ';', '<', '>', '.', '_', '-', ':'], ' ', $section);
                    $sectionWisePermissions[$section]['permissions'][$permissionsTitleKey] = $generatedPermissions[$permissionsTitleKey];

                    unset($generatedPermissions[$permissionsTitleKey]);
                }
            }

            if (!empty($sectionWisePermissions)) {
                ksort($sectionWisePermissions[$section]['permissions']);
            }
        }

        $generatedPermissions = array_merge($sectionWisePermissions, $generatedPermissions);

        return $generatedPermissions;
    }
}
