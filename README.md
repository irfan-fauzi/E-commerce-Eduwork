# Laravel 12 E-Commerce Boilerplate

Complete Laravel 12 e-commerce boilerplate with admin panel, user management, product catalog, shopping cart, checkout flow, and Midtrans payment gateway integration.

## Features

-   ✅ **User Authentication** - Laravel Breeze auth scaffolding
-   ✅ **Role-Based Access** - Admin/Customer separation via `is_admin` boolean
-   ✅ **Admin Dashboard** - Product CRUD, category management, transaction tracking
-   ✅ **Product Management** - Multiple images per product, categories with parent-child relationships
-   ✅ **Shopping Cart** - Add/update/remove items
-   ✅ **Address Book** - Customer addresses with primary selection
-   ✅ **Checkout Flow** - Cart → Transaction → Payment
-   ✅ **Midtrans Integration** - Snap payment gateway with webhook support
-   ✅ **Database Seeder** - Sample data (1 admin, 10 categories, 50 products, 5 users)
-   ✅ **Tailwind CSS** - Responsive UI with Blade templates
-   ✅ **Stock Management** - Transactional stock updates to prevent race conditions

-## Tech Stack

-   **Framework**: Laravel 12
-   **Frontend**: Blade Templates + Tailwind CSS
-   **Database**: MySQL
-   **Authentication**: Laravel Breeze
-   **Payment**: Midtrans Snap
-   **Build**: Vite

## Setup Instructions

### Prerequisites

-   PHP 8.1+
-   Composer
-   MySQL 8.0+
-   Node.js 16+
-   Midtrans account (for payment gateway)

### Installation Steps

#### 1. Clone & Install Dependencies

```bash
git clone <your-repo-url> eduwork-ecommerce
cd eduwork-ecommerce
composer install
npm install
```

#### 2. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=eduwork_ecommerce
DB_USERNAME=root
DB_PASSWORD=

MIDTRANS_IS_PRODUCTION=false
MIDTRANS_SERVER_KEY=your_midtrans_server_key
MIDTRANS_CLIENT_KEY=your_midtrans_client_key
```

#### 3. Database Setup

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```

#### 4. Run Development Server

**Terminal 1 - Build Assets**:

```bash
npm run dev
```

**Terminal 2 - Laravel Server**:

```bash
php artisan serve
```

Access the application:

-   **Frontend**: http://localhost:8000
-   **Admin**: http://localhost:8000/admin
-   **Default Admin Credentials**:
    -   Email: `admin@example.com`
    -   Password: `password`

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── ProductController.php
│   │   │   ├── CategoryController.php
│   │   │   └── TransactionController.php
│   │   ├── User/
│   │   │   ├── ProductController.php
│   │   │   ├── CartController.php
│   │   │   ├── CheckoutController.php
│   │   │   └── AddressController.php
│   │   ├── WebhookController.php
│   │   └── ProfileController.php
│   └── Middleware/
│       └── AdminMiddleware.php
├── Models/
│   ├── User.php
│   ├── Product.php
│   ├── Category.php
│   ├── Address.php
│   ├── CartItem.php
│   ├── Transaction.php
│   ├── TransactionItem.php
│   ├── ProductImage.php
│   └── Wishlist.php
└── Services/
    └── MidtransService.php

database/
├── migrations/
│   ├── 2025_11_27_000001_modify_users_table.php
│   ├── 2025_11_27_000002_create_addresses_table.php
│   ├── 2025_11_27_000003_create_categories_table.php
│   ├── 2025_11_27_000004_create_products_table.php
│   ├── 2025_11_27_000005_create_product_images_table.php
│   ├── 2025_11_27_000006_create_cart_items_table.php
│   ├── 2025_11_27_000007_create_wishlists_table.php
│   ├── 2025_11_27_000008_create_transactions_table.php
│   └── 2025_11_27_000009_create_transaction_items_table.php
├── factories/
│   ├── UserFactory.php
│   ├── CategoryFactory.php
│   └── ProductFactory.php
└── seeders/
    └── DatabaseSeeder.php

resources/
├── views/
│   ├── layouts/
│   │   ├── app.blade.php
│   │   ├── admin.blade.php
│   │   └── navigation.blade.php
│   ├── components/
│   │   └── admin-layout.blade.php
│   ├── user/
│   │   ├── products/
│   │   │   ├── index.blade.php
│   │   │   └── show.blade.php
│   │   ├── cart/
│   │   │   └── index.blade.php
│   │   ├── checkout/
│   │   │   ├── index.blade.php
│   │   │   └── payment.blade.php
│   │   └── addresses/
│   │       ├── index.blade.php
│   │       └── form.blade.php
│   └── admin/
│       ├── products/
│       │   ├── index.blade.php
│       │   └── form.blade.php
│       ├── categories/
│       │   ├── index.blade.php
│       │   └── form.blade.php
│       └── transactions/
│           ├── index.blade.php
│           └── show.blade.php

