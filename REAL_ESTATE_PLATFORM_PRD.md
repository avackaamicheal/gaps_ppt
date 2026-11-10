# Product Requirements Document (PRD)
# Personal Real Estate Sales Platform

**Product Type:** Personal Real Estate Website and Admin Dashboard  
**Target Market:** Nigeria  
**Inventory:** Land and Buildings  
**Business Model:** Independent Real Estate Agent  
**Document Status:** MVP Product Specification

---

## 1. Product Overview

### 1.1 Introduction

The product is a personal real estate sales platform designed for an independent real estate agent operating in Nigeria.

The platform will provide two major interfaces:

1. **Public-facing website:** A professional website where prospective buyers can discover available land and properties, view property details, and contact the agent.
2. **Private admin dashboard:** A secure management interface where the agent can manage property listings, enquiries, customers, inspections, documents, testimonials, and website content.

This is not a multi-vendor real estate marketplace. The platform is built around one agent's personal brand and business operations.

### 1.2 Product Vision

To create a credible digital presence that makes it easier for prospective buyers to discover properties, engage with the agent, and arrange property inspections while giving the agent a centralised system for managing real estate activities.

### 1.3 Product Objectives

- Establish a professional online presence for the agent.
- Showcase land and buildings available for sale.
- Make it easy for visitors to find properties based on location and property type.
- Generate leads through website enquiries, phone calls, and WhatsApp.
- Provide an organised system for managing property listings.
- Track prospective buyers and their enquiries.
- Manage property inspection appointments.
- Build trust through professional presentation and property information.

---

## 2. Target Users

### 2.1 Public Visitors / Prospective Buyers

Visitors should be able to:

- Browse available properties.
- Search and filter listings.
- View detailed property information.
- View property images.
- Contact the agent through WhatsApp or phone.
- Submit property enquiries.
- Learn about the agent and the services offered.

**Important:** Visitors do not need to create accounts or register.

### 2.2 Administrator / Real Estate Agent

The administrator is the sole operator of the platform.

The administrator should be able to:

- Manage property listings.
- Upload property images and documents.
- Publish, unpublish, reserve, and mark properties as sold.
- Manage enquiries.
- Manage customer information.
- Schedule property inspections.
- Manage testimonials.
- Update website information and contact details.
- Monitor business activities through the dashboard.

The MVP will support only one administrator account.

---

## 3. Scope and Features

### 3.1 Public Website

#### A. Homepage

The homepage should include:

- Personal brand identity and logo.
- Hero section with a strong headline and call-to-action.
- Search or quick navigation to available properties.
- Featured properties.
- Recently added properties.
- Separate sections for land and buildings.
- Brief introduction about the agent.
- Reasons to work with the agent.
- Customer testimonials.
- WhatsApp and phone contact buttons.
- Footer with contact information and important links.

#### B. Properties Listing Page

Visitors should be able to browse all published property listings.

Each property card should display:

- Property cover image.
- Property title.
- Location.
- Price.
- Property category.
- Relevant property attributes.
- Availability status where applicable.
- Link to view full details.

**Search and filtering options:**

- Property category: Land or Building.
- Property subtype.
- State.
- Local Government Area (LGA).
- Neighbourhood.
- Keyword search.

The listing page should support pagination.

Only properties available for public display should appear in the available inventory.

#### C. Property Details Page

Each property should have an individual page containing:

**General information**
- Property title.
- Unique property reference.
- Description.
- Price.
- Property category.
- Listing status.
- State.
- LGA.
- Neighbourhood.
- Street address, where appropriate.

**Images**
- Property image gallery.
- Main cover image.
- Image navigation.

**Property-specific information**

For land:
- Plot size.
- Dimensions.
- Number of plots.
- Land use.
- Survey availability.
- Title type.
- Topography.

For buildings:
- Number of bedrooms.
- Bathrooms.
- Toilets.
- Parking spaces.
- Number of floors.
- Building condition.
- Additional features.

