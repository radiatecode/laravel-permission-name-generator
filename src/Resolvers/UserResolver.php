<?php


namespace RadiateCode\LaravelRoutePermission\Resolvers;


use Illuminate\Support\Facades\Auth;

class UserResolver implements \RadiateCode\LaravelRoutePermission\Contracts\UserResolver
{
    /**
     * @inheritdoc
     *
     * @return mixed|null
     */
    public static function resolve()
    {
        $guards = config('route-permission.user.guards', ['web', 'api']);

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return Auth::guard($guard)->user();
            }
        }
    }

    /**
     * @return string
     */
    public static function user_model(): string
    {
        return get_class(self::resolve());
    }
}