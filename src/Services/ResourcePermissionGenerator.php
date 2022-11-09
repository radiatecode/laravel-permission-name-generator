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
        $tags = config(
            'permission-generator.resource-permission-tags'
        );

        $resourcePermissions = [];

        $onlyPermissionNames = [];

        foreach ($this->resources as $resource) {

            // check is resource has includes key, if so then merge includes tags with global tags
            if (
                is_array($resource)
                && array_key_exists('includes', $resource)
                && !empty($resource['includes'])
            ) {
                $tags = array_merge($tags, $resource['includes']);
            }

            foreach ($tags as $tag) {
                // check is the resource has excludes key, if so then exclude the tag form generating permissions
                if (
                    is_array($resource)
                    && array_key_exists('excludes', $resource)
                    && !empty($resource['excludes'])
                    && in_array($tag, $resource['excludes'])
                ) {
                    continue;
                }

                $key = $resource . '-permission';

                // remove this special char from tag, and make it slugable
                $tagSlug = str_replace(['\'', '/', '"', ',', ';', '<', '>', '.', '_'], '-', $tag);

                // remove hyphens 
                $tagTitle = str_replace('-', ' ', $tagSlug);

                // generate permission name
                $permissionName = $tagSlug . '-' . $resource;
                $permissionTitle = ucwords("{$tagTitle} {$resource}");

                $onlyPermissionNames[] = $permissionName;

                $resourcePermissions[$key][] = [
                    'name' => $permissionName,
                    'title' => $permissionTitle,
                ];
            }
        }

        return [
            'permissions' => $resourcePermissions,
            'only_permission_names' => $onlyPermissionNames
        ];
    }
}
