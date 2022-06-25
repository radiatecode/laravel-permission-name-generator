<?php


namespace RadiateCode\PermissionNameGenerator\Traits;


trait PermissionGenerator
{
    private $title = '';

    private $excludeMethods = [];

    /**
     * Set the permission group title
     *
     * @param  string  $title
     *
     * @return $this
     */
    protected function permissionGroupTitle(string $title){
        $this->title = $title;

        return $this;
    }

    /**
     * Set excluded routes by controller method
     *
     * @param ...$methods
     *
     * @return $this
     */
    protected function permissionExcludeMethods(...$methods){
        $this->excludeMethods = $methods;

        return $this;
    }

    /**
     * @return string
     */
    public function getPermissionTitle(): string{
        return $this->title;
    }

    /**
     * @return array
     */
    public function getExcludeMethods(): array
    {
        return $this->excludeMethods;
    }
}