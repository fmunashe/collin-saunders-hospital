import { defineConfig, devices } from '@playwright/test';

/**
 * Playwright E2E configuration for the HMS (Laravel Nova) application.
 *
 * The webServer block boots a dedicated Laravel server on port 8123 using the
 * `.env.e2e` environment (a disposable SQLite database that is migrated and
 * seeded before the suite runs — see tests/e2e/global-setup.ts).
 */
export default defineConfig({
    testDir: './tests/e2e',
    globalSetup: './tests/e2e/global-setup.ts',
    fullyParallel: false,
    workers: 1,
    retries: 0,
    timeout: 60_000,
    expect: { timeout: 15_000 },

    reporter: [
        ['list'],
        ['html', { outputFolder: 'playwright-report', open: 'never' }],
    ],

    use: {
        baseURL: 'http://127.0.0.1:8123',
        headless: true, // set to false to watch the browser drive the UI
        viewport: { width: 1440, height: 900 },
        actionTimeout: 15_000,
        navigationTimeout: 30_000,
        screenshot: 'only-on-failure',
        video: 'retain-on-failure',
        trace: 'retain-on-failure',
    },

    projects: [
        {
            name: 'chromium',
            use: { ...devices['Desktop Chrome'] },
        },
    ],

    webServer: {
        command: 'php artisan serve --env=e2e --host=127.0.0.1 --port=8123',
        url: 'http://127.0.0.1:8123',
        reuseExistingServer: false,
        timeout: 120_000,
        env: {
            APP_ENV: 'e2e',
        },
    },
});
