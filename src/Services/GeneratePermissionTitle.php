<?php

namespace RadiateCode\PermissionNameGenerator\Services;

use Illuminate\Support\Str;
use RadiateCode\PermissionNameGenerator\Contracts\WithPermissionGenerator;

class GeneratePermissionTitle
{
    public static function execute($controllerInstance)
    {
        // if the controller use the WithPermissible interface then get the title
        if ($controllerInstance instanceof WithPermissionGenerator) {
            $title = $controllerInstance->getPermissionsTitle();

            if (!empty($title)) {
                return $title;
            }
        }

        // Or, generate permission title from controller name
        $controllerName = class_basename($controllerInstance);

        // place white space between controller (PascalCase) name
        $name = preg_replace('/([a-z])([A-Z])/s', '$1 $2', $controllerName);

        if (Str::contains($controllerName, 'Controller')) {
            return str_replace('Controller', 'Permissions', $name);
        }

        return $name . ' Permissions';
    }
}
