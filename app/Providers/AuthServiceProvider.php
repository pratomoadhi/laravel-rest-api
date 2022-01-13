<?php

namespace App\Providers;

use App\Models\Product;
use App\Policies\ProductPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        Product::class => ProductPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::routes();
   
        // /* define a admin user role */
        // Gate::define('isAdmin', function($user) {
        //     return $user->role == 'admin';
        // });
        
        // /* define a manager user role */
        // Gate::define('isManager', function($user) {
        //     return $user->role == 'manager';
        // });
       
        // /* define a user role */
        // Gate::define('isUser', function($user) {
        //     return $user->role == 'user';
        // });
    }
}
