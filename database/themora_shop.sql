-- ============================================================
-- USERS
-- ============================================================
CREATE TABLE users (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(120) NOT NULL,
    email           VARCHAR(191) NOT NULL UNIQUE,
    password        VARCHAR(255),
    avatar          VARCHAR(255),
    bio             TEXT,
    role            ENUM('user','editor','support','super_admin') DEFAULT 'user',
    google_id       VARCHAR(100),
    github_id       VARCHAR(100),
    totp_secret     VARCHAR(64),
    is_2fa_enabled  TINYINT(1) DEFAULT 0,
    is_blocked      TINYINT(1) DEFAULT 0,
    referral_code   VARCHAR(20) UNIQUE,
    referred_by     INT UNSIGNED,
    email_verified  TINYINT(1) DEFAULT 0,
    notif_order_success   TINYINT(1) DEFAULT 1,
    notif_download_expiry TINYINT(1) DEFAULT 1,
    notif_newsletter      TINYINT(1) DEFAULT 0,
    notif_affiliate       TINYINT(1) DEFAULT 1,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (referred_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- CATEGORIES
-- ============================================================
CREATE TABLE categories (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    slug        VARCHAR(120) NOT NULL UNIQUE,
    icon        VARCHAR(60),
    sort_order  INT DEFAULT 0
) ENGINE=InnoDB;

-- ============================================================
-- PRODUCTS
-- ============================================================
CREATE TABLE products (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title           VARCHAR(255) NOT NULL,
    slug            VARCHAR(300) NOT NULL UNIQUE,
    description     LONGTEXT,
    short_desc      TEXT,
    price           DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    original_price  DECIMAL(10,2),
    category_id     INT UNSIGNED,
    file_path       VARCHAR(500),
    preview_file    VARCHAR(500),
    demo_video_url  VARCHAR(500),
    thumbnail       VARCHAR(500),
    status          ENUM('active','inactive','draft') DEFAULT 'active',
    download_limit  INT DEFAULT 5,
    flash_sale_price    DECIMAL(10,2),
    flash_sale_ends     DATETIME,
    meta_title      VARCHAR(255),
    meta_desc       TEXT,
    total_sales     INT UNSIGNED DEFAULT 0,
    avg_rating      DECIMAL(3,2) DEFAULT 0.00,
    version         VARCHAR(20) DEFAULT '1.0',
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FULLTEXT INDEX ft_product_search (title, description, short_desc)
) ENGINE=InnoDB;

-- ============================================================
-- PRODUCT IMAGES
-- ============================================================
CREATE TABLE product_images (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id  INT UNSIGNED NOT NULL,
    image_path  VARCHAR(500) NOT NULL,
    sort_order  INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- PRODUCT TAGS
-- ============================================================
CREATE TABLE product_tags (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id  INT UNSIGNED NOT NULL,
    tag         VARCHAR(80) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_tag (tag)
) ENGINE=InnoDB;

-- ============================================================
-- COUPONS
-- ============================================================
CREATE TABLE coupons (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code        VARCHAR(60) NOT NULL UNIQUE,
    type        ENUM('flat','percent') NOT NULL DEFAULT 'percent',
    value       DECIMAL(10,2) NOT NULL,
    min_order   DECIMAL(10,2) DEFAULT 0,
    max_uses    INT DEFAULT 0,
    used_count  INT DEFAULT 0,
    product_id  INT UNSIGNED,
    expiry      DATETIME,
    is_active   TINYINT(1) DEFAULT 1,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- ORDERS
-- ============================================================
CREATE TABLE orders (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED,
    guest_email     VARCHAR(191),
    guest_name      VARCHAR(120),
    subtotal        DECIMAL(10,2) NOT NULL,
    discount        DECIMAL(10,2) DEFAULT 0,
    tax             DECIMAL(10,2) DEFAULT 0,
    total           DECIMAL(10,2) NOT NULL,
    coupon_id       INT UNSIGNED,
    payment_gateway ENUM('razorpay','stripe','paypal','upi','free') DEFAULT 'razorpay',
    transaction_id  VARCHAR(255),
    status          ENUM('pending','paid','failed','refunded','disputed') DEFAULT 'pending',
    is_disputed     TINYINT(1) DEFAULT 0,
    notes           TEXT,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE SET NULL,
    INDEX idx_order_user (user_id),
    INDEX idx_order_status (status)
) ENGINE=InnoDB;

-- ============================================================
-- ORDER ITEMS
-- ============================================================
CREATE TABLE order_items (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id        INT UNSIGNED NOT NULL,
    product_id      INT UNSIGNED NOT NULL,
    price           DECIMAL(10,2) NOT NULL,
    download_token  VARCHAR(128) UNIQUE,
    download_count  INT DEFAULT 0,
    max_downloads   INT DEFAULT 5,
    token_expires   DATETIME,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- REVIEWS
-- ============================================================
CREATE TABLE reviews (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id  INT UNSIGNED NOT NULL,
    user_id     INT UNSIGNED NOT NULL,
    rating      TINYINT UNSIGNED NOT NULL CHECK (rating BETWEEN 1 AND 5),
    body        TEXT,
    media_path  VARCHAR(500),
    is_approved TINYINT(1) DEFAULT 0,
    helpful_count INT DEFAULT 0,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_review (product_id, user_id)
) ENGINE=InnoDB;

-- ============================================================
-- WISHLIST
-- ============================================================
CREATE TABLE wishlist (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NOT NULL,
    product_id  INT UNSIGNED NOT NULL,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_wishlist (user_id, product_id)
) ENGINE=InnoDB;

-- ============================================================
-- CART (session/user based)
-- ============================================================
CREATE TABLE cart (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED,
    session_id  VARCHAR(128),
    product_id  INT UNSIGNED NOT NULL,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_cart_session (session_id)
) ENGINE=InnoDB;

-- ============================================================
-- AFFILIATES
-- ============================================================
CREATE TABLE affiliates (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id          INT UNSIGNED NOT NULL UNIQUE,
    total_clicks     INT DEFAULT 0,
    total_conversions INT DEFAULT 0,
    total_earnings   DECIMAL(10,2) DEFAULT 0,
    pending_earnings DECIMAL(10,2) DEFAULT 0,
    paid_earnings    DECIMAL(10,2) DEFAULT 0,
    commission_rate  DECIMAL(5,2) DEFAULT 10.00,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE affiliate_conversions (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    affiliate_id INT UNSIGNED NOT NULL,
    order_id     INT UNSIGNED NOT NULL,
    commission   DECIMAL(10,2) NOT NULL,
    status       ENUM('pending','paid') DEFAULT 'pending',
    created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (affiliate_id) REFERENCES affiliates(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE affiliate_payouts (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    affiliate_id INT UNSIGNED NOT NULL,
    amount       DECIMAL(10,2) NOT NULL,
    method       ENUM('upi','bank','paypal') DEFAULT 'upi',
    account_info VARCHAR(255),
    status       ENUM('pending','approved','paid','rejected') DEFAULT 'pending',
    admin_note   TEXT,
    created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (affiliate_id) REFERENCES affiliates(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- SUPPORT TICKETS
-- ============================================================
CREATE TABLE tickets (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NOT NULL,
    subject     VARCHAR(255) NOT NULL,
    status      ENUM('open','in-progress','resolved','closed') DEFAULT 'open',
    priority    ENUM('low','medium','high','urgent') DEFAULT 'medium',
    assigned_to INT UNSIGNED,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE ticket_messages (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ticket_id   INT UNSIGNED NOT NULL,
    sender_id   INT UNSIGNED NOT NULL,
    body        TEXT NOT NULL,
    is_admin    TINYINT(1) DEFAULT 0,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- FAQS
-- ============================================================
CREATE TABLE faqs (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question    TEXT NOT NULL,
    answer      LONGTEXT NOT NULL,
    sort_order  INT DEFAULT 0,
    is_published TINYINT(1) DEFAULT 1,
    FULLTEXT INDEX ft_faq (question, answer)
) ENGINE=InnoDB;

-- ============================================================
-- NEWSLETTER
-- ============================================================
CREATE TABLE newsletters (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email      VARCHAR(191) NOT NULL UNIQUE,
    is_active  TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- SETTINGS (key-value store)
-- ============================================================
CREATE TABLE settings (
    id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) NOT NULL UNIQUE,
    value LONGTEXT
) ENGINE=InnoDB;

-- ============================================================
-- ACTIVITY LOG
-- ============================================================
CREATE TABLE activity_logs (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id    INT UNSIGNED NOT NULL,
    action      VARCHAR(255) NOT NULL,
    target_type VARCHAR(60),
    target_id   INT UNSIGNED,
    meta        JSON,
    ip          VARCHAR(45),
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- PASSWORD RESETS
-- ============================================================
CREATE TABLE password_resets (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email      VARCHAR(191) NOT NULL,
    token      VARCHAR(128) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    used       TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_pr_email (email)
) ENGINE=InnoDB;

-- ============================================================
-- LOGIN ATTEMPTS (brute-force protection)
-- ============================================================
CREATE TABLE login_attempts (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    identifier   VARCHAR(191) NOT NULL,
    ip           VARCHAR(45) NOT NULL,
    attempted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_la_identifier (identifier),
    INDEX idx_la_ip (ip)
) ENGINE=InnoDB;

-- ============================================================
-- IP WHITELIST (admin access)
-- ============================================================
CREATE TABLE ip_whitelist (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    note       VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- EMAIL CAMPAIGNS
-- ============================================================
CREATE TABLE email_campaigns (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subject     VARCHAR(255) NOT NULL,
    body        LONGTEXT NOT NULL,
    status      ENUM('draft','scheduled','sent') DEFAULT 'draft',
    scheduled_at DATETIME,
    sent_at     DATETIME,
    created_by  INT UNSIGNED,
    recipients  INT DEFAULT 0,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- SEED DATA
-- ============================================================

-- Default admin user (password: Admin@123)
INSERT INTO users (name, email, password, role, email_verified, referral_code) VALUES
('Super Admin', 'admin@themorashop.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uXffKN/hm', 'super_admin', 1, 'ADMIN001'),
('Demo User', 'user@themorashop.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uXffKN/hm', 'user', 1, 'USER001');

-- Categories
INSERT INTO categories (name, slug, icon, sort_order) VALUES
('Templates', 'templates', 'bi-layout-text-window', 1),
('eBooks', 'ebooks', 'bi-book', 2),
('Software', 'software', 'bi-code-slash', 3),
('Presets', 'presets', 'bi-sliders', 4),
('Courses', 'courses', 'bi-mortarboard', 5),
('Graphics', 'graphics', 'bi-palette', 6),
('Audio', 'audio', 'bi-music-note-beamed', 7),
('Fonts', 'fonts', 'bi-type', 8);

-- Products
INSERT INTO products (title, slug, description, short_desc, price, original_price, category_id, thumbnail, status, avg_rating, total_sales, meta_title, meta_desc) VALUES
('Pro UI Kit 2024', 'pro-ui-kit-2024', 'A comprehensive UI kit with 500+ components for Figma and Adobe XD. Perfect for web and mobile app design projects.', 'Premium UI Kit with 500+ components', 29.00, 49.00, 1, '/themora_Shop/public/assets/images/product-1.svg', 'active', 4.8, 124, 'Pro UI Kit 2024 — Premium Design Components', 'Download the Pro UI Kit with 500+ Figma components for $29'),
('Python Mastery eBook', 'python-mastery-ebook', 'Master Python programming from beginner to advanced. Covers data structures, algorithms, web development, and machine learning with 300+ examples.', 'Complete Python guide from zero to expert', 19.00, 39.00, 2, '/themora_Shop/public/assets/images/product-2.svg', 'active', 4.6, 89, 'Python Mastery eBook — Zero to Expert', 'Learn Python completely with this 300-page comprehensive guide'),
('SEO Automation Suite', 'seo-automation-suite', 'Powerful Python scripts to automate keyword research, rank tracking, competitor analysis, and monthly SEO reporting.', 'Automate your entire SEO workflow', 49.00, 99.00, 3, '/themora_Shop/public/assets/images/product-3.svg', 'active', 4.9, 67, 'SEO Automation Suite — Python SEO Scripts', 'Automate SEO research and reporting with Python scripts'),
('Lightroom Cinematic Presets', 'lightroom-cinematic-presets', '50 hand-crafted Lightroom presets for cinematic color grading. Compatible with Lightroom Classic, CC and mobile.', '50 professional cinematic presets', 15.00, 25.00, 4, '/themora_Shop/public/assets/images/product-4.svg', 'active', 4.7, 203, 'Lightroom Cinematic Presets Pack', '50 cinematic Lightroom presets for professional photography'),
('Full-Stack Web Dev Course', 'fullstack-web-dev-course', 'Complete full-stack web development course covering HTML, CSS, JavaScript, React, Node.js, and PostgreSQL with 60+ hours of content.', '60+ hours of full-stack development content', 79.00, 149.00, 5, '/themora_Shop/public/assets/images/product-5.svg', 'active', 4.8, 412, 'Full-Stack Web Dev Course — 60+ Hours', 'Complete full-stack development course covering React and Node.js'),
('Brand Identity Pack', 'brand-identity-pack', 'Complete brand identity package with logo templates, business card designs, letterhead, social media kit, and style guide.', 'Full brand identity design package', 35.00, 65.00, 6, '/themora_Shop/public/assets/images/product-6.svg', 'active', 4.5, 78, 'Brand Identity Pack — Complete Design Kit', 'Professional brand identity package with logo and social media templates'),
('Lo-Fi Music Pack', 'lofi-music-pack', '30 royalty-free lo-fi music tracks for YouTube, podcasts, and streaming platforms. WAV and MP3 formats included.', '30 royalty-free lo-fi tracks', 25.00, 45.00, 7, '/themora_Shop/public/assets/images/product-7.svg', 'active', 4.4, 156, 'Lo-Fi Music Pack — 30 Royalty-Free Tracks', 'Download 30 royalty-free lo-fi music tracks for content creators'),
('Premium Font Collection', 'premium-font-collection', 'Collection of 15 premium fonts including serif, sans-serif, display, and handwriting styles. Commercial license included.', '15 premium fonts with commercial license', 22.00, 40.00, 8, '/themora_Shop/public/assets/images/product-8.svg', 'active', 4.7, 91, 'Premium Font Collection — 15 Commercial Fonts', 'Download 15 premium fonts with full commercial license included');

-- Affiliates for demo user
INSERT INTO affiliates (user_id, commission_rate) VALUES (2, 15.00);

-- Default settings
INSERT INTO settings (`key`, value) VALUES
('site_name', 'Themora Shop'),
('site_tagline', 'Premium Digital Products for Creators'),
('currency', 'USD'),
('currency_symbol', '$'),
('gst_rate', '18'),
('vat_rate', '0'),
('tax_label', 'GST'),
('maintenance_mode', '0'),
('maintenance_message', 'We are performing scheduled maintenance. Back shortly!'),
('razorpay_enabled', '1'),
('stripe_enabled', '0'),
('paypal_enabled', '0'),
('upi_enabled', '1'),
('bundle_discount_enabled', '1'),
('bundle_discount_percent', '10'),
('bundle_min_items', '2'),
('download_expiry_hours', '48'),
('default_download_limit', '5'),
('affiliate_enabled', '1'),
('affiliate_commission_global', '10'),
('pagination_type', 'pagination'),
('products_per_page', '12'),
('logo_path', ''),
('footer_text', '© 2025 Themora Shop. All rights reserved.'),
('primary_color', '#6366f1'),
('robots_txt', "User-agent: *\nAllow: /"),
('sitemap_enabled', '1');

-- FAQs
INSERT INTO faqs (question, answer, sort_order) VALUES
('How do I download my purchased products?', 'After a successful payment, you will receive an email with a secure download link. You can also access your downloads anytime from the Orders section in your dashboard.', 1),
('How long are download links valid?', 'Download links are valid for 48 hours and can be used up to 5 times. If your link expires, you can request a new one from your Orders dashboard.', 2),
('Do you offer refunds?', 'We offer a 14-day money-back guarantee for all digital products. Contact our support team within 14 days of purchase for a full refund.', 3),
('Can I use the products for commercial projects?', 'Yes! Most of our products come with a commercial license. Please check the product description for specific licensing terms.', 4),
('How do I become an affiliate?', 'Every registered user automatically gets a unique referral link. Visit the Affiliate section in your dashboard to view your link and track your earnings.', 5),
('What payment methods do you accept?', 'We accept Razorpay (all UPI, cards, net banking), Stripe (international cards), PayPal, and direct UPI payments.', 6),
('Are the digital products updated?', 'Yes! When a product is updated, all existing buyers are notified and can download the latest version at no extra cost.', 7),
('How do I contact support?', 'You can reach us via the contact form, open a support ticket from your dashboard, or use our live chat widget for instant help.', 8);

-- Coupons
INSERT INTO coupons (code, type, value, min_order, max_uses, expiry, is_active) VALUES
('WELCOME20', 'percent', 20.00, 0.00, 500, DATE_ADD(NOW(), INTERVAL 1 YEAR), 1),
('FLAT10OFF', 'flat', 10.00, 30.00, 200, DATE_ADD(NOW(), INTERVAL 6 MONTH), 1),
('SUMMER50', 'percent', 50.00, 50.00, 100, DATE_ADD(NOW(), INTERVAL 2 MONTH), 1);

-- Reviews
INSERT INTO reviews (product_id, user_id, rating, body, is_approved) VALUES
(1, 2, 5, 'Absolutely stunning UI kit! The components are pixel-perfect and saved me weeks of design work. Totally worth every penny!', 1),
(2, 2, 5, 'Best Python book I have read. Clear explanations with practical examples throughout. Already landed my first freelance gig!', 1),
(5, 2, 5, 'The course is incredibly comprehensive. I went from knowing basic HTML to building full-stack apps in just 2 months!', 1);
