<?php

namespace RadiateCode\PermissionNameGenerator\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use RadiateCode\PermissionNameGenerator\Contracts\WithPermissionGenerator;

class RoutePermissionGenerator
{
    private $permissionGenerateControllers = [];

    private $globalExcludeControllers = [];

    private string $panel;

    public function __construct(string $panel = 'user')
    {
        $this->panel = $panel;
    }

    public function generate()
    {
        $this->permissionGenerateControllers = config(
            "permission-generator.permission-generate-controllers.panels.{$this->panel}",
            []
        );

        $this->globalExcludeControllers = config(
            "permission-generator.exclude-controllers.panels.{$this->panel}",
            []
        );

        $splitter = config('permission-generator.route-name-splitter-needle');

        $globalExcludedRoutes = config("permission-generator.exclude-routes.panels.{$this->panel}", []);

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

        return [
            'permissions' => $permissions,
            'only_permission_names' => $onlyPermissionNames
        ];
    }

    protected function generateKey($controllerInstance)
    {
        $key = $this->appendPermissionKey($controllerInstance);

        if (empty($key)) {
            $title = GeneratePermissionTitle::execute($controllerInstance);

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

                $title = GeneratePermissionTitle::execute($appendControllerClass);

                return strtolower(Str::slug($title, "-"));
            }

            return $appendTo;
        }

        return '';
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
        foreach ($this->permissionGenerateControllers as $prefix) {
            if (Str::contains($controller, $prefix)) {
                return true;
            }
        }

        return false;
    }
}
