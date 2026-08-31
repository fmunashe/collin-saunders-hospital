import { test, expect } from '@playwright/test';
import { login, gotoResource, gotoDashboard, selectAllRows } from './helpers';

test.describe('Billing — invoices', () => {
    test.beforeEach(async ({ page }) => {
        await login(page);
    });

    test('lists invoices with auto-generated numbers', async ({ page }) => {
        await gotoResource(page, 'invoices');
        await expect(page.locator('body')).toContainText(/INV-\d{5}/);
    });

    test('opens an invoice detail and shows items + total', async ({ page }) => {
        await gotoResource(page, 'invoices');
        // Open the first row's "view" (detail) via its row control.
        const viewLink = page.locator('tbody tr a[href*="/nova/resources/invoices/"]').first();
        await viewLink.click();
        await expect(page).toHaveURL(/\/nova\/resources\/invoices\/[^/]+$/, { timeout: 20_000 });
        await expect(page.locator('body')).toContainText(/Total|Amount|Items|Balance|Status/i);
    });

    test('exposes the Download Invoice action', async ({ page }) => {
        await gotoResource(page, 'invoices');
        await selectAllRows(page);

        // The action-select dropdown should offer "Download Invoice".
        const options = await page.locator('[dusk="action-select"]').first()
            .locator('option').evaluateAll((els) => els.map((e) => e.textContent?.trim()));
        expect(options.join(' ')).toMatch(/Download Invoice/i);
    });

    test('opens the financial reports dashboard', async ({ page }) => {
        await gotoDashboard(page, 'financial-reports');
        await expect(page.locator('body')).toContainText(/Invoiced|Collected|Revenue|Outstanding/i, { timeout: 20_000 });
    });
});
