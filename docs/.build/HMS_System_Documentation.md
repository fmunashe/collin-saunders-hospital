# Hospital Management System (HMS)
## End-to-End System Documentation

**Prepared by:** AnalyticsHive
**Client:** Collin Saunders Hospital
**Document type:** System Documentation & User Journey Reference

---

## 1. Introduction

The Hospital Management System (HMS) is a web-based platform that manages the full clinical and administrative lifecycle of a patient across three core modules — **Outpatient**, **Inpatient**, and **Pharmacy** — together with billing, referrals, reporting, and administration.

This document describes the current system end-to-end and walks through a complete patient journey, case by case, from first registration through to discharge and invoicing.

### 1.1 Purpose

- Provide a single reference describing what the system does today.
- Document the full user journey so every role understands their part in the flow.
- Serve as the baseline for training, support, and future enhancement.

### 1.2 Technology at a glance

| Layer | Technology |
|-------|-----------|
| Framework | Laravel (PHP) |
| Admin interface | Laravel Nova |
| Database | MySQL / PostgreSQL (UUID primary keys) |
| Cache / Queue / Session | Redis + Laravel Horizon |
| Authentication | Laravel Fortify (optional 2FA / passkeys) |
| Authorisation | Role- and permission-based (spatie/laravel-permission) |
| Currency | USD across the entire system |

---

## 2. Roles and Access

Access is controlled by roles, each granted a specific set of permissions. A user with no role sees a restricted dashboard prompting them to contact an administrator.

| Role | Typical responsibilities in HMS |
|------|--------------------------------|
| Administrator | Full access — all modules, configuration, users, audit log, reports |
| Doctor | Patients, visits, admissions, prescriptions, referrals, selected reports |
| Nurse | Admissions, admission (care) notes, medication administration, bed updates |
| Pharmacist | Medications, stock, prescription dispensing, pharmacy reports |
| Receptionist | Patient registration, visits, medical-aid details |
| Billing | Invoices, invoice items, financial reports |
| Support Staff | Read-only view of configuration resources |

Every create, update, and delete action is checked against the acting user's permissions **and** business-rule policies (for example, a discharged patient's records are locked).

---

## 3. Modules Overview

### 3.1 Outpatient
Patient registration, visits (consultations) linked to a doctor and department, and referrals to other facilities.

### 3.2 Inpatient
Admissions with automated bed occupancy, ward gender controls, daily clinical care notes, and medication administration.

### 3.3 Pharmacy
Medication catalogue with stock levels, expiry tracking, prescriptions, item-level dispensing, and a full stock-movement audit trail.

### 3.4 Cross-cutting
Billing and invoicing (with PDF invoices), medical-aid schemes, role-based access, seven reporting dashboards with PDF export, a full audit log, and admin-editable system settings.

---

## 4. Core Entities

| Entity | Description | Key identifiers |
|--------|-------------|-----------------|
| Patient | A person receiving care | Auto-generated patient number `PTxxxxx` |
| Visit | An outpatient consultation | Linked to patient, doctor, department |
| Admission | An inpatient stay | Linked to patient, ward, bed, doctor |
| Admission Note | A dated clinical note during a stay | Doctor / Nurse / Observation / Procedure / General |
| Medication | A pharmacy stock item | Stock quantity, reorder level, expiry |
| Prescription | A set of prescribed medications | Status: pending → partially dispensed → dispensed |
| Prescription Item | A single medication line on a prescription | Dispensed flag |
| Medication Administration | A recorded dose given to an inpatient | Route, dosage, status |
| Invoice | A bill for services / items | Auto number `INV-xxxxx`, status derived from payments |
| Referral | A referral to another facility | Priority and status |
| Stock Movement | An append-only inventory ledger entry | Received / Dispensed / Returned / Adjusted |

---

## 5. The Complete Patient Journey

This section follows a patient through the system, case by case. Each step names the responsible role, the action taken, and what the system does automatically.

