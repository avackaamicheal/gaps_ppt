# Real Estate Sales Platform — Development Plan

**Project:** Personal real estate sales platform for an independent agent  
**Market:** Nigeria  
**Stack:** Laravel, MySQL, Blade, Tailwind CSS, Alpine.js, Vite  
**Delivery:** Phased implementation. Complete and verify each phase before moving to the next.

## 1. Purpose and product boundaries

Build a platform for one independent real estate agent to showcase land and buildings, manage property listings, capture buyer enquiries, maintain customer records, and coordinate inspections. This is not a multi-vendor marketplace.

Confirmed decisions:
- Public visitors can browse listings without accounts.
- No public registration or buyer accounts.
- One administrator for the MVP.
- Contact options: phone, WhatsApp, and website enquiry form.
- Phone and WhatsApp leads can be entered manually; no WhatsApp synchronisation.
- No online payments or checkout.
- No interactive map in the MVP.
- Location filtering: Nigerian state, LGA, and neighbourhood.
- Technology: Laravel, MySQL, Blade, Tailwind CSS, Alpine.js, and Vite.

Use versions compatible with the actual environment. Inspect existing code and dependencies before upgrading or installing packages.

## 2. Development roadmap

**Execution rule:** Implement only the phase authorised by the project owner. Complete its acceptance criteria and provide a report before requesting approval for the next phase. Do not build later-phase features early.

### Phase 0 — Environment and repository inspection

**Objective:** Understand the existing project and development environment before making changes.

Tasks:
1. Inspect repository structure and determine whether Laravel is already installed.
2. Check Git status and preserve all existing user changes.
3. Inspect PHP and required extensions, Composer, Node.js, npm, MySQL, Laravel version, installed packages, frontend tooling, routes, migrations, models, controllers, views, and tests.
4. Identify dependency or compatibility issues before installing/upgrading anything.
5. Confirm how the app is started, configured, and tested.
6. Document setup steps and existing blockers.

Deliverables:
- Repository/environment assessment.
- List of blockers or dependency changes required.
- Proposed Phase 1 implementation sequence.

Acceptance criteria:
- Existing structure and runtime versions are understood.
- No existing work has been overwritten.
- Dependency changes are identified before installation.
- The app runs, or existing startup failures are documented.

No product features are built in this phase.

### Phase 1 — Application foundation and admin access

**Objective:** Establish the Laravel foundation, public shell, and secure single-admin access.

Tasks:

**Application setup**
- Configure Laravel and MySQL.
- Configure local environment variables without committing secrets.
- Set up Vite, Tailwind CSS, and Alpine.js.
- Confirm assets compile and load in Blade layouts.
- Establish consistent naming, folder structure, and reusable UI conventions.

**Admin authentication**
- Build a custom Blade admin login.
- Do not create public registration.
- Provide a controlled first-admin creation method, such as an Artisan command or seeder that is not exposed publicly.
- Protect all admin routes with authentication middleware.
- Rate-limit login attempts.
- Regenerate the session after successful login.
- On logout, invalidate the session and regenerate the CSRF token.
- Validate and display authentication errors safely.

**Layouts and public homepage**
- Create separate responsive public and admin layouts.
- Establish consistent navigation, typography, spacing, and reusable components.
- Build a homepage shell with a personal-brand hero, introductory property section, agent introduction, and phone/WhatsApp calls to action.
- Use clearly identified placeholder content where final copy is unavailable.
- Do not build functional property management or listing search yet.

**Admin dashboard shell**
- Build an authenticated overview page.
- Add placeholder navigation for Overview, Properties, Enquiries, Customers, Inspections, Documents, Testimonials, and Website Settings.
- Only Overview is functional in this phase.
- Mark unfinished sections as unavailable/coming later. Do not show fake statistics or imply unfinished features work.

Deliverables:
- Laravel app connected to MySQL.
- Working frontend build pipeline.
- Public homepage shell.
- Admin login/logout and protected dashboard shell.
- Initial setup instructions.

Acceptance criteria:
- App boots and migrations run successfully.
- Vite assets compile; Tailwind and Alpine work.
- Public homepage loads without authentication.
- Admin can log in and log out.
- Unauthenticated visitors cannot access admin routes.
- No public registration route/page exists.
- Login throttling and session security behaviours are implemented.
- Dashboard navigation does not imply unfinished features work.
- Relevant automated tests pass.

Explicitly excluded: property CRUD, public property search, enquiries, customers, inspections, document management, testimonials management, and website settings management.

### Phase 2 — Property management and public listings

**Objective:** Let the administrator manage inventory and visitors browse published properties.

Tasks:

**Data model**
- Create schema, models, relationships, validation, factories, and seeders for property categories/types, properties, property images, property documents, and Nigerian locations.
- Represent state, LGA, and neighbourhood in a maintainable way.
- Support land-specific and building-specific attributes without overcomplicating the schema.

