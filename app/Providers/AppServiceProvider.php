<?php

namespace App\Providers;

use App\Models\Setting;
use App\Policies\ActionEventPolicy;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Actions\ActionEvent;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(ActionEvent::class, ActionEventPolicy::class);

        $this->applyDatabaseSettings();
    }

    /**
     * Overlay database-backed settings on top of the file config, so existing
     * config('hms.*') reads transparently use admin-editable DB values.
     *
     * Settings are stored with dot keys (e.g. "hms.billing.consultation_fee")
     * and simply set into the config repository, overriding the file defaults.
     * Wrapped defensively so the app still boots before the migration/seed runs.
     */
    private function applyDatabaseSettings(): void
    {
        try {
            if (! Schema::hasTable('settings')) {
                return;
            }

            foreach (Setting::allKeyed() as $key => $value) {
                Config::set($key, $value);
            }
        } catch (Throwable) {
            // During install/migrate the table may not exist yet — fall back to file config.
        }
    }
}
