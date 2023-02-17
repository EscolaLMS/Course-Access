<?php

namespace EscolaLms\CourseAccess;

use EscolaLms\CourseAccess\Services\Contracts\CourseAccessServiceContract;
use EscolaLms\CourseAccess\Services\CourseAccessService;
use Illuminate\Support\ServiceProvider;

/**
 * SWAGGER_VERSION
 */
class EscolaLmsCourseAccessServiceProvider extends ServiceProvider
{
    public const SERVICES = [
        CourseAccessServiceContract::class => CourseAccessService::class,
    ];

    public const REPOSITORIES = [];

    public $singletons = self::SERVICES + self::REPOSITORIES;

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    public function register()
    {
    }
}
