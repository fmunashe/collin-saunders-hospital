<?php

namespace App\Providers;

use App\Nova\ActionEvent;
use App\Nova\Admission;
use App\Nova\AdmissionNote;
use App\Nova\Bed;
use App\Nova\Dashboards\FinancialReports;
use App\Nova\Dashboards\InpatientReports;
use App\Nova\Dashboards\Main;
use App\Nova\Dashboards\OutpatientReports;
use App\Nova\Dashboards\PatientReports;
use App\Nova\Dashboards\PharmacyReports;
use App\Nova\Dashboards\ReferralReports;
use App\Nova\Dashboards\StaffReports;
use App\Nova\Doctor;
use App\Nova\Invoice;
use App\Nova\MedicalAidScheme;
use App\Nova\Medication;
use App\Nova\MedicationAdministration;
use App\Nova\Patient;
use App\Nova\Permission;
use App\Nova\Prescription;
use App\Nova\Referral;
use App\Nova\Role;
use App\Nova\Staff;
use App\Nova\StockMovement;
use App\Nova\User;
use App\Nova\Visit;
use App\Nova\Ward;
use App\SupportPage\SupportPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Laravel\Fortify\Features;
use Laravel\Nova\Dashboard;
use Laravel\Nova\Menu\MenuGroup;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Laravel\Nova\Tool;

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
                © '.now()->year.'
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
                    ]),
                ])->icon('cog')->collapsable(),

                MenuSection::make('Patients', [
                    MenuGroup::make('Patients', [
                        MenuItem::resource(Patient::class)->icon('identification'),
                        MenuItem::resource(Visit::class)->icon('calendar-days'),
                        MenuItem::resource(Admission::class)->icon('building-office'),
                        MenuItem::resource(AdmissionNote::class)->icon('pencil-square'),
                        MenuItem::resource(Referral::class)->icon('arrow-top-right-on-square'),
                        MenuItem::resource(Invoice::class)->icon('banknotes'),
                        MenuItem::resource(MedicationAdministration::class)->icon('light-bulb'),
                    ]),
                ])->icon('identification')->collapsable(),

                MenuSection::make('Pharmacy', [
                    MenuGroup::make('Invoice', [
                        MenuItem::resource(Invoice::class)->icon('banknotes'),
                        MenuItem::resource(Medication::class)->icon('sparkles'),
                        MenuItem::resource(Prescription::class)->icon('eye-dropper'),
                        MenuItem::resource(MedicalAidScheme::class)->icon('bolt'),
                        MenuItem::resource(StockMovement::class)->icon('scale'),
                    ]),
                ])->icon('beaker')->collapsable(),

                MenuSection::make('Reports', [
                    MenuGroup::make('View Reports', [
                        MenuItem::dashboard(PatientReports::class)->name('Patient')
                            ->canSee(fn ($r) => $r->user()?->can('view-patient-reports'))->icon('identification'),
                        MenuItem::dashboard(OutpatientReports::class)->name('Outpatient')
                            ->canSee(fn ($r) => $r->user()?->can('view-outpatient-reports'))->icon('circle-stack'),
                        MenuItem::dashboard(InpatientReports::class)->name('Inpatient')
                            ->canSee(fn ($r) => $r->user()?->can('view-inpatient-reports'))->icon('table-cells'),
                        MenuItem::dashboard(PharmacyReports::class)->name('Pharmacy')
                            ->canSee(fn ($r) => $r->user()?->can('view-pharmacy-reports'))->icon('beaker'),
                        MenuItem::dashboard(FinancialReports::class)->name('Financial')
                            ->canSee(fn ($r) => $r->user()?->can('view-financial-reports'))->icon('banknotes'),
                        MenuItem::dashboard(ReferralReports::class)->name('Referral')
                            ->canSee(fn ($r) => $r->user()?->can('view-referral-reports'))->icon('light-bulb'),
                        MenuItem::dashboard(StaffReports::class)->name('Staff')
                            ->canSee(fn ($r) => $r->user()?->can('view-staff-reports'))->icon('user-group'),
                    ])->collapsedByDefault(),
                    MenuGroup::make('Download PDFs', [
                        MenuItem::externalLink('Patient', url('/nova/reports/patient-reports/pdf'))->openInNewTab()
                            ->canSee(fn ($r) => $r->user()?->can('view-patient-reports'))->icon('document-arrow-down'),
                        MenuItem::externalLink('Outpatient', url('/nova/reports/outpatient-reports/pdf'))->openInNewTab()
                            ->canSee(fn ($r) => $r->user()?->can('view-outpatient-reports'))->icon('document-arrow-down'),
                        MenuItem::externalLink('Inpatient', url('/nova/reports/inpatient-reports/pdf'))->openInNewTab()
                            ->canSee(fn ($r) => $r->user()?->can('view-inpatient-reports'))->icon('document-arrow-down'),
                        MenuItem::externalLink('Pharmacy', url('/nova/reports/pharmacy-reports/pdf'))->openInNewTab()
                            ->canSee(fn ($r) => $r->user()?->can('view-pharmacy-reports'))->icon('document-arrow-down'),
                        MenuItem::externalLink('Financial', url('/nova/reports/financial-reports/pdf'))->openInNewTab()
                            ->canSee(fn ($r) => $r->user()?->can('view-financial-reports'))->icon('document-arrow-down'),
                        MenuItem::externalLink('Referral', url('/nova/reports/referral-reports/pdf'))->openInNewTab()
                            ->canSee(fn ($r) => $r->user()?->can('view-referral-reports'))->icon('document-arrow-down'),
                        MenuItem::externalLink('Staff', url('/nova/reports/staff-reports/pdf'))->openInNewTab()
                            ->canSee(fn ($r) => $r->user()?->can('view-staff-reports'))->icon('document-arrow-down'),
                    ])->collapsable()->collapsedByDefault(),
                ])->icon('chart-bar')->collapsable(),
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
     * @return array<int, Dashboard>
     */
    protected function dashboards(): array
    {
        return [
            new Main,
            new PatientReports,
            new OutpatientReports,
            new InpatientReports,
            new PharmacyReports,
            new FinancialReports,
            new ReferralReports,
            new StaffReports,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array<int, Tool>
     */
    public function tools(): array
    {
        return [
            new SupportPage,
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
