# Jessi's ClassiQ Store

An exquisite, full-featured boutique e-commerce web application for **Jessi's ClassiQ Store** (clothing, designer bags, and statement shoes).

---

## Brand Palette & Aesthetics
Built with a boutique luxury design language, refined typography, and cohesive styling:
- **`brand-pink` (`#e06383`)**: Primary boutique rose pink for accents, highlights, and primary actions.
- **`brand-pink-dark` (`#ab3252`)**: Deep rose for active states, badges, and focal points.
- **`brand-charcoal` (`#18191d`)**: Premium deep charcoal neutral for headers, footers, and contrasting elements.
- **`brand-cream` (`#faf7f2`)**: Warm luxury backdrop tint.
- **`brand-gold` (`#d4af37`)**: Subtle metallic accents for premium detailing.

---

## User Roles & Default Test Credentials

| Role | Email | Password | Access / Permissions |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@jessiclassiq.com` | `password123` | Full access: stock, sales, staff, payroll, OCR ledger, reports |
| **Staff** | `staff@jessiclassiq.com` | `password123` | Store operations: inventory, orders, offline sales |
| **Customer** | `customer@jessiclassiq.com` | `password123` | Storefront browsing, cart, Paystack checkout, order history |

---

## Phase 1 Deliverables (Completed)
- [x] **Laravel 11 Project Scaffolded**: Clean setup with MySQL database `jessi_store`.
- [x] **Tailwind CSS & Vite**: Configured with custom boutique pink color scheme and fonts.
- [x] **Authentication & RBAC**: Laravel Breeze (Blade + Alpine.js) combined with `spatie/laravel-permission` for role authorization (`admin`, `staff`, `customer`).
- [x] **Complete 13-Table Database Schema & Eloquent Models**:
  - `users` (with `role`, `phone`, `address`, `city`)
  - `categories` (Clothes, Bags, Shoes — with parent/nesting support)
  - `products` (with slug, category_id, base_price, images, brand_tags, status)
  - `product_variants` (size, color, sku, stock_quantity, price_override)
  - `orders` & `order_items`
  - `payments` (Paystack reference, status, channel, currency, amounts)
  - `staff` (linked to users, position, bank details, Paystack recipient codes, monthly salary)
  - `salary_payments` (staff_id, period, Paystack transfer code, status, paid_at)
  - `stock_movements` (product_variant_id, type: in/out/adjustment, quantity, reason, audit)
  - `ocr_import_batches` (image_path, raw_text, parsed_data, review status)
  - `sales_records` (for digitized paper ledger entries & system sales)
  - `shop_locations` (Lagos Victoria Island boutique coordinates, address, phone)
- [x] **Database Seeders**: Seeded roles, permissions, admin/staff/customer accounts, initial shop location, categories, products, and variant stock logs.
- [x] **Boutique Front-End**: Branded welcome storefront, responsive boutique navigation, role badges, luxury cards, and user dashboard.
- [x] **Automated Test Suite**: 29 passing tests (86 assertions) covering auth, registration, storefront rendering, and model relationships.

---

## Phase 2 Deliverables (Completed)
- [x] **Curated Atelier Catalog (`/products`)**: Dynamic filtering by category, search keywords (`q`), min/max price bounds, size, color, stock availability, and multiple sort orders with pagination.
- [x] **Product Detail Experience (`/products/{slug}`)**: High-res image gallery, reactive variant selection with Alpine.js, dynamic SKU/price/stock updates, and related item recommendations.
- [x] **Shopping Bag Cart Flow (`/cart`)**: Real-time cart state via `CartService`, quantity adjustments, stock-limit guards, courier fee computation, and clear-bag capability.
- [x] **Paystack Online Checkout (`/checkout`)**: Multi-step checkout with customer contact and courier delivery inputs, Paystack payment initialization, test sandbox simulator (`/checkout/simulate-paystack`), and server-side verification callback (`/checkout/callback`).
- [x] **Order Fulfillment & Stock Audits**: Automatic order and payment creation, automated stock decrement, and traceable `StockMovement` records upon verified transaction.
- [x] **Order Confirmation & Printable Invoices (`/orders/confirmation/{orderNumber}`)**: Luxury branded invoice receipt with print button, order tracking status, item breakdown, and delivery destination details.
- [x] **Interactive Boutique Store Locator (`/store-location`)**: OpenStreetMap + Leaflet interactive map with custom boutique marker, browser geolocation, and Haversine distance calculation.
- [x] **Full Automated Phase 2 Test Suite**: 6 feature tests (59 assertions) verifying catalog filtering, cart lifecycle, checkout payment callbacks, stock audit movements, and store locator rendering.

---

## Phase 3 Deliverables (Completed)
- [x] **Protected Admin & Staff Portal (`/admin`)**: Strict RBAC protection denying guests & customer roles with 403 Forbidden checks via `EnsureUserIsAdminOrStaff` middleware.
- [x] **Atelier Executive Dashboard**: Live revenue analytics (Today's Revenue, Inventory Valuation, Active Orders count), real-time pulsing low-stock alert badges (&le; 5 units), and recent order activity feeds.
- [x] **Product Inventory & Variant Management (`/admin/products`)**: Filterable product catalog, creation form with category selection, automated slugification, and dynamic multi-variant creation (SKU, size, color, stock, price overrides) with automated `StockMovement` logging.
- [x] **Stock Management & Audit Movement Trail (`/admin/stock`)**: Searchable variant stock levels, restock/audit adjustment modal (`/admin/stock/adjust`), and immutable audit trail logging who, when, why, previous stock, and new stock.
- [x] **Order Fulfillment Management (`/admin/orders`)**: Complete order listing with status tags (`pending`, `processing`, `shipped`, `delivered`, `cancelled`), detailed invoice inspection, and one-click fulfillment status updates.
- [x] **In-Store Offline POS Terminal (`/admin/sales/create`)**: Fast walk-in sales recording interface with dynamic price calculation, multi-payment support (In-Store POS Terminal, Cash, Bank Transfer), automated stock decrement, and `SalesRecord` entry.
- [x] **Financial Reports & CSV Export (`/admin/reports`)**: Date-ranged financial reporting comparing online vs in-store offline sales, inventory valuation, and streaming CSV data export (`/admin/reports/export`).
- [x] **Full Automated Phase 3 Test Suite**: 7 feature tests (49 assertions) verifying security, dashboard KPIs, product/variant creation, stock adjustments, order status updates, POS checkout, and CSV reporting.

---

## Phase 4 Deliverables (Completed)
- [x] **Personnel & Staff Directory (`/admin/staff`)**: Full team management interface with position, monthly salary, bank account number, Paystack recipient code generation, and active/inactive status toggle.
- [x] **Paystack Recipient Integration**: Automatically creates transfer recipients (`RCP_xxx`) via `PaystackService::createTransferRecipient` using Nigerian bank codes and 10-digit NUBAN numbers.
- [x] **Paystack Salary Transfers Payroll (`/admin/payroll`)**: Dedicated compensation control center with monthly period selector (`YYYY-MM`), commitment KPI totals, and paid vs pending indicators.
- [x] **Batch & Single Salary Disbursal (`/admin/payroll/disburse`)**: Direct bank account payouts via `PaystackService::initiateTransfer`, automated creation of `SalaryPayment` records, duplicate disbursal prevention, and error handling.
- [x] **Transfer Retry Action (`/admin/payroll/retry/{id}`)**: One-click re-initiation for failed salary payments.
- [x] **Full Automated Phase 4 Test Suite**: 7 feature tests (55 assertions) verifying admin-only security gates, staff onboarding with Paystack recipient codes, individual & batch salary disbursements, double-payout guards, and transfer retries.

---

## Phase 5 Deliverables (Completed)
- [x] **Physical Daybook & Ledger Upload (`/admin/ocr-imports/upload`)**: Upload interface supporting paper ledger photos (JPG, PNG, WebP, PDF up to 10MB) with drag-and-drop and manual text fallback.
- [x] **Intelligent OCR Extraction Service (`OcrService`)**: Integrates with system Tesseract CLI binary when available and fallback heuristic tabular parser that detects dates, item descriptions, quantities, unit prices, total amounts, and payment methods.
- [x] **Smart Inventory Variant Matching**: Automated matching of parsed ledger lines to active boutique inventory variants (`ProductVariant`) by product name, size, and color.
- [x] **Side-by-Side Reconciliation Workbench (`/admin/ocr-imports/{id}/review`)**: High-contrast split view displaying the original scanned document/raw text alongside an interactive Alpine.js editable table for real-time adjustments before committing.
- [x] **Automated Stock Decrements & Audit Logging**: Committing reconciled ledger lines creates permanent `SalesRecord` entries, decrements physical stock on matched variants, and records traceable `StockMovement` logs.
- [x] **Committed Batch Inspector (`/admin/ocr-imports/{id}`)**: Detailed review of completed ledger sheets with linked sales records, financial totals, and direct integration into financial reports.
- [x] **Full Automated Phase 5 Test Suite**: 6 feature tests (44 assertions) verifying photo uploads, raw text overrides, side-by-side reconciliation, stock decrements, and sales records creation.

---

## Local Setup & Installation

### 1. Requirements
- PHP 8.2+ with `pdo_mysql`, `mbstring`, `openssl`, `curl` extensions
- MySQL / MariaDB (running on `127.0.0.1:3306`)
- Node.js 20+ & npm
- Composer 2+

### 2. Quickstart Commands
```bash
# 1. Clone or navigate to the project directory
cd /Users/ruelnuffy/Documents/shopmanager

