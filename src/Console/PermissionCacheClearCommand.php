<?php

namespace RadiateCode\PermissionNameGenerator\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use RadiateCode\PermissionNameGenerator\Enums\Constant;

class PermissionCacheClearCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'permissions:cache-clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear laravel generated permissions cache';


    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        Cache::forget(Constant::CACHE_PERMISSIONS_KEY);

        Cache::forget(Constant::CACHE_ROUTES_COUNT_KEY);

        $this->info('laravel-permission-name-generator caches are cleared successfully');
    }
}