### Step 1 — Patient Registration (Reception)

**Who:** Receptionist (or Administrator)
**Where:** Patients → Create

The receptionist registers the patient with name, date of birth, gender, contact details, patient type (Staff / Non-staff), and billing type (Cash / Medical Aid).

**System actions**
- A unique **patient number** (`PT00001`, `PT00002`, …) is generated automatically.
- The patient's **Care Status** badge shows **Outpatient** until they are admitted.
- If billing type is Medical Aid, a Medical Aid Detail record can be attached (scheme + membership).

### Step 2 — Outpatient Visit (Reception / Doctor)

**Who:** Receptionist creates the visit; Doctor conducts it
**Where:** Visits → Create

A visit is opened for the patient, linked to the attending **doctor** and **department**, with the presenting complaint recorded. The visit status moves through **Waiting → In Progress → Completed** (or Cancelled).

**System actions**
- The visit is timestamped and searchable by patient number and name.
- Filters allow the queue to be viewed by department, doctor, or status.

### Step 3 — Prescription (Doctor)

**Who:** Doctor
**Where:** Prescriptions → Create (linked to the visit, or to an admission for inpatients)

The doctor creates a prescription for the patient and adds one or more **prescription items** (medication, dosage, quantity, instructions).

**System actions**
- The prescription starts with status **Pending**.
- The patient and visit pickers show the patient number **and** name for easy selection.
- For inpatients, a prescription may be linked to the active admission; the system prevents prescribing against a discharged admission.

### Step 4 — Dispensing (Pharmacy)

**Who:** Pharmacist
**Where:** Prescription → Items → Bulk Dispense

The pharmacist reviews the prescription and dispenses items. Multiple items can be dispensed at once (bulk dispense).

**System actions (automatic)**
- Dispensing an item **deducts the quantity from medication stock**.
- A **stock movement** ledger entry is recorded (type: Dispensed, with before/after quantities).
- The system **blocks dispensing** if stock is insufficient or the medication is expired.
- When **all** items are dispensed, the prescription status becomes **Dispensed**, and `Dispensed At` and `Dispensed By` are stamped.
- Once **Dispensed**, the prescription and its items are **locked** — no further edits.
- Reversing a dispense restores stock and records a Returned movement.

### Step 5 — Admission (Doctor / Nurse)

**Who:** Doctor or Nurse
**Where:** Admissions → Create

If the patient needs inpatient care, an admission is created against a **ward** and **bed**, with the admitting doctor and reason recorded.

**System actions (automatic)**
- The chosen **bed is marked Occupied**; the patient's Care Status badge becomes **Inpatient**.
- **Double-booking is prevented** — a bed already occupied by an admitted patient cannot be reused.
- **Ward gender rules are enforced** — a male patient cannot be admitted to a female-only or maternity ward, and vice-versa.

### Step 6 — Daily Care Notes (Doctor / Nurse)

**Who:** Doctors and Nurses
**Where:** Admission → Notes

Throughout the stay, staff record dated clinical notes — ward rounds, observations, procedures, and nursing notes — building a running log for the admission.

**System actions**
- Each note records its **author** and **timestamp** automatically.
- Only the **author** of a note may edit it; notes cannot be deleted (permanent clinical record).
- Notes cannot be added to a **discharged** patient's admission.

### Step 7 — Medication Administration (Nurse)

**Who:** Nurse
**Where:** Admission → Administer Medication (action), or Medication Administrations

Nurses record each dose administered to the inpatient — medication, dosage, route, and time.

**System actions (automatic)**
- Administering **deducts one unit from medication stock** and logs a stock movement.
- The system blocks administration of out-of-stock or expired medication.
- The **Administer Medication** button is only available while the patient is admitted; it is disabled for discharged patients.
- The admission picker only shows currently-admitted patients (with patient number + name).

### Step 8 — Referral (Doctor) — where applicable

**Who:** Doctor
**Where:** Referrals → Create

