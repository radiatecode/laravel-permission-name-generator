<?php


namespace RadiateCode\LaravelRoutePermission\Traits;


trait Permissible
{
    private $title = '';

    private $excludeRoutes = [];

    private $excludeMethods = [];

    protected function permissibleTitle(string $title){
        $this->title = $title;

        return $this;
    }

    protected function permissionExcludeRoutes(...$routes){
        $this->excludeRoutes = $routes;

        return $this;
    }

    protected function permissionExcludeMethods(...$methods){
        $this->excludeMethods = $methods;

        return $this;
    }

    public function getPermissionTitle(): string{
        return $this->title;
    }

    public function getExcludeRoutes(): array
    {
        return $this->excludeRoutes;
    }

    public function getExcludeMethods(): array
    {
        return $this->excludeMethods;
    }
}