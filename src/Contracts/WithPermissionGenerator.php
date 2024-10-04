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
    public function getPermissionsTitle(): string;

    /**
     * Exclude the routes by controller method
     *
     * @return array
     */
    public function getExcludeMethods(): array;

    /**
     * Append permission to another permission
     *
     * @return string
     */
    public function getAppendTo(): string;

    /**
     * All permissions ignored if true
     *
     * @return bool
     */
    public function isPermissionsIgnored(): bool;

}