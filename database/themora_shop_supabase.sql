-- THEMORA SHOP — PostgreSQL Schema for Supabase
-- Run this in the Supabase SQL Editor

-- 1. Enable UUID extension (optional but good practice)
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- 2. Create ENUM types (Postgres requires explicit ENUMS)
DO $$ BEGIN
    CREATE TYPE user_role AS ENUM ('user', 'editor', 'support', 'super_admin');
    CREATE TYPE product_status AS ENUM ('active', 'inactive', 'draft');
    CREATE TYPE coupon_type AS ENUM ('flat', 'percent');
    CREATE TYPE payment_gateway AS ENUM ('razorpay', 'stripe', 'paypal', 'upi', 'free');
    CREATE TYPE order_status AS ENUM ('pending', 'paid', 'failed', 'refunded', 'disputed');
    CREATE TYPE ticket_status AS ENUM ('open', 'in-progress', 'resolved', 'closed');
    CREATE TYPE ticket_priority AS ENUM ('low', 'medium', 'high', 'urgent');
    CREATE TYPE payout_method AS ENUM ('upi', 'bank', 'paypal');
    CREATE TYPE payout_status AS ENUM ('pending', 'approved', 'paid', 'rejected');
    CREATE TYPE campaign_status AS ENUM ('draft', 'scheduled', 'sent');
EXCEPTION
    WHEN duplicate_object THEN null;
END $$;

