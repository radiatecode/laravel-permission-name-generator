<?php


namespace RadiateCode\PermissionNameGenerator\Traits;


trait PermissionGenerator
{
    private $title = '';

    private $excludeMethods = [];

    private $appendTo = '';

    /**
     * Set the permission group title
     *
     * @param  string  $title
     *
     * @return $this
     */
    protected function permissionGroupTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Excluded routes by controller method, 
     * so that it can not generate as permission name 
     *
     * @param ...$methods
     *
     * @return $this
     */
    protected function permissionExcludeMethods(...$methods)
    {
        $this->excludeMethods = $methods;

        return $this;
    }

    /**
     * Permission names append to another permission group
     *
     * @param string $key // SomeController::class | permission-group-key
     * @return void
     */
    protected function permissionAppendTo(string $key)
    {
        $this->appendTo = $key;

        return $this;
    }

    /**
     * @return string
     */
    public function getPermissionTitle(): string
    {
        return $this->title;
    }

    /**
     * @return array
     */
    public function getExcludeMethods(): array
    {
        return $this->excludeMethods;
    }

    /**
     * @return string
     */
    public function getAppendTo(): string
    {
        return $this->appendTo;
    }
}