**Contact options**
- WhatsApp enquiry button.
- Direct phone call button.
- Website enquiry form.

**Other information**
- Related properties.
- Publicly approved property documents, if applicable.
- Clear availability status.

#### D. About Page

Should contain:

- Agent biography.
- Professional experience.
- Areas of operation.
- Services offered.
- Professional photograph.
- Contact call-to-action.

#### E. Contact Page

Should contain:

- Phone number.
- WhatsApp number.
- Email address.
- Contact form.
- Social media links.
- Service locations.

Enquiries submitted through the contact form should be recorded in the admin dashboard.

#### F. Legal Pages

- Privacy Policy.
- Terms and Conditions.
- Property information disclaimer.

---

### 3.2 Admin Dashboard

The dashboard should provide a central location for managing the agent's real estate business.

#### A. Authentication

The system should provide:

- Secure administrator login.
- Logout functionality.
- Password hashing.
- Session management.
- Login rate limiting.
- Protected admin routes.

There should be no public registration functionality.

The initial administrator account should be created through a controlled Laravel seeder or Artisan command.

#### B. Dashboard Overview

The dashboard should display operational summaries such as:

- Total properties.
- Published properties.
- Draft properties.
- Properties under negotiation.
- Sold properties.
- New enquiries.
- Upcoming inspections.

It should also display:

- Recent enquiries.
- Upcoming inspections.
- Recently updated properties.

Statistics must be calculated from actual database records, not hard-coded values.

#### C. Property Management

The administrator should be able to:

- Create new properties.
- Edit existing properties.
- Delete properties where appropriate.
- Save properties as drafts.
- Publish properties.
- Unpublish properties.
- Mark properties as reserved.
- Mark properties as sold.
- Archive properties.
- Upload multiple images.
- Select a cover image.
- Reorder images.
- Remove images.
- Upload property documents.
- Preview properties before publication.

**Property Information Fields**

| Category | Fields |
|---|---|
| General | Title, reference number, description, category, subtype |
| Listing | Listing type, price, currency, status |
| Location | State, LGA, neighbourhood, optional street address |
| Land | Plot size, dimensions, number of plots, land use, survey availability, title type, topography |
| Building | Bedrooms, bathrooms, toilets, parking, floors, condition, features |
| Media | Images, cover image, image order, optional video |
| Documents | Document type, file, visibility |
| Administration | Featured status, publication date, created date, updated date |

#### D. Property Categories

The system should support property categories such as:

- Land.
- Residential Properties.
- Commercial Properties.
- Other property types.

The category structure should be flexible enough to accommodate additional subtypes later.

#### E. Property Status Management

Every property should have a status.

| Status | Description |
|---|---|
| Draft | Property is being prepared and is not publicly visible. |
| Published | Property is available for public viewing. |
| Under Negotiation | A prospective buyer is negotiating. |
| Reserved | Property has been temporarily reserved. |
| Sold | Property has been successfully sold. |
| Unavailable | Property is temporarily unavailable. |
| Archived | Property is no longer actively marketed. |

Properties marked as sold, reserved, unavailable, or archived must not be presented as freely available inventory.

#### F. Enquiry Management

The system should capture enquiries submitted through the website.

The administrator should also be able to manually record enquiries received through phone calls and WhatsApp.

Each enquiry should contain:

- Customer name.
- Phone number.
- Email address, where available.
- Related property.
- Message.
- Enquiry source.
- Date received.
- Enquiry status.
- Internal notes.

**Enquiry statuses**

| Status | Description |
|---|---|
| New | Enquiry received but not yet followed up. |
| Contacted | Initial contact has been made. |
| Inspection Scheduled | An inspection has been arranged. |
| Negotiation | Discussions are ongoing. |
| Converted | Enquiry resulted in a successful business outcome. |
| Closed / Lost | Enquiry is no longer active. |

The administrator should be able to:

- View all enquiries.
- Filter enquiries by status.
- Search enquiries.
- Update enquiry status.
- Add internal notes.
- Contact customers through phone or WhatsApp.
- Associate enquiries with existing customers.

WhatsApp conversation synchronisation is not required for the MVP.

#### G. Customer Management

The administrator should be able to maintain customer records.

Customer information should include:

- Full name.
- Phone number.
- Email address.
- Notes.
- Related enquiries.
- Inspection history.

The system should support associating multiple enquiries with one customer.

#### H. Inspection Management

The administrator should be able to schedule property inspections.

Each inspection should contain:

- Customer.
- Property.
- Inspection date.
- Inspection time.
- Meeting location.
- Additional notes.
- Inspection status.

**Inspection statuses**

- Pending Confirmation.
- Confirmed.
- Completed.
- Cancelled.
- Rescheduled.

The dashboard should provide a view of upcoming inspections.

#### I. Property Documents

The administrator should be able to upload documents associated with individual properties.

Possible document types include:

- Certificate of Occupancy (C of O).
- Deed of Assignment.
- Survey Plan.
- Governor's Consent.
- Other supporting documents.

Each document should have a visibility setting:

- Private.
- Public.

Documents should be private by default.

Private documents must not be accessible through public URLs.

The platform stores documents supplied by the agent; it does not independently verify their authenticity or legal validity.

#### J. Testimonials Management

The administrator should be able to:

- Add testimonials.
- Edit testimonials.
- Publish testimonials.
- Unpublish testimonials.
- Delete testimonials.

Published testimonials may appear on the public homepage.

#### K. Website Settings

The administrator should be able to manage:

- Agent's name.
- Business name, if applicable.
- Logo.
- Profile photograph.
- Phone number.
- WhatsApp number.
- Email address.
- Social media links.
- Business/service locations.
- Homepage headline.
- Homepage description.
- About section content.

#### L. Activity Logs

The system should record important administrative activities, including:

- Property creation.
- Property updates.
- Property publication.
- Property status changes.
- Property deletion.
- Enquiry status updates.

---

## 4. Business Rules

### 4.1 Property Reference Numbers

Every property should receive a unique reference number.

Examples:

- `LAND-0001`
- `PROP-0001`
- `COMM-0001`

Reference numbers must not be reused when a property is deleted or archived.

### 4.2 Property Visibility

- Draft properties must not be publicly accessible.
- Published properties can appear in public listings.
- Reserved properties must not be presented as freely available.
- Sold properties must not appear in available inventory.
- Archived properties must not appear publicly.
- Private documents must remain inaccessible to public visitors.

### 4.3 Enquiry Management

- Website enquiries must be saved in the database.
- Every enquiry should have a source.
- An enquiry may be associated with a property.
- Phone and WhatsApp enquiries can be entered manually.
- The administrator should be able to update enquiry statuses.

### 4.4 Customer Records

- A customer may have multiple enquiries.
- An enquiry may be associated with an existing customer.
- The system should minimise unnecessary duplicate customer records.

### 4.5 Location Management

The MVP should support Nigerian:

- States.
- Local Government Areas.
- Neighbourhoods.

An interactive map is not required.

Optional latitude and longitude fields may be considered for future expansion.

---

## 5. Non-Functional Requirements

### 5.1 Security

- Protect all admin routes with authentication.
- Use secure password hashing.
- Implement CSRF protection.
- Validate all incoming requests.
- Escape output to prevent cross-site scripting.
- Rate-limit login attempts.
- Restrict uploaded file types and sizes.
- Store private documents securely.
- Keep sensitive credentials out of source control.
- Regenerate sessions after successful login.
- Invalidate sessions during logout.

### 5.2 Performance

- Optimise uploaded images.
- Use pagination for property listings and admin tables.
- Use database indexes for frequently queried fields.
- Avoid unnecessary database queries.
- Load related records efficiently.
- Use appropriate caching where beneficial.

### 5.3 Responsiveness

The website and dashboard must work across:

