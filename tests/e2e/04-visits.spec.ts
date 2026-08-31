import { test, expect } from '@playwright/test';
import { login, gotoResource, gotoCreate, gotoDashboard, submitForm, pickBelongsTo, selectNative, fillDusk } from './helpers';

test.describe('Outpatient — visits', () => {
    test.beforeEach(async ({ page }) => {
        await login(page);
    });

    test('lists visits', async ({ page }) => {
        await gotoResource(page, 'visits');
        await expect(page.locator('body')).toContainText(/Visit|Patient|Doctor/i);
    });

    test('creates a visit by selecting patient and doctor', async ({ page }) => {
        await gotoCreate(page, 'visits');

        // Searchable BelongsTo fields (dusk uses the plural resource key).
        await pickBelongsTo(page, 'patients', 'PT');
        await pickBelongsTo(page, 'doctors', 'Dr');

        // Department is a plain native <select>.
        await selectNative(page, 'departments-select');

        // Visit date/time.
        await fillDusk(page, 'visit_date', '2026-08-31T09:00');

        // Complaint free-text (dusk marker on the textarea).
        await fillDusk(page, 'complaint', 'Headache and mild fever, E2E test visit.');

        await submitForm(page);
        await expect(page).toHaveURL(/\/nova\/resources\/visits\//, { timeout: 20_000 });
    });

    test('opens the outpatient reports dashboard', async ({ page }) => {
        await gotoDashboard(page, 'outpatient-reports');
        await expect(page.locator('body')).toContainText(/Visits/i, { timeout: 20_000 });
    });
});