**Property references and statuses**
- Generate unique references, using readable prefixes where appropriate, e.g. `LAND-0001`, `PROP-0001`, or `COMM-0001`.
- Reference generation must be reliable under concurrent creation and must not rely on browser-submitted values.
- Support statuses: Draft, Published, Under Negotiation, Reserved, Sold, Unavailable, Archived.
- Enforce public visibility on the server. Only properties intended for public display may appear as public listings.

**Admin property management**
- Build property list, create, view, edit, publish, unpublish, archive, and appropriate delete actions.
- Support title, description, price, category/type, location, status, and relevant attributes.
- Land attributes may include plot size, dimensions, title/documentation information, and permitted use.
- Building attributes may include bedrooms, bathrooms, condition, building type, and other relevant features.
- Validate fields server-side and confirm destructive actions.

**Images and documents**
- Support multiple images, cover image selection, and image ordering.
- Validate upload type and size; use Laravel storage abstraction.
- Allow property documents to be attached.
- Keep documents private by default. Do not expose private document URLs publicly.
- Provide access to public documents only when explicitly intended and safely authorised.

**Public discovery**
- Build property listing and detail pages.
- Add filters for category/type, state, LGA, neighbourhood, and keyword.
- Add pagination and preserve filters in the URL where practical.
- Display only published properties intended for public viewing.
- Provide useful empty states.

Deliverables:
- Property schema and relationships.
- Admin property CRUD and status workflow.
- Image/document handling.
- Public listing and detail pages, search, filters, and pagination.

Acceptance criteria:
- Admin can create/update land and building listings.
- References are unique and reliably generated.
- Images can be uploaded and managed.
- Private documents are not publicly accessible.
- Non-public listings do not appear as available public listings.
- Combined filters return correct results.
- Detail pages show correct property information and images.
- Validation, authorisation, and tests cover important workflows.

### Phase 3 — Enquiries, customers, and inspections

**Objective:** Turn visitor interest and manually recorded conversations into manageable sales leads.

Tasks:

**Website enquiry capture**
- Add enquiry form to property detail and/or contact pages.
- Capture name, contact details, message, and related property where applicable.
- Validate/sanitise input; add basic spam protection and rate limiting.
- Record source, such as website, where known.

**Admin enquiry management**
- Add enquiry list, detail view, search/filtering, status updates, and internal notes.
- Associate enquiries with properties and customers where appropriate.
- Support statuses: New, Contacted, Inspection Scheduled, Negotiation, Converted, Closed/Lost.

**Manually recorded leads**
- Allow admin to record leads from phone, WhatsApp, referrals, or other offline channels.
- Capture lead source.
- Do not read or synchronise WhatsApp messages.

**Customer records**
- Create customers from enquiries or directly in the dashboard.
- Store relevant contact information and internal notes.
- Help avoid duplicate records where practical and allow enquiries to be associated with existing customers.
- Show related enquiries and inspections.

**Inspection scheduling**
- Schedule inspections against a property and customer/enquiry.
- Capture date, time, meeting location/instructions, notes, and status.
- Allow updates, rescheduling, completion, and cancellation.
- Display upcoming and past inspections.
- Support statuses: Pending Confirmation, Confirmed, Completed, Cancelled, Rescheduled.

Deliverables:
- Website enquiry form.
- Admin enquiry management and manual lead entry.
- Customer records and relationships.
- Inspection scheduling and management.

Acceptance criteria:
- Website enquiries are saved and linked to the relevant property where supplied.
- Admin can record leads received outside the website.
- Enquiry sources/statuses are stored correctly.
- Customers can be linked to multiple enquiries and inspections.
- Inspections can be scheduled and updated.
- Validation and authorisation protect customer/lead data.
- Automated tests cover enquiry submission and key admin workflows.

Explicitly excluded: WhatsApp API integration, automated messaging, online payments, and buyer accounts.

### Phase 4 — Content management and launch readiness

**Objective:** Complete supporting content controls and prepare the app for production.

Tasks:

**Testimonials and website settings**
- Add admin CRUD for testimonials, including publish/unpublish.
- Display only approved/published testimonials publicly.
- Allow admin to manage agent name, phone, WhatsApp number, email, business address, social links, and selected homepage content.
- Validate settings and provide safe defaults. Avoid hardcoding editable business information throughout templates.

**Activity logs**
- Record important admin actions, including property changes and lead/inspection updates.
- Include actor, action, affected record, and timestamp.
- Never log passwords, secrets, or unnecessary sensitive information.

**SEO and sharing**
- Add page titles, meta descriptions, canonical URLs where appropriate, Open Graph metadata, and sitemap.
- Configure robots directives appropriately.
- Use descriptive image alt text and readable, stable property URLs.