routes/
├── web.php
├── api.php
└── auth.php
```

---

## Database Schema

### Users

-   `id`, `name`, `email`, `password`, `phone`, `is_admin`, `timestamps`

### Addresses

-   `id`, `user_id`, `label`, `address_text`, `city`, `province`, `postal_code`, `is_primary`, `timestamps`

### Categories

-   `id`, `parent_id` (nullable), `name`, `slug`, `description`, `timestamps`

### Products

-   `id`, `category_id`, `sku`, `name`, `slug`, `short_description`, `description`, `price`, `stock`, `active`, `timestamps`

### Product Images

-   `id`, `product_id`, `url`, `alt_text`, `position`, `timestamps`

### Cart Items

-   `id`, `user_id`, `product_id`, `quantity`, `added_at` (unique: user_id + product_id)

### Transactions

-   `id`, `user_id`, `address_id`, `status` (pending/paid/shipped/completed/cancelled), `total_amount`, `shipping_fee`, `payment_method`, `payment_gateway_id`, `timestamps`, `paid_at`

### Transaction Items

-   `id`, `transaction_id`, `product_id`, `product_sku`, `product_name`, `quantity`, `price`, `timestamps`

### Wishlists

-   `id`, `user_id`, `product_id`, `timestamps` (unique: user_id + product_id)

---

## API Routes

### Public Routes

-   `GET /` - Product listing
-   `GET /products/{slug}` - Product detail

### User Routes (requires auth)

-   `GET /user/dashboard` - User dashboard
-   `GET /user/cart` - View cart
-   `POST /user/cart` - Add to cart
-   `PATCH /user/cart/{id}` - Update cart item
-   `DELETE /user/cart/{id}` - Remove from cart
-   `GET /user/addresses` - List addresses
-   `POST /user/addresses` - Create address
-   `PATCH /user/addresses/{id}` - Update address
-   `DELETE /user/addresses/{id}` - Delete address
-   `GET /user/checkout` - Checkout page
-   `POST /user/checkout/pay` - Process payment

### Admin Routes (requires is_admin)

-   `GET /admin/dashboard` - Admin dashboard
-   `GET /admin/products` - Product list
-   `GET /admin/products/create` - Create product form
-   `POST /admin/products` - Store product
-   `GET /admin/products/{id}/edit` - Edit product form
-   `PATCH /admin/products/{id}` - Update product
-   `DELETE /admin/products/{id}` - Delete product
-   `GET /admin/categories` - Category list
-   `POST /admin/categories` - Create category
-   `PATCH /admin/categories/{id}` - Update category
-   `DELETE /admin/categories/{id}` - Delete category
-   `GET /admin/transactions` - Transaction list
-   `GET /admin/transactions/{id}` - Transaction detail
-   `PATCH /admin/transactions/{id}` - Update transaction status

### Webhook

-   `POST /webhook/midtrans` - Midtrans payment notification

---

## Checkout & Payment Flow

### 1. Add to Cart

```
POST /user/cart
{
  "product_id": 1,
  "quantity": 2
}
```

### 2. View Cart

```
GET /user/cart
```

### 3. Checkout

```
GET /user/checkout
```

Displays cart items, addresses, and shipping options.

### 4. Pay

```
POST /user/checkout/pay
{
  "address_id": 1,
  "shipping_fee": 10000
}
```

Returns Midtrans Snap payment page with snap_token.
Note: The application records the Midtrans `payment_type` (e.g., `bank_transfer`, `gopay`, `credit_card`) into the `payment_method` column, and records the Midtrans `transaction_id` into `gateway_reference`.

Note: If Midtrans keys are missing or there is an issue contacting the Midtrans API, the application will log the error and show a friendly message to the user. There is no fallout to production: no simulated tokens or simulated payments are generated. Use proper Midtrans sandbox or production keys in `.env` to test and run payments.

This project now uses Notiflix (loading/notification) for Midtrans popup loading state. To ensure the UI shows a spinner and notifications when the Midtrans popup is auto-opened, install frontend dependencies and build assets:

```bash
npm install
npm run dev
```

### 5. Midtrans Snap Payment

User is redirected to Midtrans Snap to complete payment. After payment:

-   **Success**: Transaction status → `paid`
-   **Pending**: Transaction status → `pending`
-   **Failed**: Transaction status → `cancelled`

### 6. Webhook Notification

```
POST /webhook/midtrans
```

Midtrans sends notification to update transaction status.

---

## Admin Panel Features

### Products Management

-   Create, read, update, delete products
-   Upload multiple images per product
-   Set category, price, stock
-   Mark products as active/inactive

### Categories Management

-   Create, read, update, delete categories
-   Support parent-child category relationships
-   List all products in category

### Transaction Management

-   View all transactions with customer details
-   View transaction items and total
-   Update transaction status (pending → paid → shipped → completed)
-   View customer address and payment details

---

## Midtrans Configuration

### 1. Get Midtrans Keys

1. Go to https://dashboard.midtrans.com
2. Log in or create account
3. Go to Settings → Access Keys
4. Copy Server Key and Client Key

### 2. Set Environment Variables

```env
MIDTRANS_IS_PRODUCTION=false  # true for production
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
```

### 3. Webhook Setup (for production)

1. Go to Settings → Webhooks
2. Add webhook URL: `https://yourdomain.com/webhook/midtrans`
3. Select events: transaction.status.change

