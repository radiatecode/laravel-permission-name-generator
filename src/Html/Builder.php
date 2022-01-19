<?php


namespace RadiateCode\LaravelRoutePermission\Html;


use Illuminate\Support\HtmlString;
use RadiateCode\LaravelRoutePermission\PermissibleRoutes;

class Builder
{
    protected function li(string $route,string $routeTitle): string
    {
        return '<li><input class="form-check-input" type="checkbox" name="role_access[]" value="'.$route.'" id="'.$route.'">'
            .'<label class="form-check-label" for="'.$route.'">'.$routeTitle.'</label></li>';
    }

    protected function card(string $id,string $title,string $listTags): string
    {
        $size = config('laravel-route-permission.card-size-class');

        return '<div class="'.$size.'">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">'.$title.'</div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="nested-checkbox">
                            <ul>
                                <li>
                                    <input class="form-check-input" type="checkbox" id="'.$id.'">
                                    <label class="form-check-label" for="'.$id.'">'.$title.'</label>
                                    <ul>
                                    '.$listTags.'
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }

    public function permissionCards(): string
    {
        $cards = '';

        $routeNameSplitter = config('laravel-route-permission.route-name-splitter');

        $routes = PermissibleRoutes::getRoutes();

        foreach ($routes as $key => $values){
            $permissionTitle = ucwords(str_replace('-',' ',$key));

            $liTags = '';

            foreach ($values as $route){
                $routeTitle = ucwords(str_replace($routeNameSplitter,' ',$route));

                $liTags .= $this->li($route,$routeTitle);
            }

            $cards .= $this->card($key,$permissionTitle,$liTags);
        }

        return new HtmlString($cards);
    }
}