**Security, quality, and production**
- Review authentication, authorisation, validation, upload handling, and private file access.
- Check CSRF protection and rate limiting.
- Review queries for N+1 patterns and add useful indexes.
- Ensure production errors do not expose debug information.
- Run automated tests and fix regressions.
- Configure production environment variables, database, file storage, and HTTPS.
- Set up backups and document restore procedure.
- Configure logs/basic monitoring.
- Run migrations safely, build production assets, and document deployment/maintenance.

Deliverables:
- Testimonial and website settings management.
- Activity logs.
- SEO/sharing improvements.
- Security/performance review.
- Deployment and backup documentation.

Acceptance criteria:
- Admin can manage testimonials and key website settings.
- Important admin actions are recorded.
- Public pages have suitable metadata and sharing previews.
- Production configuration does not expose debug details or secrets.
- Backup/restore procedures are documented.
- Full test suite passes.
- Site works on mobile and desktop.
- Production deployment checklist is complete.

## 3. Recommended implementation order within each phase

1. Inspect relevant existing code and confirm requirements.
2. Design database changes before dependent screens.
3. Create migrations, models, relationships, factories, and seeders.
4. Add server-side validation.
5. Implement authorisation and business rules.
6. Implement routes, controllers, and services.
7. Build Blade views and reusable UI components.
8. Add Alpine.js interactions only where needed.
9. Add automated tests.
10. Run migrations, tests, and frontend builds.
11. Review against phase acceptance criteria.
12. Report work and wait for approval before proceeding.

## 4. Engineering standards

### Laravel and data integrity
- Prefer established Laravel conventions over unnecessary custom abstractions.
- Use migrations for schema changes and Eloquent relationships appropriately.
- Use form requests or clear server-side validation.
- Keep controllers focused; do not put business logic in Blade templates.
- Use policies/gates for authorisation where appropriate.
- Use transactions when multiple operations must succeed or fail together.
- Add useful constraints, foreign keys, and indexes.
- Choose appropriate delete behaviour for related records.
- Do not show fake dashboard statistics or silently discard submitted information.

### Security and privacy
- Never commit `.env`, credentials, API keys, or production secrets.
- Do not add public registration.
- Protect all admin routes and validate input server-side.
- Validate uploads by actual content/type and size, not filename alone.
- Protect customer information and private property documents.
- Do not expose internal error traces in production.
- Do not introduce insecure shortcuts to make tests pass.

### Frontend and accessibility
- Build responsive mobile, tablet, and desktop layouts.
- Use semantic HTML and accessible form labels.
- Ensure keyboard navigation for interactive controls.
- Provide appropriate loading, empty, success, and error states.
- Keep the public/admin visual system consistent.
- Use Alpine.js for lightweight interactions, not as a replacement for server-side rules.

### Testing
- Test business rules, not only page rendering.
- Cover authentication, authorisation, validation, public visibility, and relationships.
- Run relevant tests after meaningful changes.
- Run the production asset build before declaring a phase complete.

## 5. Definition of Done

A phase is complete only when:
- All agreed work for the phase is implemented.
- Acceptance criteria have been checked.
- Migrations run successfully.
- Relevant automated tests pass.
- Frontend assets compile.
- No known critical security or data-integrity issue remains.
- UI is checked at mobile and desktop widths.
- Setup/implementation documentation is updated.
- A completion report is provided.

The completion report must state:
1. What was implemented.
2. Important files created/modified.
3. Migrations and database changes.
4. Packages added/updated, with reasons.
5. Commands run.
6. Tests/build checks performed and actual results.
7. Known issues or deferred work.
8. Any action required from the project owner.

## 6. Instructions for the AI coding agent

When given this plan and the Product Requirements Document (PRD):

1. Read both documents fully before changing code.
2. Treat the PRD as the source of product requirements and this plan as the implementation sequence.
3. Inspect the repository and environment before making assumptions.
4. Start with Phase 0 if the repository has not already been assessed.
5. Implement only the phase explicitly authorised by the project owner.
6. Do not build later-phase features early.
7. Do not overwrite existing files or user work without inspecting them and explaining why a change is needed.
8. Do not invent business information, property records, testimonials, or dashboard statistics. Clearly label development seed data.
9. Do not add public registration, buyer accounts, online payments, marketplace/vendor functionality, or WhatsApp synchronisation.
10. Ask for clarification only when a decision materially blocks implementation. Otherwise follow the documents and Laravel conventions.
11. Run relevant tests/build checks. Never claim a check passed unless it was actually run.
12. Provide the completion report described above.
13. Stop after the authorised phase and wait for approval.

## 7. Future enhancements — outside the initial release

Consider only after the core platform is launched and validated:
- Interactive maps and geolocation-based discovery.
- Buyer accounts, saved properties, and favourites.
- WhatsApp Business API and automated notifications.
- Email/SMS notification workflows.
- Advanced analytics and sales reporting.
- Multiple administrators and role-based permissions.
- Property comparison tools.
- Online payments, only if the business model later requires them.
- Mobile application.

These enhancements must not delay or expand the agreed MVP.
