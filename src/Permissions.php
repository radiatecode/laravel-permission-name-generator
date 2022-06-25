<?php


namespace RadiateCode\PermissionNameGenerator;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use RadiateCode\PermissionNameGenerator\Contracts\WithPermissionGenerator;
use RadiateCode\PermissionNameGenerator\Enums\Constant;

class Permissions
{
    private $controllerNamespacePrefixes = [];

    private $globalExcludeControllers = [];

    private $manualPermissions = [];

    public static function make(): Permissions
    {
        return new self();
    }

    public function get(): array
    {
        $this->controllerNamespacePrefixes = config(
            'permission-generator.controller-namespace-prefixes'
        );

        $this->globalExcludeControllers = config(
            'permission-generator.exclude-controllers'
        );

        $globalExcludedRoutes = config('permission-generator.exclude-routes');

        $splitter = config('permission-generator.route-name-splitter-needle');

        $routes = Route::getRoutes();

        $routesCount = count($routes);

        $cacheRoutes = self::getCacheRoutes($routesCount);

        if ($cacheRoutes !== null) {
            return $cacheRoutes;
        }

        $permissions = [];

        $tempRoutes = [];

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

            if ($controller == 'Closure'
                || ! $this->isControllerValid($controller)
                || $this->isExcludedController($controller)
            ) {
                continue;
            }


            $controllerInstance = app('\\'.$controller);

            // if the controller use the WithPermissible interface then find the excluded routes
            if ($controllerInstance instanceof WithPermissionGenerator) {
                $controllerMethod = $actionExtract[1];

                $excludeMethods = $controllerInstance->getExcludeMethods();

                if (in_array($controllerMethod, $excludeMethods)) {
                    continue;
                }
            }

            $tempPluckRoutes = Arr::pluck($tempRoutes, 'slug');

            // check is the current route store in temp routes in order to avoid duplicacy
            if (in_array($routeName, $tempPluckRoutes)) {
                continue;
            }

            $title = self::generatePermissionTitle($controllerInstance);

            $key = strtolower(Str::slug($title, "-"));

            $permissions[$key][] = [
                'slug' => $routeName,
                'name' => ucwords(str_replace($splitter, ' ', $routeName)),
            ];

            $tempRoutes = $permissions[$key];
        }

        // add manual permissions to rendered permissions
        if ( ! empty($this->manualPermissions)) {
            foreach ($this->manualPermissions as $key => $permission) {
                $permissions[$key][] = $permission;
            }
        }

        self::cachePermissions($routesCount, $permissions);

        return $permissions;
    }

    public function withManualPermissions(array $permissions): Permissions
    {
        $this->manualPermissions = $permissions;

        return $this;
    }

    protected static function generatePermissionTitle($controllerInstance)
    {
        // if the controller use the WithPermissible interface then get the title
        if ($controllerInstance instanceof WithPermissionGenerator) {
            $title = $controllerInstance->getPermissionTitle();

            if ( ! empty($title)) {
                return $title;
            }
        }

        // Or, generate permission title from controller name
        $controllerName = class_basename($controllerInstance);

        // place white space between controller (PascalCase) name
        $name = preg_replace('/([a-z])([A-Z])/s', '$1 $2', $controllerName);

        if (Str::contains($controllerName, 'Controller')) {
            return str_replace('Controller', 'Permission', $name);
        }

        return $name.' Permission';
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

    protected static function getCacheRoutes(int $routesCount)
    {
        if ( ! config('permission-generator.cache-permissions.cacheable')
            || ! Cache::has(Constant::CACHE_ROUTES_COUNT_KEY)
        ) {
            return null;
        }

        if ($routesCount !== Cache::get(Constant::CACHE_ROUTES_COUNT_KEY)) {
            Cache::forget(Constant::CACHE_ROUTES_COUNT_KEY);
            Cache::forget(Constant::CACHE_PERMISSIONS_KEY);

            return null;
        }

        return Cache::get(Constant::CACHE_PERMISSIONS_KEY);
    }

    protected static function cachePermissions(int $routesCount, $permissions)
    {
        if (config('permission-generator.cache-permissions.cacheable')
            && ! Cache::has(Constant::CACHE_ROUTES_COUNT_KEY)
            && ! empty($permissions)
        ) {
            Cache::put(
                Constant::CACHE_ROUTES_COUNT_KEY,
                $routesCount,
                now()->addDay()
            );
            Cache::put(
                Constant::CACHE_PERMISSIONS_KEY,
                $permissions,
                now()->addDay()
            );
        }
    }
}