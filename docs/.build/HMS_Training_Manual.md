# HMS User Training Manual

**Audience:** Hospital staff (Reception, Doctors, Nurses, Pharmacy, Billing, Administrators)
**Format:** Hands-on training guide with step-by-step tasks and exercises

---

## How to use this manual

This manual trains you to use the Hospital Management System (HMS) for your daily work. It follows the same order a patient moves through the hospital, so you learn the flow, not just the buttons. Each section lists **what you do**, **the exact steps**, and a short **practice exercise**.

> Tip: Anything marked *automatic* is done for you by the system — you don't need to calculate or update it manually.

---

## Module 0 — Getting Started

### Logging in
1. Open the HMS web address in your browser.
2. Click **Staff Portal**.
3. Enter your email and password, then click **Log In**.
4. If your account has no role yet, you'll see a message to contact your administrator.

### Finding your way around
- The **left sidebar** groups everything: Patients, Pharmacy, Reports, Configuration, and more.
- Use the **search box** (top) to jump to a patient by number or name.
- Lists have **filters** (funnel icon) and a **per-page** selector.
- IDs are hidden on lists and shown only on a record's detail page.

**Exercise:** Log in, open the **Patients** list, and change the per-page selector to 25.

---

## Module 1 — Reception: Registering Patients

### Register a new patient
1. Sidebar → **Patients** → **Create Patient**.
2. Fill in first name, last name, date of birth, gender, and phone.
3. Choose **Patient Type** (Staff / Non-staff) and **Billing Type** (Cash / Medical Aid).
4. Click **Create Patient**.

*Automatic:* the system assigns a **patient number** (e.g. `PT00042`). You never type this yourself.

### Add medical-aid details (if Billing Type = Medical Aid)
1. Open the patient → **Medical Aid Detail** → add scheme and membership number.

**Exercise:** Register a test patient named "Training Patient", Cash billing. Note the patient number the system created.

---

## Module 2 — Reception & Doctors: Visits

### Open a visit
1. Sidebar → **Visits** → **Create Visit**.
2. Search and select the **patient** (shows number + name).
3. Select the **doctor** and **department**.
4. Enter the **complaint**, set the date/time, and save.

### Work the queue
- Filter visits by **department**, **doctor**, or **status**.
- Update status as care progresses: **Waiting → In Progress → Completed**.

**Exercise:** Create a visit for your test patient, assign any doctor and department, and set status to In Progress.

---

## Module 3 — Doctors: Prescriptions

### Create a prescription
1. Sidebar → **Prescriptions** → **Create**.
2. Select the **patient** (and the **visit** for outpatients, or **admission** for inpatients).
3. Save, then add **items**: choose the medication, dosage, quantity, and instructions.

*Note:* new prescriptions start as **Pending**. You cannot prescribe against a discharged admission.

**Exercise:** Create a prescription for your test patient's visit and add two medication items.

---

## Module 4 — Pharmacy: Dispensing & Stock

### Dispense a prescription
1. Open the **Prescription** → **Items**.
2. Select the items to issue and use **Bulk Dispense**.

*Automatic:* stock is **deducted**, a **stock movement** is logged, and when all items are dispensed the prescription becomes **Dispensed** (and is then locked). The system stops you if stock is short or the medicine is expired.

### Receive new stock
1. Sidebar → **Medications** → select items → **Actions** → **Receive Stock**.
2. Enter the quantity (and reference/notes) → run.

### Stock count adjustment
1. **Medications** → select → **Actions** → **Stock Count Adjustment** → enter the corrected figure.

### Watch your levels
- Cards show **Total**, **Low Stock**, **Out of Stock**, and **Expiring** medications.
- Filter by stock status or expiry status.

**Exercise:** Receive 50 units of any medication, then dispense your test prescription and confirm the stock dropped.

---

## Module 5 — Doctors & Nurses: Admissions

### Admit a patient
1. Sidebar → **Admissions** → **Create**.
2. Select **patient**, **doctor**, **department**, **ward**, and **bed**.
3. Enter the reason and admission time, then save.

*Automatic:* the bed becomes **Occupied**, and the patient's badge becomes **Inpatient**.

*The system will stop you if:* the bed is already occupied, or the ward's gender rule doesn't match the patient (e.g. a male patient into a female or maternity ward).

**Exercise:** Admit your test patient to an appropriate ward and bed.

---

