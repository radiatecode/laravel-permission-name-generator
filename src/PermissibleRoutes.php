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

                $controllerExtract = explode('@', $actionName)[0];

                if ($controllerExtract == 'Closure') {
                    continue;
                }

                $controller = '\\' . $controllerExtract;

                $controllerInstance = app($controller);

                if ($controllerInstance instanceof WithPermissible) {
                    $title = $controllerInstance->getPermissionTitle();

                    $excludes = $controllerInstance->getExcludeRoutes();

                    $key = strtolower(Str::slug($title, "-"));

                    if (in_array($route->getName(),$excludes) || in_array($route->getName(),$current)){
                        continue;
                    }

                    $permissibleRoutes[$key][] = $route->getName();

                    $current = $permissibleRoutes[$key]; // avoid duplicate route entry
                }
            }
        }

        return $permissibleRoutes;
    }

}