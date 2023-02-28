<?php

namespace RadiateCode\PermissionNameGenerator\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use RadiateCode\PermissionNameGenerator\Contracts\WithPermissionGenerator;

class RoutePermissionGenerator
{
    private $controllerNamespacePrefixes = [];

    private $globalExcludeControllers = [];

    public function generate()
    {
        $this->controllerNamespacePrefixes = config(
            'permission-generator.permission-generate-controllers'
        );

        $this->globalExcludeControllers = config(
            'permission-generator.exclude-controllers'
        );

        $splitter = config('permission-generator.route-name-splitter-needle');

        $globalExcludedRoutes = config('permission-generator.exclude-routes');

        $routes = Route::getRoutes();

        $onlyPermissionNames = [];

        $permissions = [];

        foreach ($routes as $route) {
            $routeName = $route->getName();

            // exclude routes which defined in the config
            if (in_array($routeName, $globalExcludedRoutes)) {
                continue;
            }

            $actionName = $route->getActionName();

            $actionExtract = explode('@', $actionName);

            $controller = $actionExtract[0];

            // $routeMiddlewares = $route->gatherMiddleware();

            if (
                $controller == 'Closure'
                || !$this->isControllerValid($controller)
                || $this->isExcludedController($controller)
            ) {
                continue;
            }


            $controllerInstance = app('\\' . $controller);

            // if the controller use the WithPermissible interface then find the excluded routes
            if ($controllerInstance instanceof WithPermissionGenerator) {
                $controllerMethod = $actionExtract[1];

                $excludeMethods = $controllerInstance->getExcludeMethods();

                if (in_array($controllerMethod, $excludeMethods)) {
                    continue;
                }
            }

            // check is the current route store in onlyPermissionNames in order to avoid duplicacy
            if (in_array($routeName, $onlyPermissionNames)) {
                continue;
            }

            $key = $this->generateKey($controllerInstance);

            $permissions[$key][] = [
                'name' => $routeName, // permission name
                'text' => ucwords(str_replace($splitter, ' ', $routeName)), // permission title
            ];

            $onlyPermissionNames[] = $routeName;
        }

        ksort($permissions);

        return [
            'permissions' => $this->sectionPermissions($permissions),
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
            foreach ($permissions as $permissionsTitle) {
                // check is the permissions title is key or class
                if (class_exists($permissionsTitle)) {
                    $permissionsTitleClassInstance = app("\\" . $permissionsTitle);

                    $title = $this->generatePermissionTitle($permissionsTitleClassInstance);

                    $permissionsTitle = strtolower(Str::slug($title, "-"));
                }

                if (array_key_exists($permissionsTitle, $generatedPermissions)) {
                    $sectionWisePermissions[$section]['section'] = str_replace(['\'', '/', '"', ',', ';', '<', '>', '.', '_', '-', ':'], ' ', $section);
                    $sectionWisePermissions[$section]['permissions'][$permissionsTitle] = $generatedPermissions[$permissionsTitle];

                    unset($generatedPermissions[$permissionsTitle]);
                }
            }

            if (!empty($sectionWisePermissions)) {
                ksort($sectionWisePermissions[$section]['permissions']);
            }
        }

        $generatedPermissions = array_merge($sectionWisePermissions, $generatedPermissions);

        return $generatedPermissions;
    }

    protected function generateKey($controllerInstance)
    {
        $key = $this->appendPermissionKey($controllerInstance);

        if (empty($key)) {
            $title = $this->generatePermissionTitle($controllerInstance);

            return strtolower(Str::slug($title, "-"));
        }

        return $key;
    }

    protected function appendPermissionKey($currentControllerInstance)
    {
        if ($currentControllerInstance instanceof WithPermissionGenerator) {
            $appendTo = $currentControllerInstance->getAppendTo();

            // generate key
            if (!empty($appendTo) && class_exists($appendTo)) {
                $appendControllerClass = app("\\" . $appendTo);

                $title = $this->generatePermissionTitle($appendControllerClass);

                return strtolower(Str::slug($title, "-"));
            }

            return $appendTo;
        }

        return '';
    }

    protected function generatePermissionTitle($controllerInstance)
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

    protected function isExcludedController($controller): bool
    {
        foreach ($this->globalExcludeControllers as $prefix) {
            if (Str::contains($controller, $prefix)) {
                return true;
            }
        }

        return false;
    }

    protected function isControllerValid($controller): bool
    {
        foreach ($this->controllerNamespacePrefixes as $prefix) {
            if (Str::contains($controller, $prefix)) {
                return true;
            }
        }

        return false;
    }
}
