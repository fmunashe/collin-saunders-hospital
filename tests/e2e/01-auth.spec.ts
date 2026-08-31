import { test, expect } from '@playwright/test';
import { ADMIN, login } from './helpers';

test.describe('Authentication', () => {
    test('shows the Nova login page', async ({ page }) => {
        await page.goto('/nova/login');
        await expect(page.locator('input[type="email"]').first()).toBeVisible();
        await expect(page.locator('input[type="password"]').first()).toBeVisible();
    });

    test('rejects invalid credentials', async ({ page }) => {
        await page.goto('/nova/login');
        await page.locator('input[type="email"]').first().fill('wrong@hms.local');
        await page.locator('input[type="password"]').first().fill('badpassword');
        await page.getByRole('button', { name: /sign in|log in/i }).click();

        // Should remain on the login page
        await expect(page).toHaveURL(/login/, { timeout: 15_000 });
        await expect(page.locator('input[type="email"]').first()).toBeVisible();
    });

    test('logs in as admin and reaches the dashboard', async ({ page }) => {
        await login(page, ADMIN.email, ADMIN.password);
        await expect(page).toHaveURL(/\/nova/);
        // The main dashboard shows hospital metrics
        await expect(page.locator('body')).toContainText(/Patients|Dashboard|Visits/i, { timeout: 20_000 });
    });
});
