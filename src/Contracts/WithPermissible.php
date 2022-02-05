<?php


namespace RadiateCode\LaravelRoutePermission\Contracts;


interface WithPermissible
{
    /**
     * Permissible title
     *
     * [It used to grouping the routes which are associated with same controller]
     *
     * @return string
     */
    public function getPermissionTitle(): string;

    /**
     * Exclude the routes by controller method
     *
     * @return array
     */
    public function getExcludeMethods(): array;
}