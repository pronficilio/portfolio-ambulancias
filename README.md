# Medevac Ambulancias

Legacy PHP web application for Medevac Ambulancias, combining a public services website with an administrative sales, CRM, and inventory panel.

## Overview

This repository contains two closely related surfaces: a public-facing website for ambulance and medical support services, and a password-protected administration panel under `panel/`.

The public site is built as a multi-page HTML/CSS/JavaScript experience with AJAX-backed forms, email delivery, and an embedded chat widget. The admin area adds catalog management, product and inventory workflows, shopping carts, point-of-sale operations, customer records, supplier access, scheduling, and permissions.

Repository-root screenshots such as `ambulancias (1).png` through `ambulancias (4).png` show the public homepage, gallery, and invoice-request flow.

## Key Features

- Multi-page public website for emergency care, transfers, neonatal therapy, event coverage, medical consultation, training, corporate outsourcing, gallery, and client-facing informational pages.
- AJAX-powered feedback and invoice-request forms routed through PHP mail handlers.
- Embedded chat widget with availability checks, live message polling, and fallback inquiry capture when operators are offline.
- Role-aware admin panel with login, session handling, and permission-gated tabs and actions.
- Catalog and product management for categories, subcategories, discounts, suppliers, products, images, files, pricing, and inventory thresholds.
- Cart and point-of-sale workflows with barcode input, mixed payment methods, invoice flags, saved/open carts, and sales reporting.
- CRM-style customer management with billing/shipping data, phone records, follow-up calls, scheduling, and spreadsheet import.

## Tech Stack

- Languages: PHP, JavaScript, HTML, CSS
- Frontend: Bootstrap, jQuery, DataTables, Select2, FullCalendar, jsTree, Intro.js, Toastr, Gritter
- Backend: Custom PHP, PHPMailer, PHPExcel
- Database: MySQL
- Other integrations: Google Analytics

## Architecture

- `index.html` and the sibling `*.html` files implement the public site pages.
- `css/`, `js/`, `images/`, and `fonts/` provide shared static assets for the public site.
- `php/` and `correo/` handle form/email workflows for public-facing interactions.
- `achat/` contains the public chat widget and its PHP endpoints.
- `panel/` contains the admin login, dashboard shell, tab content, AJAX endpoints, business classes, and bundled UI libraries.
- `panel/admin/classes/` holds the main business logic for access control, customers, products, carts, suppliers, files, calls, agenda items, and chat.
- `Site-V-1.0/` and `test/` appear to be alternate or older site copies rather than automated test suites.

## Getting Started

### Prerequisites

- A PHP-capable web server
- A MySQL database with the expected application tables
- Outbound SMTP access for email-based features

### Local Setup

This repository does not include a one-command local setup or package-manager scripts.

To run it locally:

1. Serve the project from a PHP-capable web root so the public pages and PHP endpoints are reachable.
2. Configure the database connection used by `panel/admin/classes/permisos.php`.
3. Ensure mail-related PHP endpoints can reach an SMTP server before testing invoice, opinion, password-recovery, or chat-notification flows.
4. Open `index.html` for the public site or `panel/index.html` for the admin login screen.

## Configuration

No `.env.example` or environment-variable-based configuration was found in this repository.

Database, mail, and site-level settings are embedded directly in PHP files such as:

- `panel/admin/classes/permisos.php`
- `panel/admin/classes/Acceso.class.php`
- `php/mail.php`
- `achat/ajax/iniciaChat.php`

Do not reuse embedded credentials from legacy code in a public deployment.

## Notes / Trade-offs

- This is a legacy PHP codebase that still uses APIs such as `mysql_*`, `ereg/eregi`, and `get_magic_quotes_gpc`, so a modern PHP runtime will likely require compatibility work.
- No CI workflow, package manifest, Docker setup, or automated test runner configuration was found in this copy of the project.
- One admin include references `panel/admin/classes/usuarios.class.php`, but that file is not present in this workspace.

## Contact

Replace this section with your preferred public portfolio contact channel.
