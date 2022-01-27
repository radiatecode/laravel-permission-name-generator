<?php


namespace RadiateCode\LaravelRoutePermission;


use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use RadiateCode\LaravelRoutePermission\Contracts\WithPermissible;

class PermissibleRoutes
{
    private const CACHE_ROUTES_COUNT_KEY = 'routes-count';

    private const CACHE_ROUTES_KEY = 'routes';

    public static function getRoutes(): array
    {
        $routes = Route::getRoutes();

        $routesCount = count($routes);

        $cacheRoutes = self::getCacheRoutes($routesCount);

        if ($cacheRoutes !== null) {
            return $cacheRoutes;
        }

        $globalExcludeRoutes = config('route-permission.exclude-routes');

        $permissibleRoutes = [];

        $current = [];

        foreach ($routes as $route) {
            if (in_array($route->getName(), $globalExcludeRoutes)) {
                continue;
            }

            $actionName = $route->getActionName();

            $actionExtract = explode('@', $actionName);

            $controllerExtract = $actionExtract[0];

            if ($controllerExtract == 'Closure') {
                continue;
            }

            if ( ! self::isNamespaceAllowable($controllerExtract)) {
                continue;
            }

            $controller = '\\'.$controllerExtract;

            $controllerInstance = app($controller);

            if ( ! $controllerInstance instanceof WithPermissible) {
                continue;
            }

            $controllerMethod = $actionExtract[1];

            $excludeRoutes = $controllerInstance->getExcludeRoutes();

            $excludeMethods = $controllerInstance->getExcludeMethods();

            $currentRoutes = Arr::pluck($current,'route');

            if (in_array($route->getName(), $excludeRoutes)
                || in_array($controllerMethod, $excludeMethods)
                || in_array($route->getName(), $currentRoutes)
            ) {
                continue;
            }

            $title = self::permissionTitle($controllerInstance);

            $key = strtolower(Str::slug($title, "-"));

            $permissibleRoutes[$key][] = [
                'route' => $route->getName(),
                'title' => ucwords(str_replace(config('route-permission.route-name-splitter'), ' ', $route->getName()))
            ];

            $current = $permissibleRoutes[$key]; // avoid duplicate route entry
        }

        self::cacheRoutes($routesCount, $permissibleRoutes);

        return $permissibleRoutes;
    }

    protected static function permissionTitle($controllerInstance)
    {
        $title = $controllerInstance->getPermissionTitle();

        if ( ! empty($title)) {
            return $title;
        }

        // if no title defined then generate title from controller name
        $controllerName = class_basename($controllerInstance);

        // place white space between controller (PascalCase) name
        $name = preg_replace('/([a-z])([A-Z])/s', '$1 $2', $controllerName);

        if (Str::contains($controllerName, 'Controller')) {
            return str_replace('Controller', 'Permission', $name);
        }

        return $name.' Permission';
    }

    protected static function isNamespaceAllowable($namespace): bool
    {
        $allowableNamespaces = config('route-permission.allowable-controller-namespace');

        foreach ($allowableNamespaces as $allowableNamespace) {
            if (Str::contains($namespace, $allowableNamespace)) {
                return true;
            }
        }

        return false;
    }

    protected static function getCacheRoutes(int $routesCount)
    {
        if ( ! config('route-permission.cache-routes.cacheable')
            || ! Cache::has(self::CACHE_ROUTES_COUNT_KEY)
        ) {
            return null;
        }

        if ($routesCount !== Cache::get(self::CACHE_ROUTES_COUNT_KEY)) {
            Cache::forget(self::CACHE_ROUTES_COUNT_KEY);
            Cache::forget(self::CACHE_ROUTES_KEY);

            return null;
        }

        return Cache::get(self::CACHE_ROUTES_KEY);
    }

    protected static function cacheRoutes(int $routesCount, $permissibleRoutes)
    {
        if (config('route-permission.cache-routes.cacheable')
            && ! Cache::has(self::CACHE_ROUTES_COUNT_KEY)
        ) {
            Cache::put(self::CACHE_ROUTES_COUNT_KEY, $routesCount,now()->addDay());
            Cache::put(self::CACHE_ROUTES_KEY, $permissibleRoutes,now()->addDay());
        }
    }
}