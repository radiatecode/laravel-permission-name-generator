<?php


namespace RadiateCode\PermissionNameGenerator;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use RadiateCode\PermissionNameGenerator\Contracts\WithPermissionGenerator;

class Permissions
{
    private const CACHE_ROUTES_COUNT_KEY = 'routes-count';

    private const CACHE_ROUTES_KEY = 'routes';

    private static $permissibleMiddlewares = [];

    public static function get(): array
    {
        self::$permissibleMiddlewares = config('permissions-generator.permission-middlewares');

        $globalExcludeControllers = config('permissions-generator.exclude-controllers');

        $globalExcludedRoutes = config('permissions-generator.exclude-routes');

        $splitter = config('permissions-generator.route-name-splitter');

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

            $routeMiddlewares = $route->gatherMiddleware();

            if ($controller == 'Closure'
                || ! self::isPermissibleMiddleware($routeMiddlewares)
                || in_array($controller, $globalExcludeControllers)
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

            $tempPluckRoutes = Arr::pluck($tempRoutes, 'route');

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

        self::cacheRoutes($routesCount, $permissions);

        return $permissions;
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

    protected static function isPermissibleMiddleware($currentRouteMiddlewares): bool
    {
        foreach ($currentRouteMiddlewares as $middleware) {
            if (in_array($middleware,self::$permissibleMiddlewares)) {
                return true;
            }
        }

        return false;
    }

    protected static function getCacheRoutes(int $routesCount)
    {
        if ( ! config('permissions-generator.cache-routes.cacheable')
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
        if (config('permissions-generator.cache-routes.cacheable')
            && ! Cache::has(self::CACHE_ROUTES_COUNT_KEY)
        ) {
            Cache::put(self::CACHE_ROUTES_COUNT_KEY, $routesCount,
                now()->addDay());
            Cache::put(self::CACHE_ROUTES_KEY, $permissibleRoutes,
                now()->addDay());
        }
    }
}