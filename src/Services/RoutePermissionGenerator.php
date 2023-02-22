<?php

namespace RadiateCode\PermissionNameGenerator\Services;

use Illuminate\Support\Arr;
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
            'permission-generator.controller-namespace-prefixes'
        );

        $this->globalExcludeControllers = config(
            'permission-generator.exclude-controllers'
        );

        $splitter = config('permission-generator.route-name-splitter-needle');

        $globalExcludedRoutes = config('permission-generator.exclude-routes');

        $routes = Route::getRoutes();

        $tempRoutes = [];

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

            $tempPluckRoutes = Arr::pluck($tempRoutes, 'name');

            // check is the current route store in temp routes in order to avoid duplicacy
            if (in_array($routeName, $tempPluckRoutes)) {
                continue;
            }

            $key = $this->generateKey($controllerInstance);

            $permissions[$key][] = [
                'name' => $routeName, // permission name
                'title' => ucwords(str_replace($splitter, ' ', $routeName)), // permission title
            ];

            $onlyPermissionNames[] = $routeName;

            $tempRoutes = $permissions[$key];
        }

        return [
            'permissions' => $permissions,
            'only_permission_names' => $onlyPermissionNames
        ];
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
            $title = $controllerInstance->getPermissionTitle();

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
