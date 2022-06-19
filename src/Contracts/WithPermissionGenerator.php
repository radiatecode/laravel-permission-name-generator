<?php


namespace RadiateCode\PermissionNameGenerator\Contracts;


interface WithPermissionGenerator
{
    /**
     * Permission title
     *
     * [It used to grouping the permissions]
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