<?php

namespace App\Providers;

use App\Nova\ActionEvent;
use App\Nova\Admission;
use App\Nova\Bed;
use App\Nova\Dashboards\Main;
use App\Nova\Doctor;
use App\Nova\Invoice;
use App\Nova\MedicalAidScheme;
use App\Nova\Medication;
use App\Nova\MedicationAdministration;
use App\Nova\Patient;
use App\Nova\Permission;
use App\Nova\Prescription;
use App\Nova\Role;
use App\Nova\Staff;
use App\Nova\StockMovement;
use App\Nova\User;
use App\Nova\Visit;
use App\Nova\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Laravel\Fortify\Features;
use Laravel\Nova\Menu\MenuGroup;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();
        Nova::withBreadcrumbs();

        Nova::style('nova-custom', resource_path('css/nova.css'));

        Nova::footer(function (Request $request) {
            return Blade::render('
             <div style="
            width: 100%;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            line-height: 1.6;
        ">
            <div>
                Collin Saunders Hospital
            </div>

            <div>
                © ' . now()->year . '
            </div>
        </div>
            ');
        });
        //

        Nova::mainMenu(function (Request $request) {
            $user = $request->user();

            if ($user && $user->roles->isEmpty()) {
                return [
                    MenuSection::dashboard(Main::class)->icon('home'),
                ];
            }

            return [
                MenuSection::dashboard(Main::class)->icon('home'),

                MenuSection::make('User Management', [
                    MenuGroup::make('Users', [
                        MenuItem::resource(User::class)->icon('users'),
                        MenuItem::resource(Staff::class)->icon('user-circle'),
                        MenuItem::resource(Doctor::class)->icon('user-plus'),
                    ]),
                    MenuGroup::make('Roles & Permissions', [
                        MenuItem::resource(Role::class)->icon('shield-check'),
                        MenuItem::resource(Permission::class)->icon('key'),
                    ]),
                    MenuGroup::make('Audit', [
                        MenuItem::resource(ActionEvent::class)->icon('clipboard-document-list'),
                    ]),
                ])->icon('user-group')->collapsable(),

                MenuSection::make('Configuration', [
                    MenuGroup::make('Configs', [
                        MenuItem::resource(Ward::class)->icon('wrench-screwdriver'),
                        MenuItem::resource(Bed::class)->icon('wrench'),
                    ])
                ])->icon('cog')->collapsable(),

                MenuSection::make('Patients', [
                    MenuGroup::make('Patients', [
                        MenuItem::resource(Patient::class)->icon('identification'),
                        MenuItem::resource(Visit::class)->icon('calendar-days'),
                        MenuItem::resource(Admission::class)->icon('building-office'),
                        MenuItem::resource(Invoice::class)->icon('banknotes'),
                        MenuItem::resource(MedicationAdministration::class)->icon('light-bulb'),
                    ])
                ])->icon('identification')->collapsable(),

                MenuSection::make('Pharmacy', [
                    MenuGroup::make('Invoice', [
                        MenuItem::resource(Invoice::class)->icon('banknotes'),
                        MenuItem::resource(Medication::class)->icon('sparkles'),
                        MenuItem::resource(Prescription::class)->icon('eye-dropper'),
                        MenuItem::resource(MedicalAidScheme::class)->icon('bolt'),
                        MenuItem::resource(StockMovement::class)->icon('scale'),
                    ])
                ])->icon('beaker')->collapsable(),
            ];
        });
    }


    /**
     * Register the configurations for Laravel Fortify.
     */
    protected function fortify(): void
    {
        Nova::fortify()
            ->features([
                Features::updatePasswords(),
                // Features::emailVerification(),
                // Features::twoFactorAuthentication(['confirm' => true, 'confirmPassword' => true]),
                // Features::passkeys(),
            ])
            ->register();
    }

    /**
     * Register the Nova routes.
     */
    protected function routes(): void
    {
        Nova::routes()
            ->withAuthenticationRoutes(default: true)
            ->withPasswordResetRoutes()
            ->withoutEmailVerificationRoutes()
            ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewNova', function ($user) {
            return true;
        });
    }

    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array<int, \Laravel\Nova\Dashboard>
     */
    protected function dashboards(): array
    {
        return [
            new \App\Nova\Dashboards\Main,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array<int, \Laravel\Nova\Tool>
     */
    public function tools(): array
    {
        return [
            new \App\SupportPage\SupportPage,
        ];
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        parent::register();

        //
    }
}