---

## File Upload & Storage

Product images are stored in `storage/app/public/products/`. To make them accessible:

```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public`.

Images are accessible at: `/storage/products/filename.jpg`

---

## Authentication

The project uses **Laravel Breeze** for authentication. Features:

-   User registration
-   Login/logout
-   Password reset
-   Email verification (optional)

To disable email verification, remove it from the User model.

---

## Authorization

### Admin Middleware

The `AdminMiddleware` checks if `auth()->user()->is_admin === true`.

Usage in routes:

```php
Route::middleware(['auth', 'admin'])->group(function () {
    // Admin routes
});
```

---

## Error Handling

### Common Issues

**1. "Unknown column 'phone' in 'where clause'"**

-   Run migrations: `php artisan migrate`

**2. "Class 'Midtrans\Snap' not found"**

-   Install Midtrans package: `composer require midtrans/midtrans-php`

**3. Images not loading after upload**

-   Run: `php artisan storage:link`
-   Check that `storage/app/public/products/` directory exists

**4. Payment page not loading**

-   Verify Midtrans keys in `.env`
-   Check Midtrans is set to sandbox mode for development

---

## Customization

### Change Default Shipping Fee

Edit `resources/views/user/checkout/index.blade.php`:

```php
<input type="number" name="shipping_fee" value="10000" min="0">
```

### Add More Payment Methods

Extend `MidtransService.php` to support additional gateways.

### Customize Email Templates

Email templates are in `resources/views/emails/`.

---

## Performance Optimization

-   Products are paginated (12 per page)
-   Transactions are paginated (20 per page)
-   Database queries use eager loading (`with()`)
-   Stock updates use atomic database transactions

---

## Security Considerations

-   ✅ CSRF protection on all POST/PATCH/DELETE routes
-   ✅ SQL injection prevention via Eloquent ORM
-   ✅ Password hashing with Bcrypt
-   ✅ Role-based access control (is_admin)
-   ✅ Midtrans signature verification on webhooks
-   ⚠️ For production: Enable HTTPS, set `APP_DEBUG=false`, use strong DB passwords

---

## Deployment

### 1. Prepare Server

```bash
# SSH into server
php -v  # PHP 8.1+
composer --version
node -v && npm -v
```

### 2. Clone & Setup

```bash
cd /var/www
git clone <your-repo> eduwork-ecommerce
cd eduwork-ecommerce
composer install --no-dev
npm install && npm run build
cp .env.example .env
php artisan key:generate
```

### 3. Configure Environment

```bash
# Edit .env with production values
nano .env
```

### 4. Database

```bash
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
```

### 5. Permissions

```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data /var/www/eduwork-ecommerce
```

### 6. Web Server (Nginx)

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/eduwork-ecommerce/public;

    index index.html index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

---

## Testing

### Run Tests

```bash
php artisan test
```

### Create Test Data

```bash
php artisan tinker
>>> $user = User::factory()->admin()->create();
>>> $category = Category::factory()->create();
>>> Product::factory(10)->for($category)->create();
```

---

## Contributing

1. Create a feature branch: `git checkout -b feature/amazing-feature`
2. Commit changes: `git commit -m 'Add amazing feature'`
3. Push: `git push origin feature/amazing-feature`
4. Open a Pull Request

---

## License

This project is open-source software licensed under the MIT license.

---

## Support

For issues, questions, or suggestions:

1. Check existing GitHub issues
2. Create a new issue with details
3. Join Laravel community forums