-- 3. Create Tables
CREATE TABLE IF NOT EXISTS users (
    id              SERIAL PRIMARY KEY,
    name            VARCHAR(120) NOT NULL,
    email           VARCHAR(191) NOT NULL UNIQUE,
    password        VARCHAR(255),
    avatar          VARCHAR(255),
    bio             TEXT,
    role            user_role DEFAULT 'user',
    google_id       VARCHAR(100),
    github_id       VARCHAR(100),
    totp_secret     VARCHAR(64),
    is_2fa_enabled  BOOLEAN DEFAULT FALSE,
    is_blocked      BOOLEAN DEFAULT FALSE,
    referral_code   VARCHAR(20) UNIQUE,
    referred_by     INTEGER REFERENCES users(id) ON DELETE SET NULL,
    email_verified  BOOLEAN DEFAULT FALSE,
    notif_order_success   BOOLEAN DEFAULT TRUE,
    notif_download_expiry BOOLEAN DEFAULT TRUE,
    notif_newsletter      BOOLEAN DEFAULT FALSE,
    notif_affiliate       BOOLEAN DEFAULT TRUE,
    created_at      TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    id          SERIAL PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    slug        VARCHAR(120) NOT NULL UNIQUE,
    icon        VARCHAR(60),
    sort_order  INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS products (
    id              SERIAL PRIMARY KEY,
    title           VARCHAR(255) NOT NULL,
    slug            VARCHAR(300) NOT NULL UNIQUE,
    description     TEXT,
    short_desc      TEXT,
    price           DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    original_price  DECIMAL(10,2),
    category_id     INTEGER REFERENCES categories(id) ON DELETE SET NULL,
    file_path       VARCHAR(500),
    preview_file    VARCHAR(500),
    demo_video_url  VARCHAR(500),
    thumbnail       VARCHAR(500),
    status          product_status DEFAULT 'active',
    download_limit  INTEGER DEFAULT 5,
    flash_sale_price    DECIMAL(10,2),
    flash_sale_ends     TIMESTAMP WITH TIME ZONE,
    meta_title      VARCHAR(255),
    meta_desc       TEXT,
    total_sales     INTEGER DEFAULT 0,
    avg_rating      DECIMAL(3,2) DEFAULT 0.00,
    version         VARCHAR(20) DEFAULT '1.0',
    created_at      TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Full-text search index for products (Postgres uses tsvector)
CREATE INDEX IF NOT EXISTS idx_products_search ON products USING GIN (to_tsvector('english', title || ' ' || COALESCE(description, '') || ' ' || COALESCE(short_desc, '')));

CREATE TABLE IF NOT EXISTS product_images (
    id          SERIAL PRIMARY KEY,
    product_id  INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    image_path  VARCHAR(500) NOT NULL,
    sort_order  INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS product_tags (
    id          SERIAL PRIMARY KEY,
    product_id  INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    tag         VARCHAR(80) NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_tag ON product_tags(tag);

CREATE TABLE IF NOT EXISTS coupons (
    id          SERIAL PRIMARY KEY,
    code        VARCHAR(60) NOT NULL UNIQUE,
    type        coupon_type DEFAULT 'percent',
    value       DECIMAL(10,2) NOT NULL,
    min_order   DECIMAL(10,2) DEFAULT 0,
    max_uses    INTEGER DEFAULT 0,
    used_count  INTEGER DEFAULT 0,
    product_id  INTEGER REFERENCES products(id) ON DELETE SET NULL,
    expiry      TIMESTAMP WITH TIME ZONE,
    is_active   BOOLEAN DEFAULT TRUE,
    created_at  TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
    id              SERIAL PRIMARY KEY,
    user_id         INTEGER REFERENCES users(id) ON DELETE SET NULL,
    guest_email     VARCHAR(191),
    guest_name      VARCHAR(120),
    subtotal        DECIMAL(10,2) NOT NULL,
    discount        DECIMAL(10,2) DEFAULT 0,
    tax             DECIMAL(10,2) DEFAULT 0,
    total           DECIMAL(10,2) NOT NULL,
    coupon_id       INTEGER REFERENCES coupons(id) ON DELETE SET NULL,
    payment_gateway payment_gateway DEFAULT 'razorpay',
    transaction_id  VARCHAR(255),
    status          order_status DEFAULT 'pending',
    is_disputed     BOOLEAN DEFAULT FALSE,
    notes           TEXT,
    created_at      TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_items (
    id              SERIAL PRIMARY KEY,
    order_id        INTEGER NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    product_id      INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    price           DECIMAL(10,2) NOT NULL,
    download_token  VARCHAR(128) UNIQUE,
    download_count  INTEGER DEFAULT 0,
    max_downloads   INTEGER DEFAULT 5,
    token_expires   TIMESTAMP WITH TIME ZONE
);

CREATE TABLE IF NOT EXISTS reviews (
    id          SERIAL PRIMARY KEY,
    product_id  INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    user_id     INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    rating      SMALLINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    body        TEXT,
    media_path  VARCHAR(500),
    is_approved BOOLEAN DEFAULT FALSE,
    helpful_count INTEGER DEFAULT 0,
    created_at  TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uniq_review UNIQUE (product_id, user_id)
);

CREATE TABLE IF NOT EXISTS wishlist (
    id          SERIAL PRIMARY KEY,
    user_id     INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    product_id  INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    created_at  TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uniq_wishlist UNIQUE (user_id, product_id)
);

CREATE TABLE IF NOT EXISTS cart (
    id          SERIAL PRIMARY KEY,
    user_id     INTEGER REFERENCES users(id) ON DELETE CASCADE,
    session_id  VARCHAR(128),
    product_id  INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    created_at  TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS affiliates (
    id               SERIAL PRIMARY KEY,
    user_id          INTEGER NOT NULL UNIQUE REFERENCES users(id) ON DELETE CASCADE,
    total_clicks     INTEGER DEFAULT 0,
    total_conversions INTEGER DEFAULT 0,
    total_earnings   DECIMAL(10,2) DEFAULT 0,
    pending_earnings DECIMAL(10,2) DEFAULT 0,
    paid_earnings    DECIMAL(10,2) DEFAULT 0,
    commission_rate  DECIMAL(5,2) DEFAULT 10.00
);

CREATE TABLE IF NOT EXISTS affiliate_conversions (
    id           SERIAL PRIMARY KEY,
    affiliate_id INTEGER NOT NULL REFERENCES affiliates(id) ON DELETE CASCADE,
    order_id     INTEGER NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    commission   DECIMAL(10,2) NOT NULL,
    status       VARCHAR(20) DEFAULT 'pending',
    created_at   TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS affiliate_payouts (
    id           SERIAL PRIMARY KEY,
    affiliate_id INTEGER NOT NULL REFERENCES affiliates(id) ON DELETE CASCADE,
    amount       DECIMAL(10,2) NOT NULL,
    method       payout_method DEFAULT 'upi',
    account_info VARCHAR(255),
    status       payout_status DEFAULT 'pending',
    admin_note   TEXT,
    created_at   TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tickets (
    id          SERIAL PRIMARY KEY,
    user_id     INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    subject     VARCHAR(255) NOT NULL,
    status      ticket_status DEFAULT 'open',
    priority    ticket_priority DEFAULT 'medium',
    assigned_to INTEGER REFERENCES users(id) ON DELETE SET NULL,
    created_at  TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS ticket_messages (
    id          SERIAL PRIMARY KEY,
    ticket_id   INTEGER NOT NULL REFERENCES tickets(id) ON DELETE CASCADE,
    sender_id   INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    body        TEXT NOT NULL,
    is_admin    BOOLEAN DEFAULT FALSE,
    created_at  TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS faqs (
    id          SERIAL PRIMARY KEY,
    question    TEXT NOT NULL,
    answer      TEXT NOT NULL,
    sort_order  INTEGER DEFAULT 0,
    is_published BOOLEAN DEFAULT TRUE
);
CREATE INDEX IF NOT EXISTS idx_faqs_search ON faqs USING GIN (to_tsvector('english', question || ' ' || answer));

CREATE TABLE IF NOT EXISTS newsletters (
    id         SERIAL PRIMARY KEY,
    email      VARCHAR(191) NOT NULL UNIQUE,
    is_active  BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS settings (
    id    SERIAL PRIMARY KEY,
    key   VARCHAR(100) NOT NULL UNIQUE,
    value TEXT
);

CREATE TABLE IF NOT EXISTS activity_logs (
    id          SERIAL PRIMARY KEY,
    admin_id    INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    action      VARCHAR(255) NOT NULL,
    target_type VARCHAR(60),
    target_id   INTEGER,
    meta        JSONB,
    ip          VARCHAR(45),
    created_at  TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS password_resets (
    id         SERIAL PRIMARY KEY,
    email      VARCHAR(191) NOT NULL,
    token      VARCHAR(128) NOT NULL UNIQUE,
    expires_at TIMESTAMP WITH TIME ZONE NOT NULL,
    used       BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS login_attempts (
    id           SERIAL PRIMARY KEY,
    identifier   VARCHAR(191) NOT NULL,
    ip           VARCHAR(45) NOT NULL,
    attempted_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS ip_whitelist (
    id         SERIAL PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    note       VARCHAR(255),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS email_campaigns (
    id          SERIAL PRIMARY KEY,
    subject     VARCHAR(255) NOT NULL,
    body        TEXT NOT NULL,
    status      campaign_status DEFAULT 'draft',
    scheduled_at TIMESTAMP WITH TIME ZONE,
    sent_at     TIMESTAMP WITH TIME ZONE,
    created_by  INTEGER REFERENCES users(id) ON DELETE SET NULL,
    recipients  INTEGER DEFAULT 0,
    created_at  TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Seed Data (Converted for PG)
INSERT INTO users (name, email, password, role, email_verified, referral_code) VALUES
('Super Admin', 'admin@themorashop.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uXffKN/hm', 'super_admin', TRUE, 'ADMIN001'),
('Demo User', 'user@themorashop.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uXffKN/hm', 'user', TRUE, 'USER001')
ON CONFLICT DO NOTHING;

INSERT INTO categories (name, slug, icon, sort_order) VALUES
('Templates', 'templates', 'bi-layout-text-window', 1),
('eBooks', 'ebooks', 'bi-book', 2),
('Software', 'software', 'bi-code-slash', 3),
('Presets', 'presets', 'bi-sliders', 4),
('Courses', 'courses', 'bi-mortarboard', 5),
('Graphics', 'graphics', 'bi-palette', 6),
('Audio', 'audio', 'bi-music-note-beamed', 7),
('Fonts', 'fonts', 'bi-type', 8)
ON CONFLICT DO NOTHING;

INSERT INTO products (title, slug, description, short_desc, price, original_price, category_id, thumbnail, status, avg_rating, total_sales, meta_title, meta_desc) VALUES
('Pro UI Kit 2024', 'pro-ui-kit-2024', 'A comprehensive UI kit with 500+ components for Figma and Adobe XD. Perfect for web and mobile app design projects.', 'Premium UI Kit with 500+ components', 29.00, 49.00, 1, '/assets/images/product-1.svg', 'active', 4.8, 124, 'Pro UI Kit 2024 — Premium Design Components', 'Download the Pro UI Kit with 500+ Figma components for $29'),
('Python Mastery eBook', 'python-mastery-ebook', 'Master Python programming from beginner to advanced. Covers data structures, algorithms, web development, and machine learning with 300+ examples.', 'Complete Python guide from zero to expert', 19.00, 39.00, 2, '/assets/images/product-2.svg', 'active', 4.6, 89, 'Python Mastery eBook — Zero to Expert', 'Learn Python completely with this 300-page comprehensive guide')
ON CONFLICT DO NOTHING;

INSERT INTO settings (key, value) VALUES
('site_name', 'Themora Shop'),
('site_tagline', 'Premium Digital Products for Creators'),
('currency', 'USD'),
('currency_symbol', '$'),
('gst_rate', '18'),
('vat_rate', '0'),
('tax_label', 'GST'),
('maintenance_mode', '0'),
('razorpay_enabled', '1'),
('stripe_enabled', '0'),
('paypal_enabled', '0'),
('upi_enabled', '1'),
('affiliate_enabled', '1')
ON CONFLICT DO NOTHING;
