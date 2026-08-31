# Hospital Management System — Team Composition

**Document Version:** 1.1
**Date:** 26 August 2026

---

## 1. Team Structure

```
                         ┌─────────────────┐
                         │ Project Manager │
                         │    (1 person)   │
                         └────────┬────────┘
                                  │
         ┌────────────┬───────────┼───────────┬────────────┐
         │            │           │           │            │
┌────────▼───┐ ┌──────▼─────┐ ┌──▼────┐ ┌────▼─────┐ ┌───▼──────────┐
│ Solution   │ │ Business   │ │  Dev  │ │ QA/Test  │ │ Cyber        │
│ Architect  │ │ Analyst(1) │ │ Team  │ │ Engr (1) │ │ Security (1) │
│    (1)     │ │            │ │  (3)  │ │          │ │              │
└────────────┘ └────────────┘ └───┬───┘ └──────────┘ └──────────────┘
                                  │
                    ┌─────────────┼─────────────┐
                    │             │             │
          ┌─────────▼──┐ ┌───────▼────┐ ┌──────▼───────┐
          │ Senior Dev │ │ Mid Dev 1  │ │ Mid Dev 2    │
          │ (Lead)     │ │            │ │              │
          └────────────┘ └────────────┘ └──────────────┘

                         ┌────────────────┐
                         │   Database     │
                         │ Administrator  │
                         │      (1)       │
                         └────────────────┘
```

**Total Team Size: 9 members**

---

## 2. Role Descriptions & Responsibilities

### Project Manager (1)

**Allocation:** Full-time (6 months)

| Area | Responsibility |
|------|---------------|
| Planning | Sprint planning, milestone tracking, risk management |
| Communication | Client liaison, status reporting, stakeholder management |
| Delivery | Scope management, change control, quality gates |
| Travel | Leads all on-site visits, coordinates logistics |
| Governance | Budget tracking, resource allocation, escalation management |

**Required Skills:** Agile/Scrum certification, healthcare project experience preferred, stakeholder management

---

### Solution Architect (1)

**Allocation:** Part-time — 50% Month 1–2, 30% Month 3–5, 50% Month 6

| Area | Responsibility |
|------|---------------|
| System Design | End-to-end solution architecture, technology stack decisions |
| Integration | System integration patterns, API strategy, third-party connectivity |
| Standards | Coding standards, design patterns, architectural governance |
| Scalability | Performance architecture, caching strategy, load planning |
| Review | Architecture review gates, technical debt management |
| Documentation | Architecture decision records, system context diagrams, deployment architecture |

**Required Skills:** Enterprise architecture (TOGAF preferred), healthcare IT systems, Laravel/PHP ecosystem, cloud infrastructure, integration patterns (REST, HL7/FHIR awareness), high-availability design

---

### Database Administrator (1)

**Allocation:** Part-time — 60% Month 1–2, 40% Month 3–4, 60% Month 5–6

| Area | Responsibility |
|------|---------------|
| Design | Database schema design, normalization, indexing strategy |
| Performance | Query optimization, slow query analysis, database tuning |
| Security | Access controls, encryption at rest, data masking for PII |
| Backup | Backup strategy, disaster recovery, point-in-time recovery |
| Migration | Data migration planning, ETL from legacy systems |
| Monitoring | Database health monitoring, alerting, capacity planning |
| Compliance | Data retention policies, audit logging, POPIA/GDPR considerations |

**Required Skills:** MySQL/PostgreSQL (advanced), database design, performance tuning, backup/recovery, data security, migration tools, monitoring (Percona, pgAdmin)

---

### Cyber Security Specialist (1)

**Allocation:** Part-time — 30% Month 1, 20% Month 2–4, 40% Month 5, 60% Month 6

