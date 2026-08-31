import { test, expect } from '@playwright/test';
import { login, gotoDashboard } from './helpers';

const REPORTS = [
    { key: 'patient-reports', text: /Patients/i },
    { key: 'outpatient-reports', text: /Visits/i },
    { key: 'inpatient-reports', text: /Admissions|Inpatients|Occupancy/i },
    { key: 'pharmacy-reports', text: /Medications|Stock/i },
    { key: 'financial-reports', text: /Revenue|Invoiced/i },
    { key: 'referral-reports', text: /Referrals/i },
    { key: 'staff-reports', text: /Staff/i },
];

test.describe('Reports — dashboards & PDF export', () => {
    test.beforeEach(async ({ page }) => {
        await login(page);
    });

    for (const report of REPORTS) {
        test(`renders the ${report.key} dashboard`, async ({ page }) => {
            await gotoDashboard(page, report.key);
            await expect(page.locator('body')).toContainText(report.text, { timeout: 20_000 });
        });
    }

    test('downloads a report PDF', async ({ page }) => {
        // Use the /reports/... twin route which lives OUTSIDE the Nova SPA
        // namespace, so the browser performs a genuine navigation/download that
        // Nova's Inertia router won't intercept.
        const downloadPromise = page.waitForEvent('download', { timeout: 30_000 });
        await page.goto('/reports/financial-reports/pdf').catch((e) => {
            // Chromium reports the navigation as aborted / "Download is starting"
            // once it turns into a download — both are expected here.
            const msg = String(e?.message);
            if (!msg.includes('ERR_ABORTED') && !msg.includes('Download is starting')) throw e;
        });

        const download = await downloadPromise;
        const filename = download.suggestedFilename();
        expect(filename).toMatch(/financial_reports_.*\.pdf/);

        // Confirm a real file landed on disk.
        const path = await download.path();
        expect(path).toBeTruthy();
    });
});
