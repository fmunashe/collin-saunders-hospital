import { execSync } from 'node:child_process';
import { existsSync, writeFileSync, rmSync } from 'node:fs';
import path from 'node:path';

/**
 * Prepares a clean, seeded SQLite database for the E2E run.
 *
 * Runs once before all tests:
 *  1. Ensures an app key exists for the e2e environment
 *  2. Recreates the disposable SQLite database file
 *  3. Runs migrations + seeders so the UI has data to work with
 */
export default async function globalSetup() {
    const root = process.cwd();
    const dbPath = path.join(root, 'database', 'e2e.sqlite');
    const run = (cmd: string) =>
        execSync(cmd, { cwd: root, stdio: 'inherit', env: { ...process.env, APP_ENV: 'e2e' } });

    console.log('\n[e2e] Preparing test database...');

    // Fresh database file
    if (existsSync(dbPath)) {
        rmSync(dbPath);
    }
    writeFileSync(dbPath, '');

    // App key (ignore failure if already set)
    try {
        run('php artisan key:generate --env=e2e --force');
    } catch {
        /* key may already be present */
    }

    // Clear cached config so the e2e env is picked up
    run('php artisan config:clear');

    // Migrate + seed the disposable database
    run('php artisan migrate:fresh --seed --env=e2e --force');

    console.log('[e2e] Test database ready.\n');
}