| Area | Responsibility |
|------|---------------|
| Assessment | Threat modelling, vulnerability assessment, risk analysis |
| Policy | Security policies, access control framework, password policies |
| Application | OWASP compliance, input validation review, authentication hardening |
| Infrastructure | Firewall rules, network segmentation, SSL/TLS configuration |
| Audit | Security audit, penetration testing coordination, compliance review |
| Data Protection | PHI/PII protection, encryption standards, data classification |
| Training | Security awareness for dev team, secure coding guidelines |
| Incident Response | Incident response plan, breach notification procedures |

**Required Skills:** CISSP/CEH/CompTIA Security+, web application security (OWASP Top 10), healthcare data regulations (POPIA, HIPAA awareness), penetration testing, network security, encryption standards

---

### Business Analyst (1)

**Allocation:** Full-time (6 months)

| Area | Responsibility |
|------|---------------|
| Requirements | Elicitation, documentation, user stories, acceptance criteria |
| Process Mapping | Clinical workflows, patient journeys, departmental processes |
| Validation | UAT coordination, requirements traceability |
| Documentation | System specifications, user manuals, training materials |
| Domain | Hospital operations understanding, regulatory compliance |

**Required Skills:** Healthcare domain knowledge, process modelling (BPMN), requirements management tools

---

### Senior Developer / Tech Lead (1)

**Allocation:** Full-time (6 months)

| Area | Responsibility |
|------|---------------|
| Architecture | Implementation of solution architecture, database schema, API design |
| Development | Core modules, complex integrations, performance optimization |
| Leadership | Code reviews, technical mentoring, standards enforcement |
| DevOps | CI/CD setup, deployment pipelines, server configuration |
| Decisions | Library selection, implementation patterns, technical trade-offs |

**Required Skills:** Laravel (advanced), Nova, PHP 8+, MySQL/PostgreSQL, Vue.js, REST APIs, DevOps

---

### Mid-Level Developer (2)

**Allocation:** Full-time (6 months)

| Area | Responsibility |
|------|---------------|
| Development | Feature implementation, CRUD operations, UI components |
| Testing | Unit tests, feature tests, bug fixes |
| Integration | Third-party APIs, reporting modules, data imports/exports |
| Support | Documentation, code comments, knowledge transfer |

**Developer 1 Focus:** Backend — models, policies, actions, business logic, pharmacy & billing
**Developer 2 Focus:** Frontend & reporting — Nova resources, dashboards, metrics, PDF generation, exports

**Required Skills:** Laravel (intermediate+), Nova, PHP 8+, SQL, Vue.js basics, Git

---

### QA/Test Engineer (1)

**Allocation:** Full-time (6 months)

| Area | Responsibility |
|------|---------------|
| Test Planning | Test strategy, test cases, regression suites |
| Execution | Manual testing, exploratory testing, cross-browser testing |
| Automation | Automated test scripts for critical paths |
| UAT | Support client UAT sessions, defect management |
| Quality | Performance testing, security testing (basic), accessibility |

**Required Skills:** Test management tools, PHP/Laravel testing (Pest/PHPUnit), browser testing, defect tracking

---

## 3. Team Allocation by Phase

### Phase 1: Outpatient, Inpatient & Pharmacy (Months 1–3)

| Role | Month 1 | Month 2 | Month 3 |
|------|---------|---------|---------|
| Project Manager | 100% | 100% | 100% |
| Solution Architect | 50% | 50% | 30% |
| Database Administrator | 60% | 60% | 40% |
| Cyber Security Specialist | 30% | 20% | 20% |
| Business Analyst | 100% | 80% | 60% |
| Senior Developer | 100% | 100% | 100% |
| Mid Developer 1 | 100% | 100% | 100% |
| Mid Developer 2 | 80% | 100% | 100% |
| QA Engineer | 40% | 80% | 100% |

### Phase 2: Lab, Theatre, Radiology & Other (Months 4–6)

