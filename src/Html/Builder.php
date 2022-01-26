<?php


namespace RadiateCode\LaravelRoutePermission\Html;


use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use RadiateCode\LaravelRoutePermission\PermissibleRoutes;

class Builder
{
    public static function permissionButtons(): HtmlString
    {
        $permissionButtons = config('route-permission.permission-buttons');

        $html = '<div class="col-md-12 col-lg-12 col-sm-12">
                    <div class="card">
                        <div class="card-footer">
                            <div class="permission-buttons" style="float: right !important">
                            '.(implode(' ',$permissionButtons)).'
                            </div>
                        </div>
                    </div>
                </div>';

        return new HtmlString($html);
    }

    public static function permissionCards(): string
    {
        return View::make('route-permission::permission', ['routes' => PermissibleRoutes::getRoutes()])->render();
    }

    public static function permissionScripts(): string
    {
        return View::make('route-permission::scripts')->render();
    }
}