# 2. Install PHP and Node dependencies
composer install
npm install

# 3. Environment configuration
cp .env.example .env
php artisan key:generate

# 4. Configure Database in .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=jessi_store
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Run Migrations & Seed Default Data
php artisan migrate:fresh --seed

# 6. Build Frontend Assets
npm run build

# 7. Start the Local Server
php artisan serve --port=8000
```

Visit the application at: `http://127.0.0.1:8000`

---

## Running Automated Tests
```bash
php artisan test
```

---

## Upcoming Phases Roadmap
- **Phase 2 — Storefront & Customer Journey** (Completed)
- **Phase 3 — Admin Panel Core & POS** (Completed)
- **Phase 4 — Staff & Paystack Payroll** (Completed)
- **Phase 5 — OCR Ledger Import** (Completed)

All 5 project phases for Jessi's ClassiQ Store have been built, verified, tested, and styled.

---

## Background Queue Workers on Shared Hosting / cPanel

For shared hosting environments (such as cPanel) without persistent Supervisor daemon processes, background queue jobs (OCR document processing, batch payroll disbursements) are executed via the Laravel Scheduler.

### 1. cPanel Cron Configuration
Add a single cron job in the cPanel **Cron Jobs** manager running every minute:

```bash
* * * * * cd /home/username/public_html && php artisan schedule:run >> /dev/null 2>&1
```
*(Replace `/home/username/public_html` with your actual project root path).*

### 2. Scheduled Worker Execution
In `routes/console.php`, the scheduler automatically runs:
```bash
php artisan queue:work --stop-when-empty --max-time=50
```
This processes queued jobs on demand, exits cleanly when empty or after 50 seconds, with a 10-minute mutex expiry to prevent deadlocks.

### 3. Migrating Legacy OCR Ledgers to Private Storage
To migrate existing OCR ledger sheets from the legacy `public` disk to tenant-isolated `private` storage (`storage/app/private/tenants/{tenant_id}/ledger_uploads/`), run:

```bash
# Preview files without modifying storage or database:
php artisan ocr:migrate-ledgers-to-private --dry-run

# Execute migration:
php artisan ocr:migrate-ledgers-to-private
```
This command copies public ledger images to private tenant paths, verifies integrity, updates database records, and safely removes public copies. It is idempotent and can be safely re-run.
