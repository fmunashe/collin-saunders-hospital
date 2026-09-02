% Hospital Management System (HMS)
% Staff Training — AnalyticsHive for Collin Saunders Hospital
%

# Welcome to HMS Training

## What you'll learn

- What HMS is and how it's organised
- The complete patient journey, step by step
- Your role's specific tasks
- Hands-on practice at each stage

::: notes
Welcome participants. Explain the session follows a real patient through the system.
:::

## What is HMS?

- A single web platform for the whole patient lifecycle
- Three core modules: **Outpatient**, **Inpatient**, **Pharmacy**
- Plus billing, referrals, reporting, and administration
- All amounts in **USD**

## Getting started

- Open the web address → **Staff Portal**
- Log in with your email and password
- The **left sidebar** groups everything
- Use **search** to find a patient by number or name

# The Patient Journey

## The big picture

Register → Visit → Prescription → Dispense
→ Admission → Care Notes → Medication
→ Referral → Discharge → Invoice

Each step has an owner and happens in order.

## 1. Register the patient — Reception

- Patients → **Create Patient**
- Enter details, patient type, billing type
- *Automatic:* a **patient number** (PT00001) is created
- Care Status starts as **Outpatient**

## 2. Open a visit — Reception / Doctor

- Visits → **Create Visit**
- Select patient, doctor, department
- Record the complaint
- Status: **Waiting → In Progress → Completed**

## 3. Prescription — Doctor

- Prescriptions → **Create**
- Link the patient and visit (or admission)
- Add items: medication, dosage, quantity
- Starts as **Pending**

## 4. Dispensing — Pharmacy

- Prescription → Items → **Bulk Dispense**
- *Automatic:* stock is deducted and logged
- Blocked if stock is short or expired
- All items dispensed → prescription **Dispensed** and locked

## 5. Admission — Doctor / Nurse

- Admissions → **Create**
- Select ward and bed
- *Automatic:* bed becomes **Occupied**; patient is **Inpatient**
- Blocked if bed occupied or ward gender rule mismatched

## 6. Care notes — Doctor / Nurse

- Admission → **Notes**
- Type: Doctor / Nurse / Observation / Procedure / General
- *Automatic:* your name and time are recorded
- Only the author can edit; notes are never deleted

## 7. Medication administration — Nurse

- Admission → **Administer Medication**
- Medication, dosage, route, time
- *Automatic:* one unit deducted from stock
- Only available while the patient is admitted

## 8. Referral — Doctor (when needed)

- Referrals → **Create**
- Destination, reason, priority
- Status: Pending → Accepted → Completed / Cancelled

## 9. Discharge — Doctor / Nurse

- Admission → **Discharge Patient** button
- Modal: outcome, date/time, discharge notes
- *Automatic:* bed freed & released; patient back to **Outpatient**
- Records for the stay are then locked

## 10. Invoice — Billing

- Generate from the visit, or Invoices → Create
- Add line items (consultation, bed-days, medications)
- *Automatic:* number, total, and status
- Locked once **Paid**; download as branded PDF

# Supporting Tools

## Pharmacy stock

- **Receive Stock** and **Stock Count Adjustment** actions
- Cards: Total, Low Stock, Out of Stock, Expiring
- Every change logged in the stock ledger

## Reports

- Seven dashboards: Patient, Outpatient, Inpatient, Pharmacy, Financial, Referral, Staff
- Export each as a branded PDF
- You see only what your role permits

## Administration

- **Users** and **Roles** (assign many permissions at once)
- **Settings** — fees and thresholds (shown in $)
- **Audit Log** — who did what, when

# Rules to Remember

## The system protects the data

- Beds can't be double-booked
- Ward gender rules (maternity = female)
- Can't dispense short or expired stock
- Dispensed prescriptions are locked
- No records for discharged patients
- Invoices lock once paid

## If something is blocked…

- Check stock and expiry (dispensing)
- Check the bed and ward gender (admission)
- Check discharge status (notes / medication)
- Check invoice paid status (invoice items)

# Practice & Wrap-up

## Hands-on exercise

Follow one test patient end-to-end:

1. Register → 2. Visit → 3. Prescribe → 4. Dispense
5. Admit → 6. Care note → 7. Administer → 8. Discharge → 9. Invoice

## Key takeaways

- HMS follows the real patient flow
- The system automates numbers, stock, beds, totals
- Rules keep records safe and accurate
- Practice on test data first

## Questions?

Thank you — and welcome to HMS.

*AnalyticsHive · Collin Saunders Hospital*
