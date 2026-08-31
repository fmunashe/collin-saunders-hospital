import { test, expect } from '@playwright/test';
import { login, gotoResource, gotoDashboard } from './helpers';

test.describe('Inpatient — admissions & beds', () => {
    test.beforeEach(async ({ page }) => {
        await login(page);
    });

    test('lists admissions', async ({ page }) => {
        await gotoResource(page, 'admissions');
        await expect(page.locator('body')).toContainText(/Admission|Ward|Patient/i);
    });

    test('lists beds with status', async ({ page }) => {
        await gotoResource(page, 'beds');
        await expect(page.locator('body')).toContainText(/Available|Occupied|Bed/i);
    });

    test('shows an occupied bed for current inpatients', async ({ page }) => {
        await gotoResource(page, 'beds');
        // Seeder admits 5 patients, so at least one bed is occupied
        await expect(page.locator('body')).toContainText(/Occupied/i);
    });

    test('opens the inpatient reports dashboard', async ({ page }) => {
        await gotoDashboard(page, 'inpatient-reports');
        await expect(page.locator('body')).toContainText(/Inpatients|Admissions|Occupancy|Stay/i, { timeout: 20_000 });
    });
});
