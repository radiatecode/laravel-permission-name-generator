<?php


namespace RadiateCode\LaravelRoutePermission\Traits;


trait Permissible
{
    private $title;

    private $excludeRoutes = [];

    protected function permissibleTitle(string $title){
        $this->title = $title;

        return $this;
    }

    public function getPermissibleGroup(): string{
        return $this->title;
    }

    protected function permissionExcludeOn(...$routes){
        $this->excludeRoutes = $routes;

        return $this;
    }

    public function getExcludeRoutes(): array
    {
        return $this->excludeRoutes;
    }
}