- Mobile phones.
- Tablets.
- Laptops.
- Desktop computers.

The public website should follow a mobile-first design approach.

### 5.4 Accessibility

- Use readable typography.
- Maintain sufficient colour contrast.
- Provide labels for form inputs.
- Include meaningful image alternative text.
- Ensure navigation is keyboard accessible.
- Display clear validation and error messages.

### 5.5 SEO

The public website should support:

- Page titles.
- Meta descriptions.
- Search-engine-friendly URLs.
- Image alt text.
- XML sitemap.
- Robots.txt.
- Appropriate indexing rules for published properties.

---

## 6. Technical Requirements

### 6.1 Recommended Technology Stack

| Component | Technology |
|---|---|
| Backend | Laravel 13 |
| Programming Language | PHP 8.3+ |
| Database | MySQL 8 |
| Frontend | Blade Templates |
| CSS Framework | Tailwind CSS 4 |
| JavaScript | Alpine.js |
| Asset Bundler | Vite |
| Package Manager | npm |
| Authentication | Laravel session authentication |
| File Storage | Laravel Storage; Cloudinary or S3-compatible storage for production |

### 6.2 Architecture

The application should follow a Laravel monolithic architecture.

The public website, admin dashboard, authentication and business logic will exist within one Laravel application.

A separate frontend application or API-first architecture is not necessary for the MVP.

### 6.3 Application Structure

Suggested structure:

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   ├── Public/
│   │   └── Auth/
│   ├── Middleware/
│   └── Requests/
├── Models/
├── Providers/
└── Services/

resources/
├── css/
│   └── app.css
├── js/
│   └── app.js
└── views/
    ├── layouts/
    │   ├── public.blade.php
    │   └── admin.blade.php
    ├── auth/
    ├── admin/
    ├── public/
    └── components/

