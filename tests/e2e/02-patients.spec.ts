import { test, expect } from '@playwright/test';
import { login, gotoResource, gotoCreate, submitForm, uniqueSuffix, fillDusk, selectSearchable } from './helpers';

test.describe('Patient management', () => {
    test.beforeEach(async ({ page }) => {
        await login(page);
    });

    test('lists patients', async ({ page }) => {
        await gotoResource(page, 'patients');
        await expect(page.locator('body')).toContainText(/Patient/i);
        // Seeded patients start with PT
        await expect(page.locator('body')).toContainText(/PT\d{5}/);
    });

    test('registers a new patient via the create form', async ({ page }) => {
        const suffix = uniqueSuffix();
        await gotoCreate(page, 'patients');

        // First & last name — dusk marker sits directly on the input element.
        await fillDusk(page, 'first_name', 'Playwright');
        await fillDusk(page, 'last_name', `Tester${suffix}`);

        // Date of birth
        await fillDusk(page, 'date_of_birth', '1990-05-15');

        // Phone
        await fillDusk(page, 'phone', `+2772${suffix}`);

        // Gender / Patient Type / Billing Type are searchable selects — pick options
        await selectSearchable(page, 'gender', 'Male');
        await selectSearchable(page, 'patient_type', 'Non staff');
        await selectSearchable(page, 'billing_type', 'Cash');

        await submitForm(page);

        // We should be redirected to the new patient's detail page
        await expect(page).toHaveURL(/\/nova\/resources\/patients\//, { timeout: 20_000 });
        await expect(page.locator('body')).toContainText(`Tester${suffix}`);
        // Patient number auto-generated
        await expect(page.locator('body')).toContainText(/PT\d{5}/);
    });

    test('searches for a patient', async ({ page }) => {
        await gotoResource(page, 'patients');
        const search = page.locator('input[type="search"], input[placeholder*="Search" i]').first();
        await search.fill('PT00001');
        await page.waitForTimeout(1500);
        await expect(page.locator('body')).toContainText(/PT00001/);
    });
});
