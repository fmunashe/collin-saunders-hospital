import { Page, expect } from '@playwright/test';

export const ADMIN = { email: 'admin@hms.local', password: 'password' };

/** Generate a short unique suffix so repeated runs don't collide. */
export function uniqueSuffix(): string {
    return Date.now().toString().slice(-6) + Math.floor(Math.random() * 90 + 10);
}

/**
 * Log in to Nova as the given user and wait for the dashboard.
 */
export async function login(page: Page, email = ADMIN.email, password = ADMIN.password) {
    await page.goto('/nova/login');

    // Nova renders email/password inputs; target by type for resilience.
    await page.locator('input[type="email"]').first().fill(email);
    await page.locator('input[type="password"]').first().fill(password);

    await page.getByRole('button', { name: /sign in|log in/i }).click();

    // Wait for the actual post-login landing page. Matching only "/nova" is too
    // loose (it also matches "/nova/login"); we must wait until Nova has fully
    // authenticated and redirected to a dashboard so the session cookie is
    // established for subsequent full-page requests (e.g. PDF downloads).
    await page.waitForURL(/\/nova\/dashboards\//, { timeout: 30_000 });

    // Confirm we're authenticated (login form gone).
    await expect(page.locator('input[type="password"]')).toHaveCount(0, { timeout: 15_000 });
}

/**
 * Robustly navigate within the Nova SPA.
 *
 * A hard `page.goto` to a Nova sub-route can trigger net::ERR_ABORTED because
 * Nova's client router (Inertia) intercepts the navigation. When that happens
 * the SPA often stays on the previous view (e.g. the dashboard) instead of
 * rendering the target route. To make navigation deterministic we:
 *   1. Force a full browser navigation via window.location (bypasses the SPA
 *      router entirely so the server renders the target route fresh).
 *   2. Wait for the URL to actually match the target path.
 *   3. Fall back to a hard reload if the URL didn't change.
 */
async function novaGoto(page: Page, url: string) {
    const targetPath = url.startsWith('http') ? new URL(url).pathname : url;

    try {
        // Use networkidle so the page has finished navigating before we check.
        await page.goto(url, { waitUntil: 'networkidle', timeout: 30_000 });
    } catch (e: any) {
        // Nova SPA often aborts hard navigations — swallow ERR_ABORTED only.
        if (!String(e?.message).includes('ERR_ABORTED')) throw e;
    }

    // After the goto (or abort), check if we actually arrived at the target path.
    // If not (the SPA router ate the navigation and we're still on a different
    // Nova route), force a reload via the browser address bar.
    const currentUrl = page.url();
    if (!currentUrl.includes(targetPath)) {
        // Navigate to the root first to break out of the SPA context (keeps cookies)
        await page.goto('/', { waitUntil: 'domcontentloaded' });
        // Now do a fresh full-page load to the target URL (no SPA interference)
        await page.goto(url, { waitUntil: 'networkidle', timeout: 30_000 });
    }

    await settle(page);
}

/**
 * Navigate directly to a Nova resource index page.
 */
export async function gotoResource(page: Page, uriKey: string) {
    await novaGoto(page, `/nova/resources/${uriKey}`);
}

/**
 * Open the "Create" form for a resource.
 */
export async function gotoCreate(page: Page, uriKey: string) {
    await novaGoto(page, `/nova/resources/${uriKey}/new`);
}

/**
 * Navigate to a Nova dashboard by uri key.
 */
export async function gotoDashboard(page: Page, uriKey: string) {
    await novaGoto(page, `/nova/dashboards/${uriKey}`);
}

/**
 * Wait for the Nova SPA to finish its initial render / data fetch.
 * Nova keeps long-lived connections, so networkidle is unreliable — instead we
 * wait for the loading spinner to disappear and give the view a moment.
 */
export async function settle(page: Page) {
    // Wait for any Nova loading indicator to detach, if present.
    await page.locator('[dusk="loading-view"]').waitFor({ state: 'detached', timeout: 10_000 }).catch(() => {});
    await page.waitForTimeout(1200);
}

/**
 * Fill a Nova text/number field located by its visible label.
 * Nova wraps each field; the input follows the label text.
 */
export async function fillField(page: Page, label: string, value: string) {
    // Nova field rows contain the label then the control.
    const field = page.locator('.o1-flex, [dusk$="-field"]', { hasText: label }).first();
    const input = field.locator('input:not([type="hidden"]), textarea').first();
    await input.scrollIntoViewIfNeeded();
    await input.click();
    await input.fill(value);
}

/**
 * Fill a Nova text/number/date field by its dusk attribute.
 *
 * In current Nova versions the `dusk="{attribute}"` marker sits directly on the
 * <input>/<textarea> element (e.g. dusk="first_name"), so we target it directly
 * and fall back to a nested control for older layouts.
 */
export async function fillDusk(page: Page, attribute: string, value: string) {
    const direct = page.locator(`input[dusk="${attribute}"], textarea[dusk="${attribute}"]`).first();
    const target = (await direct.count())
        ? direct
        : page.locator(`[dusk="${attribute}"] input:not([type=hidden]), [dusk="${attribute}"] textarea`).first();
    await target.scrollIntoViewIfNeeded();
    await target.fill(value);
}

/**
 * Interact with a Nova *searchable* Select field (a `Select`->`searchable()`).
 *
 * Structure discovered from the running app:
 *   [dusk="{attr}-search-input-selected"]  → the clickable trigger ("Click to choose")
 *   [dusk="{attr}-search-input-dropdown"]  → the opened dropdown panel
 *   [dusk="{attr}-search-input-result-N"]  → each selectable option row
 * Some selects also expose a text input to filter; we type into it when present.
 */
export async function selectSearchable(page: Page, attribute: string, optionText: string) {
    const trigger = page.locator(`[dusk="${attribute}-search-input-selected"]`).first();
    await trigger.scrollIntoViewIfNeeded();
    await trigger.click();

    // Wait for the dropdown to open.
    const dropdown = page.locator(`[dusk="${attribute}-search-input-dropdown"]`).first();
    await dropdown.waitFor({ state: 'visible', timeout: 8_000 }).catch(() => {});

    // If a filter input is present inside the dropdown, type to narrow results.
    const filter = dropdown.locator('input').first();
    if (await filter.count()) {
        await filter.fill(optionText).catch(() => {});
        await page.waitForTimeout(300);
    }

    // Click the option row whose text matches.
    const option = page
        .locator(`[dusk^="${attribute}-search-input-result-"]`)
        .filter({ hasText: new RegExp(`^\\s*${escapeRegex(optionText)}\\s*$`, 'i') })
        .first();

    if (await option.count()) {
        await option.click();
    } else {
        // Fallback: any result row containing the text.
        await page
            .locator(`[dusk^="${attribute}-search-input-result-"]`)
            .filter({ hasText: new RegExp(escapeRegex(optionText), 'i') })
            .first()
            .click();
    }
}

function escapeRegex(s: string): string {
    return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

/**
 * Click the primary "Create & ..." / "Update ..." submit button on a Nova form.
 */
export async function submitForm(page: Page) {
    await page.getByRole('button', { name: /create|update|save/i }).last().click();
}

/**
 * Assert a success toast / that we landed on a detail or index page.
 */
export async function expectSaved(page: Page) {
    // Nova shows a toast and redirects; wait for either.
    await expect(page).toHaveURL(/\/nova\/resources\//, { timeout: 20_000 });
}

/**
 * Select all rows on the current Nova index page.
 * Uses the select-all dropdown → "Select this page" button.
 */
export async function selectAllRows(page: Page) {
    await page.locator('[dusk="select-all-dropdown-trigger"]').click();
    await page.locator('[dusk="select-all-button"]').waitFor({ state: 'visible', timeout: 8_000 });
    await page.locator('[dusk="select-all-button"]').click();
    await page.waitForTimeout(300);
}

/**
 * Choose a resource action from the `action-select` dropdown by its label,
 * which opens the action's confirmation modal.
 */
export async function chooseAction(page: Page, label: string) {
    const select = page.locator('[dusk="action-select"]').first();
    await select.scrollIntoViewIfNeeded();
    // selectOption by visible label triggers Nova's action modal.
    await select.selectOption({ label });
    await page.waitForTimeout(600);
}

/**
 * Confirm/run the currently-open Nova action modal.
 */
export async function confirmActionModal(page: Page) {
    const modal = page.locator('[dusk="confirm-action-modal"], [role="dialog"]').first();
    await modal.waitFor({ state: 'visible', timeout: 8_000 }).catch(() => {});
    await page.locator('[dusk="confirm-action-button"]').first().click()
        .catch(async () => {
            await modal.getByRole('button', { name: /run action|confirm|receive|adjust|save/i }).last().click();
        });
}

/**
 * Select an option in a Nova *searchable* BelongsTo relation field.
 *
 * These use the plural resource uri key as their dusk prefix, e.g.
 *   [dusk="patients-search-input-selected"]         → trigger
 *   [dusk="patients-search-input-result-N"]         → result rows
 * @param pluralKey e.g. "patients", "doctors"
 * @param query     text to type (e.g. "PT" or a name) to filter results
 */
export async function pickBelongsTo(page: Page, pluralKey: string, query: string) {
    const trigger = page.locator(`[dusk="${pluralKey}-search-input-selected"]`).first();
    await trigger.scrollIntoViewIfNeeded();
    await trigger.click();

    // Type into the dropdown's search box to trigger the async lookup.
    const searchBox = page.locator('input[placeholder="Search"]:visible').last();
    await searchBox.waitFor({ state: 'visible', timeout: 8_000 }).catch(() => {});
    if (await searchBox.count()) {
        await searchBox.fill(query);
        await page.waitForTimeout(1000);
    }

    await page.locator(`[dusk^="${pluralKey}-search-input-result-"]`).first()
        .click({ timeout: 8_000 });
}

/**
 * Select an option in a plain (non-searchable) Nova BelongsTo / Select rendered
 * as a native <select>, e.g. [dusk="departments-select"].
 */
export async function selectNative(page: Page, dusk: string, index = 1) {
    const select = page.locator(`[dusk="${dusk}"]`).first();
    await select.scrollIntoViewIfNeeded();
    // Pick the option at `index` (0 is usually the "Choose" placeholder).
    const values = await select.locator('option').evaluateAll((els) =>
        els.map((e) => (e as HTMLOptionElement).value).filter((v) => v !== '')
    );
    if (values.length) {
        await select.selectOption(values[Math.min(index - 1, values.length - 1)]);
    }
}
