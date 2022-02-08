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
        $permissibleMiddlewares = config('route-permission.permission-middlewares');

        $globalExcludeControllers = config('route-permission.exclude-controllers');

        $globalExcludedRoutes = config('route-permission.exclude-routes');

        $splitter = config('route-permission.route-name-splitter');

        $routes = Route::getRoutes();

        $routesCount = count($routes);

        $cacheRoutes = self::getCacheRoutes($routesCount);

        if ($cacheRoutes !== null) {
            return $cacheRoutes;
        }

        $permissibleRoutes = [];

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

            $routeMiddlewares = $route->gatherMiddleware();

            if ($controller == 'Closure'
                || ! self::isPermissibleMiddleware($permissibleMiddlewares,$routeMiddlewares)
                || in_array($controller, $globalExcludeControllers)
            ) {
                continue;
            }

            $controllerInstance = app('\\'.$controller);

            // if the controller use the WithPermissible interface then find the excluded routes
            if ($controllerInstance instanceof WithPermissible) {
                $controllerMethod = $actionExtract[1];

                $excludeMethods = $controllerInstance->getExcludeMethods();

                if (in_array($controllerMethod, $excludeMethods)) {
                    continue;
                }
            }

            $tempPluckRoutes = Arr::pluck($tempRoutes, 'route');

            // check is the current route store in temp routes in order to avoid duplicacy
            if (in_array($routeName, $tempPluckRoutes)) {
                continue;
            }

            $title = self::generatePermissionTitle($controllerInstance);

            $key = strtolower(Str::slug($title, "-"));

            $permissibleRoutes[$key][] = [
                'route' => $routeName,
                'title' => ucwords(str_replace($splitter, ' ', $routeName)),
            ];

            $tempRoutes = $permissibleRoutes[$key];
        }

        self::cacheRoutes($routesCount, $permissibleRoutes);

        return $permissibleRoutes;
    }

    protected static function generatePermissionTitle($controllerInstance)
    {
        // if the controller use the WithPermissible interface then get the title
        if ($controllerInstance instanceof WithPermissible) {
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

    protected static function isPermissibleMiddleware($permissibleMiddlewares,$currentRouteMiddlewares): bool
    {
        foreach ($currentRouteMiddlewares as $middleware) {
            if (in_array($middleware,$permissibleMiddlewares)) {
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
            Cache::put(self::CACHE_ROUTES_COUNT_KEY, $routesCount,
                now()->addDay());
            Cache::put(self::CACHE_ROUTES_KEY, $permissibleRoutes,
                now()->addDay());
        }
    }
}