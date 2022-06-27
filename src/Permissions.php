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

    protected $onlyPermissions = [];

    protected $permissions = [];

    public function __construct()
    {
        $this->generate();
    }

    public static function make(): Permissions
    {
        return new self();
    }

    public function get(): array
    {
        return $this->getCachedPermissions();
    }

    public function getOnlyPermissions()
    {
        if (! $this->hasCachedPermissions()) {
            return $this->onlyPermissions;
        }

        return Cache::get(Constant::CACHE_ONLY_PERMISSIONS);
    }

    protected function generate(): Permissions
    {
        if ($this->hasCachedPermissions()) {
            return $this;
        }

        $this->controllerNamespacePrefixes = config(
            'permission-generator.controller-namespace-prefixes'
        );

        $this->globalExcludeControllers = config(
            'permission-generator.exclude-controllers'
        );

        $globalExcludedRoutes = config('permission-generator.exclude-routes');

        $splitter = config('permission-generator.route-name-splitter-needle');

        $routes = Route::getRoutes();

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

            $title = $this->generatePermissionTitle($controllerInstance);

            $key = strtolower(Str::slug($title, "-"));

            $this->permissions[$key][] = [
                'slug' => $routeName,
                'name' => ucwords(str_replace($splitter, ' ', $routeName)),
            ];

            $this->onlyPermissions[] = $routeName;

            $tempRoutes = $this->permissions[$key];
        }

        // add custom permissions to rendered permissions
        $this->customPermissions();

        $this->cachePermissions();

        return $this;
    }

    protected function customPermissions(): Permissions
    {
        $customPermissions = config('permission-generator.custom-permissions');

        if (is_array($customPermissions) && ! empty($customPermissions)) {
            foreach ($customPermissions as $key => $permission) {
                if (array_key_exists(0, $permission)
                    && is_array(
                        $permission[0]
                    )
                ) {
                    foreach ($permission as $item) {
                        $this->permissions[$key][] = $item;
                    }

                    continue;
                }

                $this->permissions[$key][] = $permission;
            }
        }

        return $this;
    }

    protected function generatePermissionTitle($controllerInstance)
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

    protected function getCachedPermissions()
    {
        if ( ! $this->hasCachedPermissions()) {
            return $this->permissions;
        }

        return Cache::get(Constant::CACHE_PERMISSIONS);
    }

    protected function hasCachedPermissions(): bool
    {
        return config('permission-generator.cache-permissions.cacheable')
            && Cache::has(Constant::CACHE_PERMISSIONS);
    }

    protected function cachePermissions()
    {
        if (! $this->hasCachedPermissions() && ! empty($this->permissions)) {
            Cache::put(
                Constant::CACHE_PERMISSIONS,
                $this->permissions,
                now()->addDay()
            );

            Cache::put(
                Constant::CACHE_ONLY_PERMISSIONS,
                $this->onlyPermissions,
                now()->addDay()
            );
        }
    }
}