<?php


namespace RadiateCode\LaravelRoutePermission\Contracts;


interface WithPermissible
{
    public function getPermissionTitle(): string;

    public function getExcludeRoutes(): array;

    public function getExcludeMethods(): array;
}