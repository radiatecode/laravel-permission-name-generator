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

    private $tail = [];

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

        $permissionsTobeInject = [];

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

            // check is the current route store in temp routes in order to avoid duplicacy
            if (in_array($routeName, $onlyPermissionNames)) {
                continue;
            }

            // append key generate
            $appendKey = $this->appendPermissionKey($controllerInstance);

            $title = $this->generatePermissionTitle($controllerInstance);

            // current controller key generate
            $currentKey = strtolower(Str::slug($title, "-"));

            // check if append key is not empty
            if (!empty($appendKey)) {
                // find the append key in the permission deep level
                $exist = $this->keyExists($permissions, $appendKey);

                if ($exist) {
                    // catch the position of the append key
                    $livingTails = $this->tail();

                    // assign the current permission to that position
                    $permissionInjectableKey = $livingTails . "." . $currentKey;

                    $this->arr_push($permissions, $permissionInjectableKey, [
                        'name' => $routeName, // permission name
                        'text' => ucwords(str_replace($splitter, ' ', $routeName)), // permission title
                    ]);
                } else { // if append key is empty
                    $permission = [
                        'name' => $routeName, // permission name
                        'text' => ucwords(str_replace($splitter, ' ', $routeName)), // permission title
                    ];

                    // then the appendable permission will be waiting list for append key to be generate
                    $permissionsTobeInject[$appendKey][$currentKey][] = $permission;
                }
            } else {
                $permissions[$currentKey][] = [
                    'name' => $routeName, // permission name
                    'text' => ucwords(str_replace($splitter, ' ', $routeName)), // permission title
                ];
            }
            
            $onlyPermissionNames[] = $routeName;

            $this->emptyTheTail();
        }

        $this->permissionToBeAppend($permissionsTobeInject, $permissions);

        return [
            'permissions' => $permissions,
            'only_permission_names' => $onlyPermissionNames
        ];
    }

    /**
     * Append links to existing nav-links
     *
     * @param $permissionsTobeInject
     * @param $permissions
     */
    protected function permissionToBeAppend(&$permissionsTobeInject, &$permissions)
    {
        foreach ($permissionsTobeInject as $key => $permission) {
            $exist = $this->keyExists($permissions, $key);

            if ($exist) {
                // get the position of the parent menu
                $livingTails = $this->tail() . '.' . $key;

                $appendablePermissions = Arr::get($permissions, $livingTails);

                $combinedPermissions = array_merge($appendablePermissions, $permission);

                Arr::set($permissions, $livingTails, $combinedPermissions);
            }

            unset($permissionsTobeInject[$key]);
        }
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

    /**
     * @return string
     */
    protected function tail(): string
    {
        return implode('.', array_reverse($this->tail));
    }

    protected function emptyTheTail()
    {
        $this->tail = [];
    }

    /**
     * @param  array  $arr
     * @param       $keySearch
     *
     * @return bool
     */
    private function keyExists(array $arr, $keySearch): bool
    {
        // is in base array?
        if (array_key_exists($keySearch, $arr)) {
            $this->tail[] = $keySearch;

            return true;
        }

        // check arrays contained in this array
        foreach ($arr as $key => $element) {
            if (is_array($element)) {
                if (array_key_exists($keySearch, $element)) {
                    $this->tail[] = $key;

                    return true;
                } else if ($this->keyExists($element, $keySearch)) {
                    $this->tail[] = $key;

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param $array
     * @param $key
     * @param $value
     *
     * @return array|mixed
     */
    private function arr_push(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        foreach ($keys as $i => $key) {
            if (count($keys) === 1) {
                break;
            }

            unset($keys[$i]);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)][] = $value;

        return $array;
    }
}