routes/
└── web.php
```

### 6.4 Development Guidelines

- Use Laravel conventions.
- Use named routes.
- Use Eloquent relationships.
- Use Form Request classes for complex validation.
- Use reusable Blade components.
- Separate public and admin layouts.
- Keep controllers focused.
- Use service classes where they provide meaningful separation of business logic.
- Avoid unnecessary packages.
- Write tests for critical business workflows.

---

## 7. Suggested Database Entities

The following database entities are proposed for the application.

| Entity | Purpose |
|---|---|
| users | Administrator account. |
| property_categories | Property categories and subtypes. |
| properties | Core property listing information. |
| property_images | Images associated with properties. |
| property_documents | Documents associated with properties. |
| customers | Prospective buyer records. |
| enquiries | Website and manually recorded enquiries. |
| inspections | Property inspection appointments. |
| testimonials | Customer testimonials. |
| settings | Website and agent configuration. |
| activity_logs | Records of important administrative activities. |

### 7.1 Important Relationships

- A property has many images.
- A property has many documents.
- A property can have many enquiries.
- A property can have many inspections.
- A customer can have many enquiries.
- A customer can have many inspections.
- An enquiry can belong to a property.
- An enquiry can belong to a customer.
- Activity logs should identify the administrator responsible for an action.

---

## 8. User Interface and Design Direction

The overall design should communicate:

- Trust.
- Professionalism.
- Credibility.
- Simplicity.
- Premium real estate services.

### 8.1 Public Website

The public website should have:

- Clean layouts.
- High-quality property photography.
- Clear typography.
- Strong calls to action.
- Easy navigation.
- Consistent personal branding.
- Responsive property cards and image galleries.

### 8.2 Admin Dashboard

The dashboard should have:

- A professional sidebar navigation.
- Clear dashboard summaries.
- Simple data tables.
- Easy-to-use forms.
- Consistent buttons and status indicators.
- Clear success and error messages.
- Responsive navigation.

Suggested navigation:

1. Overview
2. Properties
3. Enquiries
4. Customers
5. Inspections
6. Documents
7. Testimonials
8. Website Settings

Only the Overview section needs to be functional during Phase 1.

---

## 9. MVP Acceptance Criteria

The MVP will be considered functionally complete when:

1. Visitors can access the public homepage.
2. Visitors can browse land and building listings.
3. Visitors can search and filter properties.
4. Visitors can view individual property details.
5. Visitors can contact the agent through WhatsApp and phone.
6. Visitors can submit website enquiries.
7. Enquiries are stored in the database.
8. The administrator can securely log in.
9. Unauthenticated users cannot access admin routes.
10. The administrator can create and manage property listings.
11. The administrator can upload property images.
12. The administrator can manage property documents and visibility.
13. The administrator can update property statuses.
14. The administrator can manage enquiries.
15. The administrator can manage customer records.
16. The administrator can schedule inspections.
17. The website and dashboard are responsive.
18. No public buyer registration is available.
19. No online payment functionality is included.

---

## 10. Project Development Phases

### Phase 1 — Application Foundation

**Objective:** Establish the Laravel application and core infrastructure.

Deliverables:

- Laravel project setup.
- MySQL database configuration.
- Tailwind CSS and Alpine.js setup.
- Vite configuration.
- Admin authentication.
- Admin route protection.
- Public website layout.
- Admin dashboard layout.
- Basic public homepage.
- Dashboard placeholder.

**Important:** Do not implement property management or enquiry functionality during this phase.

### Phase 2 — Property Management

**Objective:** Build the core property listing system.

Deliverables:

- Property database schema.
- Property categories.
- Property CRUD.
- Property image uploads.
- Property document management.
- Property statuses.
- Public property listing pages.
- Property detail pages.
- Search and filtering.

### Phase 3 — Lead and Business Management

**Objective:** Enable the agent to manage prospective buyers.

Deliverables:

- Enquiry management.
- Customer management.
- Enquiry status tracking.
- Internal notes.
- Inspection scheduling.
- Upcoming inspection dashboard.

### Phase 4 — Content Management and Launch Preparation

**Objective:** Prepare the platform for production.

Deliverables:

- Testimonials management.
- Website settings.
- SEO implementation.
- Activity logs.
- Security review.
- Performance optimisation.
- Testing.
- Database backup strategy.
- Production deployment.

---

## 11. Future Enhancements

The following features are outside the initial MVP but may be considered later:

- Interactive property maps.
- Location-based property discovery.
- Email and SMS notifications.
- WhatsApp Business API integration.
- Multiple agent accounts.
- Staff roles and permissions.
- Buyer accounts.
- Saved properties and favourites.
- Advanced CRM functionality.
- Lead conversion analytics.
- Property performance analytics.
- Document expiry reminders.
- Multi-language support.
- Multi-currency support.

---

## 12. Important Product Constraints

The development team or AI coding agent must respect the following decisions:

1. This is a personal real estate platform, not a multi-vendor marketplace.
2. The platform is designed for the Nigerian real estate market.
3. Both land and buildings must be supported.
4. There will be one administrator account in the MVP.
5. Buyers do not need to register or log in.
6. Transactions happen directly between the buyer and agent.
7. No online payment integration is required.
8. WhatsApp and phone are important contact channels.
9. Location filtering should support state, LGA and neighbourhood.
10. An interactive map is not required for the MVP.
11. The preferred stack is Laravel, MySQL, Blade, Tailwind CSS and Alpine.js.
12. The public website and admin dashboard should have separate layouts.
13. Development should follow the four phases outlined above.

---

## 13. Final Product Definition

The final product should function as a professional digital sales platform for an independent real estate agent.

The public website will help prospective buyers discover properties and initiate contact, while the private dashboard will allow the agent to manage listings, leads, customers, inspections and website content.

The primary goal is not simply to display properties online. It is to give the agent a structured digital system for attracting prospective buyers, managing enquiries and supporting the property sales process.

**Implementation principle:** Build the foundation first, deliver one complete workflow at a time, and avoid introducing features outside the agreed MVP scope.
