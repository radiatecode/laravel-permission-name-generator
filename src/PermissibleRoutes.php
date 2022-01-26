<?php


namespace RadiateCode\LaravelRoutePermission;


use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use RadiateCode\LaravelRoutePermission\Contracts\WithPermissible;

class PermissibleRoutes
{
    private const ALLOWABLE_NAMESPACE = 'App\Http\Controllers';

    public static function getRoutes(): array
    {
        $routes = Route::getRoutes();

        $permissibleRoutes = [];

        $current = [];

        foreach ($routes as $route) {
            $action = $route->getAction();

            $namespace = $action['namespace'];

            if (Str::contains($namespace, self::ALLOWABLE_NAMESPACE)) {
                $actionName = $route->getActionName();

                $actionExtract = explode('@', $actionName);

                $controllerExtract = $actionExtract[0];

                if ($controllerExtract == 'Closure') {
                    continue;
                }

                $controllerMethod = $actionExtract[1];

                $controller = '\\'.$controllerExtract;

                $controllerInstance = app($controller);

                if ($controllerInstance instanceof WithPermissible) {
                    $title = $controllerInstance->getPermissionTitle();

                    // if no title defined then generate title from controller name
                    if (empty($title)) {
                        $controllerName = class_basename($controllerExtract);

                        if (Str::contains($controllerName, 'Controller')) {
                            $title = str_replace('Controller', ' Permission',
                                $controllerName);
                        }
                    }

                    $excludeRoutes = $controllerInstance->getExcludeRoutes();

                    $excludeMethods = $controllerInstance->getExcludeMethods();

                    $key = strtolower(Str::slug($title, "-"));

                    if (in_array($route->getName(), $excludeRoutes)
                        || in_array($controllerMethod, $excludeMethods)
                        || in_array($route->getName(), $current)
                    ) {
                        continue;
                    }

                    $permissibleRoutes[$key][] = $route->getName();

                    $current
                        = $permissibleRoutes[$key]; // avoid duplicate route entry
                }
            }
        }

        return $permissibleRoutes;
    }

}