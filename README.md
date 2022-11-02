# Laravel Permission Name Generator
[![Latest Version on Packagist](https://img.shields.io/packagist/v/radiatecode/laravel-permission-name-generator.svg?style=flat-square)](https://packagist.org/packages/radiatecode/laravel-permission-name-generator)
[![Total Downloads](https://img.shields.io/packagist/dt/radiatecode/laravel-permission-name-generator.svg?style=flat-square)](https://packagist.org/packages/radiatecode/laravel-permission-name-generator)

This package will generate permission names from route names. In many application we create static permission names (ex: create-post, edit-post, delete-post) to check user's accessability, using the package can helps you to generate permission names dynamically.
# Requirements
- [PHP >= 7.1](https://www.php.net/)
- [Laravel 5.7|6.x|7.x|8.x](https://github.com/laravel/framework)
- [JQuery](https://jquery.com/)
- [Bootstrap](https://getbootstrap.com/)
# Installation
You can install the package via composer:

    composer require radiatecode/laravel-permission-name-generator

Publish config file

    php artisan vendor:publish --provider="RadiateCode\PermissionNameGenerator\PermissionNameServiceProvider" --tag="permission-generator-config"

Publish default permission view files (optional)

        php artisan vendor:publish --provider="RadiateCode\PermissionNameGenerator\PermissionNameServiceProvider"

# Usage

## PermissionGenerator trait [Optional]
While this package generate permission names from route names, in some cases we might need to exclude some routes so that it won't generate as permission names. To do so implement the **WithPermissionGenerator** contracts in the controller, then use the **PermissionGenerator** trait. 

Available methods in **PermissionGenerator** trait

- `permissionExcludeMethods()` : use to exculde a route from being generated as permission name.
- `permissionGroupTitle()`: Use to set group title for permissions

**Example**
```php
use App\Http\Controllers\Controller;
use RadiateCode\LaravelRoutePermission\Contracts\WithPermissionGenerator;
use RadiateCode\LaravelRoutePermission\Traits\PermissionGenerator;

class OfficeController extends Controller implements WithPermissionGenerator
{
    use PermissionGenerator;
   
    public function __construct()
    {
         $this->permissionGroupTitle('Office Crud Permission')->permissionExcludeMethods('index','listDatatable'); // index and listDatatable associate routes won't be generated as permission names
    }
}
```

> **PermissionGenerator** trait is optional. Because if no group permission title defined, then this package dynamically generate a title based on controller name. Routes can be excluded in the config file in order to tell the package not to generate those routes as permission names.

## Get permissions names

    RadiateCode\PermissionNameGenerator\Permissions::make()->get();

**Output**

![Stats](img/permissions.png)

> Notice the key `department-permissions` it generated from `DepartmentController` and permissions are generated from DepartmentController's routes.

## Permission View Builder Facade
The package comes with predefined a view with permission names

[**PermissionViewBuilder** facade]. 

**Builder methods:**

- `view(string $view, array $data = [])`: set your view, this method will render your view with two predefined keys (permissionCards, permissionScripts) and put those keys according to you view layout

- `withRolePermissions(string $roleName,arra $rolePermissions,string $permissionsSaveUrl = null)`: This method helps you to pre-checked the permissions of a role, in the 3rd arg you can define a url where you can save the role permissions

### Submiting permissions can be get by

> ```php
>   $request->get('permissions');  // array of permissions
> ```

## Example
**In controller:**

```php
namespace RadiateCode\PermissionNameGenerator\Facades\PermissionsView;
use App\Models\Role;

class RoleController extends Controller
{
    public function permissionsShow($id)
    {
        $role = Role::query()->findOrFail($id);

        return PermissionsView::withRolePermissions(
            $role->role_name,
            json_decode($role->role_permissions), // assume role permissions stored as json encoded
            route('create-role-permission', $role->id) // permission save url for a role
        )->view('app.role.permissions'); // your view
    }
}
```
**In app/role/permissions.blade.php file:**
```html
@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">Role Permissions</h3>
                </div>
                <div class="card-body">
                    <!-- the key generated by permission view builder of permission name generator -->
                    {!! $permissionCards !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<!-- the key generated by permission view builder of permission name generator -->
    {!! $permissionScripts !!}
@endpush
```

> The layout is only for demo purpose, you should only notice the `$permissionCards` and `$permissionScripts` variables, and put those acording to your view layout. 
### Permissions view
![Stats](img/permission-view.png)

**Create the permission savings route**

```php
Route::post('/role/{id}/permissions/create',[RoleController::class,'permissionStore'])->name('create-role-permission');
```
```php
use \Illuminate\Http\Request;
class RoleController extends Controller
{
     public function permissionStore(Request $request,$id)
    {
        $role = Role::find($id);

        $role->role_permissions = json_encode($request->get('permissions')); // get the submitted permissions
        $role->save();

        return response()->json('success',201);
    }
}
```

## Configuration

Config the **config/permission-generator.php** file.

1. If route name contains any special char then split the name by that char. It will use to generate permission title. For example if route name is **create.post** then permission title would be **Create Post**
```php
/**
 * Split route name by defined needle
 */
'route-name-splitter-needle'    => '.',
```

2. You can defined custom permissions 
```php
/**
 * Custom permissions
 */
'custom-permissions'  => [
    //
],
```
> Example 
> ```php
> 'custom-permissions' = [
>    'user-permission' => ['active-user','inactive-user'],
>    'bonus-permission' => [
>       'name' => 'approve-own-department',
>       'title' => 'Approve Own Department'
>     ]
> ]
>
>```
> Note: notice the `user-permission` key which contains only permission name, the package dynamically make a title for the permission name.
>

3. We can define controller by it's namespace, it could be whole namespace or it could be sub/prefix of controller namespace. This config play vital role to generate permissions because permissions will be generated only for our defined controllers.

```php
/**
 * Define controller namespace
 *
 * By Default permissions will be generated from all controller's routes
 * 
 * [Note: permissions will be generated from those controller which contains the defined prefix]
 */
'controller-namespace-prefixes' => [
    'App\Http\Controllers',
],
```
4. Exclude routes by controller. If we want to exclude routes of a controller then we can exclude it. By default all auth related routes will be excluded from being generated as permission names.

```php
 /**
 * Exclude routes by controller whole namespace or sub/prefix of controller namespace
 *
 * By default all auth controller's routes will be excluded from being generated as permission names
 * 
 * [Note: We can exclude routes by defining controller name or namespace-prefix. All the routes associated with controller will be excluded]
 */
'exclude-controllers'           => [
    // exclude every route which associate with the namespace-prefix of controller 
    'App\Http\Controllers\Auth',
],
```

5. **Or,** we can exclude routes by route name

```php
 /**
 * Exclude routes by route name
 */
'exclude-routes'                => [
    // route.name
],
```

6. Caching the permission names

```php
/**
 * Cache the rendered permission names
 */
'cache-permissions'             => [
    'cacheable'    => true,
    'cache-driver' => env('CACHE_DRIVER', 'file'),
],
```
7. Permission card size (bootstrap grid)
```php
/**
 * Permission card size
 *
 * [NT: Predefined permission cards works on bootstrap]
 */
'card-size-class'               => 'col-md-3 col-lg-3 col-sm-12',
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