| Role | Month 4 | Month 5 | Month 6 |
|------|---------|---------|---------|
| Project Manager | 100% | 100% | 100% |
| Solution Architect | 30% | 30% | 50% |
| Database Administrator | 40% | 60% | 60% |
| Cyber Security Specialist | 20% | 40% | 60% |
| Business Analyst | 100% | 80% | 60% |
| Senior Developer | 100% | 100% | 80% |
| Mid Developer 1 | 100% | 100% | 100% |
| Mid Developer 2 | 100% | 100% | 100% |
| QA Engineer | 80% | 100% | 100% |

---

## 4. RACI Matrix

| Activity | PM | SA | DBA | Sec | BA | Senior Dev | Mid Devs | QA |
|----------|----|----|-----|-----|----|-----------|----------|-----|
| Requirements gathering | A | C | I | C | R | C | I | C |
| Solution architecture | C | R | C | C | I | C | I | I |
| Database design | I | C | R | C | I | C | I | I |
| Security framework | C | C | C | R | I | C | I | C |
| Sprint planning | R | I | I | I | C | C | C | C |
| Feature development | I | I | I | I | I | A | R | I |
| Code review | I | C | I | C | I | R | I | I |
| Database optimization | I | C | R | I | I | C | I | I |
| Security review | I | C | C | R | I | C | I | C |
| Unit testing | I | I | I | I | I | C | R | C |
| Integration testing | I | C | C | C | I | C | C | R |
| Penetration testing | I | I | I | R | I | C | I | C |
| UAT coordination | A | I | I | I | R | I | I | C |
| Deployment | A | C | C | C | I | R | C | C |
| Go-live security sign-off | C | C | C | R | I | C | I | I |
| Training & handover | A | C | C | C | R | C | C | C |
| Documentation | C | C | C | C | R | C | C | C |

**R** = Responsible, **A** = Accountable, **C** = Consulted, **I** = Informed

---

## 5. Communication Plan

| Meeting | Frequency | Participants | Duration |
|---------|-----------|-------------|----------|
| Daily Standup | Daily | Dev team, QA | 15 min |
| Sprint Planning | Bi-weekly | Full team | 2 hours |
| Sprint Demo | Bi-weekly | Full team + client | 1 hour |
| Sprint Retrospective | Bi-weekly | Full team | 45 min |
| Client Status Update | Weekly | PM + client | 30 min |
| Architecture Review | Bi-weekly | SA, Senior Dev, DBA, PM | 1 hour |
| Security Review | Monthly | Sec Specialist, SA, Senior Dev | 1 hour |
| Database Review | Bi-weekly | DBA, Senior Dev | 45 min |

---

## 6. Tools & Environment

| Category | Tool |
|----------|------|
| Project Management | Jira / Linear |
| Communication | Slack / Microsoft Teams |
| Repository | GitHub / GitLab |
| CI/CD | GitHub Actions / GitLab CI |
| Documentation | Confluence / Notion |
| Design | Figma (if UI customization needed) |
| Testing | Pest (PHP), Playwright (E2E) |
| Monitoring | Laravel Telescope, Sentry |
| Database | MySQL Workbench / pgAdmin, Percona Monitoring |
| Security | OWASP ZAP, Burp Suite, SonarQube |
| Architecture | Draw.io, Structurizr (C4 diagrams) |

---

## 7. Key Interactions & Collaboration Points

```
Solution Architect ◄──────► Senior Developer
       │                          │
       │    Architecture          │    Implementation
       │    decisions             │    guidance
       ▼                          ▼
Database Administrator ◄──► Mid Developers
       │                          │
       │    Schema design         │    Feature queries
       │    Query review          │    Data access
       ▼                          ▼
Cyber Security Specialist ◄──► QA Engineer
       │                          │
       │    Security testing      │    Test coverage
       │    Vulnerability fixes   │    Regression
       ▼                          ▼
Business Analyst ◄────────► Project Manager
       │                          │
       │    Requirements          │    Delivery
       │    Validation            │    Governance
       ▼                          ▼
                    CLIENT
```
