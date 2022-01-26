<?php


namespace RadiateCode\LaravelRoutePermission\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RadiateCode\LaravelRoutePermission\Resolvers\UserResolver;

class Role extends Model
{
    public function creator(): BelongsTo
    {
        return $this->belongsTo(UserResolver::user_model(),'creator_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(UserResolver::user_model(),'updated_by');
    }
}