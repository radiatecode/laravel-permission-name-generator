<?php


namespace RadiateCode\PermissionNameGenerator\Traits;


trait PermissionGenerator
{
    private $title = '';

    private $ignore = false;

    private $excludeMethods = [];

    private $appendTo = '';

    /**
     * Set the permissions title
     * 
     *
     * @param  string  $title
     *
     * @return $this
     */
    protected function permissionsTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Excluded routes by controller method, so that it can not generate as permission name 
     *
     * @param ...$methods
     *
     * @return $this
     */
    protected function permissionsExclude(...$methods)
    {
        $this->excludeMethods = $methods;

        return $this;
    }

    /**
     * Permission names append to another permission group
     *
     * @param string $key // SomeController::class | permissions-title key
     * @return void
     */
    protected function permissionsAppendTo(string $key)
    {
        $this->appendTo = $key;

        return $this;
    }

    /**
     * Permission ignored when value true
     *
     * @param boolean $ignore
     * @return void
     */
    protected function permissionsIgnoreWhen(bool $ignore)
    {
        $this->ignore = $ignore;

        return $this;
    }

    /**
     * @return string
     */
    public function getPermissionsTitle(): string
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

    public function isPermissionsIgnored(): bool
    {
        return $this->ignore;
    }
}
