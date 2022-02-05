# Laravel Route Permission

The package used to generate route permission view / privilege view. Sometimes we create role permissions using middleware, So generating permission view for all routes is difficult task. This package can help to generate permission
view dynamically. Whenever new routes registered in **routes.php** permission view will be updated dynamically.


## Example
### Generate permission view
![Stats](img/example-1.png)

```php
class RoleController extends Controller
{
     public function permissions($id)
    {
        $role = Role::find($id);

        return view('app.role.permission')
            ->with('permissions',PermissionViewBuilder::withRolePermissions($role->role_name,json_decode($role->role_access))->permissionView())
            ->with('permission_scripts',PermissionViewBuilder::permissionScripts(route('preset.role.permissions',$id)));
    }
}
```
**In view (blade) file:**
```html
<div class="permissions">
    {!! $permissions !!}
</div>

......
<!-- generate scripts -->
{!! $permission_scripts !!}
```
# Requirements
- [PHP >= 7.1](https://www.php.net/)
- [Laravel 5.7|6.x|7.x|8.x](https://github.com/laravel/framework)
- [Jquery](https://www.chartjs.org/)
- [Bootstrap](https://www.chartjs.org/)
# Installation
You can install the package via composer:

    composer require radiatecode/laravel-route-permission

Publish config file

    php artisan vendor:publish --provider="RadiateCode\LaravelRoutePermission\PermissionServiceProvider" --tag="route-permission-config"

# Usages
## Permissible trait
It is used to defined permission title, exclude routes by methods in the controller class. First implement the **WithPermissible**
Interface in a controller, then use the **Permissible** trait.
```php
use App\Http\Controllers\Controller;
use RadiateCode\LaravelRoutePermission\Contracts\WithPermissible;
use RadiateCode\LaravelRoutePermission\Traits\Permissible;

class OfficeController extends Controller implements WithPermissible
{
    use Permissible;
   
    public function __construct()
    {
         $this->permissibleTitle('Office Crud Permission')
            ->permissionExcludeMethods('index','listDatatable'); // when necessary exclude specific routes by the controller methods
    }
}
```

> Permissible trait is optional. If no permissible title defined, then this package dynamically generate a title based on controller name, And routes can be excluded in the config file.

## Permissible routes

You can get permissible routes And make permission view in order to set role permissions.

    RadiateCode\LaravelRoutePermission\PermissibleRoutes::getRoutes


> Under the hood it gets all the routes which registered in **web.php** and only take those controller routes which are allowable (defined in **config** file). The permissible routes grouped by controller.

## Permission View Builder Facade
If you want to use predefined permission view then use **PermissionViewBuilder** facade. 

See the above [example](#example)

**Builder methods:**

- permissionView() : it generate bootstrap permissions card based on permissible routes.
- withRolePermissions($roleName,$rolePermissions) : it is used to checked all the permissions that have access to a particular role.
- permissionScripts($url = null) : it generate functions for check all and uncheck all buttons. the **$url** param used to submit the checked permissions for specific role.

## Config

In **route-permission.php** config file you can define allowable controller namespace. Only the routes which associate with allowable controller can be count as permissioble routes.

```php
    /**
     * Generate permissible routes for the allowable controller namespace
     *
     * [nt: namespaces could be whole controller classname or namespace prefix]
     */
    'allowable-controller-namespace' => [
        'App\Http\Controllers', // prefix
    ],

    .........
```

## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security
If you discover any security related issues, please email [radiate126@gmail.com](mailto:radiate126@gmail.com) instead of using the issue tracker. 

## Credits
- [Noor Alam](https://github.com/radiatecode)
- [All Contributors](https://github.com/radiatecode/laravel-route-permission/contributors)


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