If the patient must be sent to another facility, a referral captures the destination, reason, **priority**, and **status** (Pending → Accepted → Completed / Cancelled), linked to the visit or admission.

### Step 9 — Discharge (Doctor / Nurse)

**Who:** Doctor or Nurse
**Where:** Admission → Discharge Patient (action)

A single **Discharge Patient** button opens a modal to capture the **outcome** (Discharged / Transferred / Deceased), **discharge date/time**, and **discharge notes** — without editing the whole record.

**System actions (automatic)**
- The admission status is updated and the **discharge time stamped**.
- The **bed is freed** (status → Available) and **released** from the admission (no longer allocated).
- The patient's Care Status badge returns to **Outpatient**.
- After discharge, clinical records (notes, administrations, prescriptions) for that admission are **locked**.

### Step 10 — Billing & Invoicing (Billing)

**Who:** Billing team
**Where:** Visit → Generate Invoice (action), or Invoices → Create

An invoice is generated for the visit or admission. Line items (consultation, bed-day charges, dispensed medications, procedures) are added as invoice items.

**System actions (automatic)**
- A unique **invoice number** (`INV-00001`, …) is generated.
- The **total is recalculated** automatically from the line items.
- The **status is derived** from payments: Pending → Partially Paid → Paid. Medical-aid workflow states (Submitted to Medical Aid, Rejected) are preserved.
- Invoice items **cannot be added once the invoice is Paid**.
- The invoice can be **downloaded as a branded PDF**.
- Default charges (consultation fee, admission fee, ward day-rates) are drawn from admin-editable **Settings**.

---

## 6. Supporting Capabilities

### 6.1 Pharmacy stock management
- **Receive Stock** and **Stock Count Adjustment** actions (bulk-capable) update inventory and log movements.
- Metrics for **Total**, **Low Stock**, **Out of Stock**, and **Expiring** medications.
- Filters by stock status and expiry status.

### 6.2 Reporting
Seven reporting dashboards — **Patient, Outpatient, Inpatient, Pharmacy, Financial, Referral, Staff** — each permission-gated and exportable as a branded PDF (with page borders and "Page X of Y").

### 6.3 Configuration (Settings)
Billing fees and pharmacy thresholds are stored in the database and editable by administrators under **Configuration → Settings** — no code change required. Monetary values display the USD symbol.

### 6.4 Audit log
Every create/update/delete is recorded in a read-only **Audit Log**, showing the action, the user, the affected record (as a link), status, and time.

### 6.5 Documentation portal
Technical and project documents are available to signed-in staff under the **Documentation** menu.

---

## 7. Business Rules Summary

| Rule | Enforcement |
|------|-------------|
| Patient numbers are unique and sequential | Auto-generated on creation |
| A bed cannot be double-booked | Blocked on admission save |
| Ward gender restrictions (incl. maternity = female) | Blocked on admission save + form validation |
| Dispensing deducts stock; cannot exceed stock or dispense expired | Blocked on dispense |
| Prescription auto-completes when all items dispensed | Automatic status sync |
| A dispensed prescription is locked | Policy denies edits |
| No clinical records for discharged patients | Policy + validation |
| Care notes editable only by their author; never deleted | Policy |
| Invoice totals & status auto-derived | Model automation |
| Invoice items locked once Paid | Policy |
| Discharge frees and releases the bed | Model automation |

---

## 8. Glossary

- **Care Status** — whether a patient is currently an inpatient or outpatient.
- **Dispense** — issue prescribed medication, deducting it from stock.
- **Stock Movement** — an immutable inventory ledger entry.
- **Referral** — sending a patient to another facility.
- **Medical Aid** — insurance scheme covering a patient's charges.

---

*This document reflects the current, delivered system. Planned Phase 2 capabilities (integration layer for SMS/WhatsApp, ICD-10, SAP, and additional clinical modules) are described separately in the Architecture Document.*
