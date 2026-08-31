# Hospital Management System (HMS)

A comprehensive hospital management system built on Laravel + Nova, covering **Outpatient**, **Inpatient**, and **Pharmacy** operations with role-based access control, billing, and a full audit trail.

---

## Modules

### Outpatient
- Patient registration with auto-generated patient numbers (`PT00001`)
- Visit/consultation tracking with status workflow (waiting → in progress → completed)
- Doctor and department management
- One-click invoice generation from a visit (consultation fee + dispensed medications)

### Inpatient
- Admissions with ward and bed assignment
- **Automatic bed occupancy management** — beds are marked occupied on admission and freed on discharge/transfer
- **Double-booking prevention** — a bed cannot be assigned to two admitted patients
- Inpatient medication administration with stock deduction and audit
- Inpatient/Outpatient status badge on patient records

### Pharmacy
- Medication catalogue with stock levels, reorder thresholds, and expiry dates
- Prescriptions with line items and bulk dispensing
- **Stock guards** — cannot dispense more than available stock or expired medication
- Automatic prescription status sync (pending → partially dispensed → dispensed)
- Full stock movement audit trail (received, dispensed, returned, adjustment)
- Receive Stock, Stock Count Adjustment (bulk-capable) actions
- Low-stock, out-of-stock, and expiry metrics & filters

### Billing
- Invoices with auto-generated numbers (`INV-00001`)
- **Line-item driven totals** — invoice totals are always the sum of items
- **Automatic status** — derived from payments (pending / partially paid / paid)
- Medical aid workflow states (submitted / rejected) preserved
- PDF invoice download

### Cross-cutting
- Referrals to external hospitals
- Role-based access control (Spatie permissions) with per-resource policies
- Audit log (Nova Action Events) exposed as a protected resource
- CSV/Excel export on every resource
- 2FA and passkey support

---

## Requirements

- PHP 8.3+
- Composer
- Node.js & npm
- MySQL 8+ / PostgreSQL 14+ (SQLite for local/testing)
- A valid Laravel Nova license

---

## Installation

```bash
# Install dependencies
composer install
npm install && npm run build

# Environment
cp .env.example .env
php artisan key:generate

# Configure your database in .env, then:
php artisan migrate --seed

# Link storage (for invoice PDFs & exports)
php artisan storage:link
```

## Seeded Accounts

| Email | Role | Access |
|-------|------|--------|
| `admin@hms.local` | admin | Full access |
| `support@hms.local` | support_staff | Dashboard + user viewing |
| `user@hms.local` | *(none)* | Restricted dashboard — prompts to contact administrator |

Default password: `password`

---

## Configuration

Billing defaults and pharmacy settings live in `config/hms.php` (overridable via `.env`):

| Env Variable | Purpose | Default |
|--------------|---------|---------|
| `HMS_CONSULTATION_FEE` | Default outpatient consultation fee | 350.00 |
| `HMS_ADMISSION_FEE` | One-off admission fee | 500.00 |
| `HMS_RATE_GENERAL` | General ward daily rate | 800.00 |
| `HMS_RATE_ICU` | ICU daily rate | 3500.00 |
| `HMS_EXPIRY_WARNING_DAYS` | Days before expiry to flag medication | 90 |

---

## Testing

```bash
php artisan test
```

Covers the critical business logic:
- **Pharmacy** — stock deduction, reversal, over-dispensing prevention, expiry blocking, prescription status sync
- **Inpatient** — bed occupancy, discharge freeing beds, double-booking prevention, bed reuse
- **Billing** — invoice number generation, line-item totals, status derivation

---

## Key Business Rules (Automated)

1. Dispensing a prescription item deducts medication stock and logs a movement; reversing it restores stock.
2. Stock can never go negative and expired medication cannot be dispensed or administered.
3. Admitting a patient occupies their bed; discharge/transfer/death frees it.
4. A bed cannot be double-booked among admitted patients.
5. Invoice totals are always the sum of line items; status follows payments.
6. Patient numbers and invoice numbers are auto-generated and sequential.

---

## Tech Stack

- **Framework:** Laravel 13
- **Admin/UI:** Laravel Nova 5
- **Auth/Permissions:** Laravel Fortify + Spatie Laravel Permission
- **PDF:** laraveldaily/laravel-invoices (dompdf)
- **Exports:** maatwebsite/excel
- **Primary keys:** UUIDs throughout