---

## Changelog

### v1.0.0 (November 2025)

-   ✅ Initial e-commerce boilerplate release
-   ✅ Admin panel with product/category/transaction management
-   ✅ User authentication with Laravel Breeze
-   ✅ Shopping cart and checkout flow
-   ✅ Midtrans payment gateway integration
-   ✅ Responsive Tailwind UI
-   ✅ Database seeder with sample data

---

**Built with ❤️ using Laravel 12**

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

-   **[Vehikl](https://vehikl.com)**
-   **[Tighten Co.](https://tighten.co)**
-   **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
-   **[64 Robots](https://64robots.com)**
-   **[Curotec](https://www.curotec.com/services/technologies/laravel)**
-   **[DevSquad](https://devsquad.com/hire-laravel-developers)**
-   **[Redberry](https://redberry.international/laravel-development)**
-   **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Panduan Fork → Clone → Branch → Commit → Push → Pull Request

---

## ⚡ 1. Fork Repository

Jika repository **bukan** milik Anda:

1. Buka repository sumber di GitHub.
2. Klik tombol **Fork** (kanan atas).
3. Pilih akun Anda.

Hasilnya: Anda memiliki salinan repo tersebut di akun GitHub Anda.

---

## ⚡ 2. Clone Repository Fork

Clone repository fork ke komputer lokal:

```bash
git clone https://github.com/Nama Repository Fork Anda/E-commerce-Eduwork.git
cd E-commerce-Eduwork
```

---

## ⚡ 3. Tambahkan Remote `upstream` (Jika Repo hasil Fork)

Digunakan untuk mendapatkan update dari repository sumber.

```bash
git remote add upstream https://github.com/ORIGINAL_OWNER/E-commerce-Eduwork.git
git remote -v
```

> Jika repository utama adalah milik Anda sendiri, langkah ini tidak perlu dilakukan.

---

## ⚡ 4. Update Branch `main`

Selalu lakukan update sebelum mulai mengerjakan fitur:

```bash
git checkout main
git pull origin main
```

Jika repo menggunakan upstream:

```bash
git fetch upstream
git merge upstream/main
git push origin main
```

---

## ⚡ 5. Buat Branch Baru

Jangan pernah bekerja langsung di branch `main`.

```bash
git checkout -b feature/nama-fitur
```

Contoh:

```bash
git checkout -b feature/login-validation
```

---

## ⚡ 6. Lakukan Perubahan Kode

Edit atau tambahkan file sesuai kebutuhan.

---

## ⚡ 7. Cek Perubahan

```bash
git status
```

---

## ⚡ 8. Stage Perubahan

Tambahkan semua perubahan:

```bash
git add -A
```

Atau file tertentu:

```bash
git add path/ke/file
```

---

## ⚡ 9. Commit dengan Format yang Benar

Gunakan format commit singkat, jelas, dan terstruktur.

```bash
git commit -m "feat(auth): add login form validation"
```

### Format commit:

```
type(scope): message
```

### Tipe umum commit:

-   **feat** → fitur baru
-   **fix** → perbaikan bug
-   **refactor** → perbaikan kode tanpa mengubah fungsi
-   **style** → formatting, layout
-   **docs** → dokumentasi
-   **chore** → konfigurasi, non-code

---

## ⚡ 10. Push Branch ke Repo Fork

```bash
git push origin feature/login-validation
```

Branch akan muncul di GitHub.

---

## ⚡ 11. Buat Pull Request (PR)

1. Buka GitHub → tampil tombol **Compare & pull request**.
2. Pastikan:

    - **base repo** = repository sumber
    - **base branch** = `main` / `develop`
    - **compare** = branch fitur Anda

3. Isi deskripsi PR:

    - Perubahan apa
    - Alasan
    - Cara menguji perubahan

Klik **Create pull request**.

---

## ⚡ 12. Sinkronisasi Fork Jika Ada Update

Untuk update branch `main`:

```bash
git checkout main
git fetch upstream
git merge upstream/main
git push origin main
```

Update pada branch fitur:

```bash
git checkout feature/login-validation
git merge main
git push origin feature/login-validation
```

---

## ⚡ 13. Jika Terjadi Konflik

Jika Git menampilkan tanda konflik:

```
<<<<< HEAD
```

Selesaikan konflik, lalu:

```bash
git add -A
git commit
git push
```

PR akan diperbarui otomatis.

---

## ⚡ 14. Ringkasan Super Singkat

```
Fork → Clone → Add upstream → Update main →
Buat branch → Coding → add → commit → push → PR
```