## Module 6 — Doctors & Nurses: Care Notes

### Add a care note
1. Open the **Admission** → **Notes** → add a note.
2. Choose the **type** (Doctor / Nurse / Observation / Procedure / General) and write the note.

*Automatic:* your name and the time are recorded. Only you can edit your own note; notes are never deleted. You cannot add notes to a discharged patient.

**Exercise:** Add a nursing observation note to your test admission.

---

## Module 7 — Nurses: Medication Administration

### Record a dose
1. Open the **Admission** → **Administer Medication**.
2. Select the **medication**, enter **dosage** and **route**, set the time, and confirm.

*Automatic:* one unit is deducted from stock and logged. The button is only available while the patient is admitted.

**Exercise:** Record one administered dose against your test admission.

---

## Module 8 — Doctors: Referrals (when needed)

### Refer to another facility
1. Sidebar → **Referrals** → **Create**.
2. Record the destination, reason, **priority**, and link the visit or admission.
3. Track the **status**: Pending → Accepted → Completed / Cancelled.

---

## Module 9 — Doctors & Nurses: Discharge

### Discharge a patient
1. Open the **Admission** → click **Discharge Patient**.
2. In the modal choose the **outcome** (Discharged / Transferred / Deceased), set the **date/time**, and write **discharge notes**.
3. Confirm.

*Automatic:* the discharge time is stamped, the **bed is freed and released**, and the patient becomes **Outpatient** again. Records for that stay are then locked.

**Exercise:** Discharge your test admission with a short discharge note, then confirm the bed shows Available.

---

## Module 10 — Billing: Invoices

### Generate an invoice
1. From a **Visit**, use **Generate Invoice**; or **Invoices → Create** for an admission.
2. Add **invoice items** (consultation, bed-days, medications, procedures).

*Automatic:* the invoice number, total, and status (Pending → Partially Paid → Paid) are handled for you. You cannot add items once an invoice is **Paid**.

### Record payment
1. Open the invoice → update the **paid amount**. The status updates automatically.

### Print / download
1. Open the invoice → **Download Invoice** for a branded PDF.

**Exercise:** Generate an invoice for your test visit, add a consultation line, record a partial payment, and download the PDF.

---

## Module 11 — Reports

1. Sidebar → **Reports** → open any dashboard (Patient, Outpatient, Inpatient, Pharmacy, Financial, Referral, Staff).
2. Use **Download PDFs** to export a branded report.

*You only see the reports your role permits.*

---

## Module 12 — Administrators

### Manage users, roles & permissions
- **Users** → create staff accounts and assign a role.
- **Roles** → tick the permissions for a role (you can select many at once) and save.

### Edit system settings
- **Configuration → Settings** → update fees and thresholds (consultation fee, admission fee, ward day-rates, expiry warning days). Money fields show the **$** symbol.

### Review the audit log
- **Audit Log** → see who did what and when; click through to the affected record.

**Exercise (admin):** Create a "nurse" test user, assign the Nurse role, and confirm they can reach Admissions but not Settings.

---

## Quick Reference — Who does what

| Task | Role | Where |
|------|------|-------|
| Register patient | Reception | Patients → Create |
| Open visit | Reception/Doctor | Visits → Create |
| Prescribe | Doctor | Prescriptions |
| Dispense | Pharmacy | Prescription → Bulk Dispense |
| Receive stock | Pharmacy | Medications → Receive Stock |
| Admit | Doctor/Nurse | Admissions → Create |
| Care notes | Doctor/Nurse | Admission → Notes |
| Give medication | Nurse | Admission → Administer Medication |
| Refer out | Doctor | Referrals |
| Discharge | Doctor/Nurse | Admission → Discharge Patient |
| Invoice & payment | Billing | Invoices |
| Reports | Per role | Reports |

---

## Common Questions

**The system won't let me dispense — why?** Stock may be insufficient or the medicine expired. Check the medication's stock and expiry.

**I can't edit a prescription.** Once fully dispensed, prescriptions are locked. Create a new one if needed.

**I can't admit a patient to a ward.** The bed may be occupied, or the ward's gender rule doesn't match the patient.

**I can't add notes/medication for a patient.** They may already be discharged — records for a discharged stay are locked.

**I can't add an item to an invoice.** The invoice is probably marked Paid.

---

*Practice on test data first. When in doubt, ask your administrator or refer to the System Documentation.*
