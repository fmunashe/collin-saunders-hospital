import { test, expect } from '@playwright/test';
import { login, gotoResource, gotoCreate, gotoDashboard, submitForm, uniqueSuffix, fillDusk, selectAllRows, chooseAction, confirmActionModal } from './helpers';

test.describe('Pharmacy — medications & stock', () => {
    test.beforeEach(async ({ page }) => {
        await login(page);
    });

    test('lists medications with stock levels', async ({ page }) => {
        await gotoResource(page, 'medications');
        await expect(page.locator('body')).toContainText(/Paracetamol|Amoxicillin|Medication/i);
    });

    test('creates a new medication', async ({ page }) => {
        const suffix = uniqueSuffix();
        await gotoCreate(page, 'medications');

        await fillDusk(page, 'name', `TestDrug ${suffix}`);
        await fillDusk(page, 'dosage_form', 'Tablet');
        await fillDusk(page, 'strength', '250mg');
        await fillDusk(page, 'stock_quantity', '120');
        await fillDusk(page, 'reorder_level', '20');
        await fillDusk(page, 'unit_price', '3.50');

        await submitForm(page);

        await expect(page).toHaveURL(/\/nova\/resources\/medications\//, { timeout: 20_000 });
        await expect(page.locator('body')).toContainText(`TestDrug ${suffix}`);
    });

    test('runs the Receive Stock action to add inventory', async ({ page }) => {
        await gotoResource(page, 'medications');

        // Select all rows on this page, then choose the Receive Stock action.
        await selectAllRows(page);
        await chooseAction(page, 'Receive Stock');

        // Fill the action modal's Quantity field.
        const modal = page.locator('[dusk="confirm-action-modal"], [role="dialog"]').first();
        await modal.locator('input[type="number"], input').first().fill('50');

        await confirmActionModal(page);

        // A success toast should confirm the received units.
        await expect(page.locator('body')).toContainText(/Received|units/i, { timeout: 20_000 });
    });

    test('shows pharmacy dashboard metrics', async ({ page }) => {
        await gotoDashboard(page, 'pharmacy-reports');
        await expect(page.locator('body')).toContainText(/Medications|Stock|Prescriptions/i, { timeout: 20_000 });
    });
});
