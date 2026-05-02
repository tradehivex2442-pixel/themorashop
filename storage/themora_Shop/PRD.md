THEMORA SHOP
Digital Product Selling Platform
Product Requirements Document  |  v2.0  |  Enhanced Edition
60+
Total Features	4
Payment Gateways	3
User Roles	100%
Responsive	2
UI Themes

Tech Stack: PHP 8.2  |  MySQL 8  |  MDB UI  |  Redis Cache
Payments: Razorpay  |  Stripe  |  PayPal  |  UPI

1. Project Overview
Themora Shop is a full-stack digital product selling platform built with PHP and MySQL. It supports selling downloadable digital goods — templates, ebooks, software, presets, courses, and more — with a premium, responsive UI for both mobile (native app feel) and desktop (professional eCommerce experience).

The platform includes user-facing storefront features, a robust admin dashboard, advanced security, AI-powered recommendations, affiliate marketing, PWA support, and deep analytics — everything needed to run a professional digital product business.

2. User Side — Frontend Features
2.1  Authentication
Feature	Details / Notes
Signup / Login	Email + password with form validation
Google / GitHub OAuth  NEW	One-click social login via OAuth 2.0
Forgot / Reset Password	Secure token-based password reset via email
Two-Factor Auth (2FA)  NEW	TOTP authenticator app support (Google Authenticator)
User Profile	Edit name, email, avatar, change password
Account Deletion / Export  NEW	GDPR-compliant data export and account deletion

2.2  Landing Page
Feature	Details / Notes
Hero Section	Animated CTA button — 'Explore Products'
Featured & Trending Carousel  NEW	Auto-rotating product highlights with manual arrows
Testimonials	Star ratings + user testimonials grid
FAQ Accordion	Expandable Q&A section
Newsletter Popup  NEW	Email capture with discount incentive
Sticky Header  NEW	Logo + search + cart icon always visible on scroll
Footer	Links, social icons, copyright, branding

2.3  Product Listing Page
Feature	Details / Notes
Responsive Layout	Grid (desktop, 3–4 per row) / vertical cards (mobile)
Filters  NEW	Category, price range, rating, file type, tag
Live Search with Autocomplete  NEW	Instant results as user types, keyboard-navigable
Sort Options	Latest, price (asc/desc), popularity, rating
Wishlist / Save for Later  NEW	Heart icon on card saves to user profile
Infinite Scroll / Pagination	Admin configurable

2.4  Product Detail Page
Feature	Details / Notes
Product Info  NEW	Title, description, screenshots, demo video embed
Pricing Display	Price + original (crossed out) when discounted
Buy Now / Add to Cart	Prominent CTA with mobile-friendly sizing
Free Preview / Sample  NEW	Download a limited sample file before purchase
Ratings & Reviews  NEW	Star ratings + written reviews (verified buyers only)
Related Products	Carousel of similar/recommended products
Social Share  NEW	Copy link + share to Twitter, WhatsApp, LinkedIn

2.5  Cart & Checkout
Feature	Details / Notes
Cart Page	Product list, quantity edit, remove item, subtotal
Coupon / Referral Code  NEW	Apply discount or referral code at checkout
Bundle Discount  NEW	Automatic discount when buying multiple items
Order Summary	Tax breakdown, final total, applied discounts
Payment Gateways  NEW	Razorpay, Stripe, PayPal, UPI (admin configurable)
Guest Checkout  NEW	Purchase without creating an account

2.6  Orders & Downloads
Feature	Details / Notes
Order History	Purchased products list with dates and statuses
Secure Download Links	Signed URLs with configurable auto-expiry
Download Attempt Limit  NEW	Configurable max download attempts per order
Invoice / Receipt PDF	Downloadable PDF receipt for every order
Re-send Download Email  NEW	User can request download link re-delivery
Order Status Tracking	Pending → Paid → Ready to Download

2.7  Affiliate & Referral System  [NEW]
Feature	Details / Notes
Unique Referral Link  NEW	Each user gets a personal referral URL
Earnings Dashboard  NEW	Clicks, conversions, pending & paid earnings
Configurable Commission  NEW	Admin sets commission % per product or globally
Payout Request  NEW	User submits payout request (UPI / bank transfer)

2.8  Support & Community
Feature	Details / Notes
Contact Form	Message submission with email notification to admin
Searchable FAQ Page  NEW	Full-text search across FAQ entries
AI Chatbot  NEW	Instant answers to common queries via AI integration
Support Ticket System  NEW	Submit tickets, track status (open/in-progress/resolved)
Live Chat Widget  NEW	Third-party live chat integration (Crisp / Tawk.to)

3. Admin Side — Dashboard Features
3.1  Authentication & Roles
Feature	Details / Notes
Admin Login	Secure login with rate limiting & brute-force lockout
Role-Based Access  NEW	Super Admin, Editor, Support Agent roles
Activity Log  NEW	Full log of admin actions per user
IP Whitelist  NEW	Restrict admin panel access by IP address

3.2  Product Management
Feature	Details / Notes
Add / Edit / Delete Products	Full CRUD for all product fields
Secure File Upload	Private storage with signed access URLs
Product Status  NEW	Active / Inactive / Draft states
Bulk CSV Import  NEW	Upload multiple products via CSV template
SEO Fields  NEW	Meta title, description, slug per product
File Version Updates  NEW	Push updated file to all existing buyers
Product Tags  NEW	Tagging system for better discoverability

3.3  Order & Payment Management
Feature	Details / Notes
Order List	All orders with transaction ID, status, user info
Refund Management	Manual and automatic refund processing
Payment Gateway Settings	Configure API keys for each gateway in dashboard
Currency / Tax Settings	Set currency, GST %, VAT % globally
Abandoned Cart Recovery  NEW	View abandoned carts, send recovery emails
Chargeback / Dispute Flag  NEW	Flag and track disputed orders

3.4  User Management
Feature	Details / Notes
User List	View all registered users with filters and search
Block / Unblock Users	Instantly restrict or restore user access
Purchase & Download History	Full history per user
Impersonate User  NEW	Log in as any user for debugging (Super Admin only)
CSV / Excel Export  NEW	Export user data for external use
Affiliate Payout Approvals  NEW	Review and approve affiliate payout requests

3.5  Coupons & Marketing
Feature	Details / Notes
Discount Codes	Flat amount or percentage discount codes
Expiry & Usage Limits	Set code expiry date and max uses
Flash Sale Pricing  NEW	Limited-time price overrides per product
Email Campaigns  NEW	Scheduled promotional emails to subscribers
Abandoned Cart Emails  NEW	Automated recovery email sequences
Web Push Notifications  NEW	Browser push notifications to opted-in users

3.6  Reports & Analytics
Feature	Details / Notes
Sales Reports	Daily, weekly, monthly revenue charts
Tax Collection Summary	GST / VAT collected per period
Top-Selling Products	Ranked chart of best performers
User Acquisition Report  NEW	Traffic source breakdown (organic, referral, direct)
Affiliate Performance Report  NEW	Per-affiliate clicks, conversions, earnings
Export Reports  NEW	Download as CSV or PDF

3.7  Support Management
Feature	Details / Notes
View Tickets	All tickets with status, priority, and user info
Respond to Tickets	Reply directly from dashboard
Assign to Agent  NEW	Route tickets to specific support agents
FAQ Management	Add, edit, reorder FAQ entries
AI-Suggested Replies  NEW	AI drafts response suggestions per ticket

3.8  Settings & Branding
Feature	Details / Notes
Payment API Keys	Editable gateway credentials per environment
Tax Settings	GST %, VAT %, per-country rules
Branding	Logo, site name, footer text, colors
Email Template Editor  NEW	Visual editor for all transactional emails
Custom Domain Indicator  NEW	SSL status display in settings
Maintenance Mode Toggle  NEW	Put site in maintenance with custom message
SEO Config  NEW	Robots.txt editor, auto-sitemap generation

4. Advanced & AI-Powered Features  [NEW]
4.1  AI Recommendations
Machine-learning powered personalization based on user browse and purchase history:
•Personalized product suggestions on homepage  NEW
•'You may also like' section on product detail page  NEW
•AI-curated product recommendation emails  NEW

4.2  SEO & Performance
Feature	Details / Notes
Auto Meta Tags  NEW	Auto-generated title, description, OG tags per product
Sitemap.xml  NEW	Auto-generated and submitted to Google Search Console
Schema Markup  NEW	Product, Review, BreadcrumbList structured data
Lazy Loading  NEW	Images and non-critical scripts load on demand
Caching Layer  NEW	Redis or file-based caching for pages and queries

4.3  Security Architecture
Feature	Details / Notes
Rate Limiting  NEW	Auth endpoints limited to 5 req/min per IP
CSRF Protection  NEW	Token validation on all state-changing forms
Signed Download URLs  NEW	Short-lived signed URLs for file delivery
SQL Injection / XSS Prevention  NEW	Prepared statements and output escaping throughout
Brute-Force Lockout  NEW	Account locked after 5 failed login attempts
HTTPS + HSTS  NEW	Forced HTTPS with Strict-Transport-Security header

4.4  Notification System
Feature	Details / Notes
Order Confirmation Email  NEW	Sent immediately after successful payment
Download Link Email  NEW	Secure download link delivered post-purchase
Failed Payment Email  NEW	Alert user with retry link
New Product Launch Email  NEW	Broadcast to subscribers on new product publish
Web Push Notifications  NEW	Via service worker (PWA)
Admin New-Order Alert  NEW	Instant email/Slack alert to admin

4.5  PWA — Progressive Web App
Feature	Details / Notes
Installable on Mobile  NEW	Add to home screen on Android and iOS
Offline Product Listing  NEW	Cached listing browsable without internet
AppBar + Bottom Navigation  NEW	Native app-style mobile navigation
Service Worker Push  NEW	Background push notifications

4.6  Localization & Currency
Feature	Details / Notes
Multi-Currency Display  NEW	Auto-detect user currency with real-time rates
Multi-Language (i18n)  NEW	Translation-ready string architecture
Per-Country Tax Rules  NEW	Region-specific tax calculation
Locale Formatting  NEW	Dates, numbers, currency per locale

4.7  Reviews & Social Proof
Feature	Details / Notes
Star Ratings  NEW	1–5 star ratings from verified buyers only
Written Reviews with Media  NEW	Text reviews with optional image upload
Admin Moderation  NEW	Approve / hide / delete reviews
Helpful Voting  NEW	Users can mark reviews as helpful
Rating Badge on Card  NEW	Aggregate star rating shown on product cards

4.8  Third-Party Integrations
Feature	Details / Notes
Google Analytics 4 + Meta Pixel  NEW	Full eCommerce event tracking
Mailchimp / Brevo  NEW	Sync subscribers and send campaigns
Zapier Webhooks  NEW	Trigger automations on order, signup, refund events
WhatsApp Notifications  NEW	Order alerts via Twilio or WATI
Discord / Slack Alerts  NEW	New order notifications to team channels

5. Technology Stack
Layer	Technology	Notes
Frontend	PHP + MDB UI (Bootstrap 5)	Material Design components, responsive grid
Backend	PHP 8.2	MVC pattern, REST API endpoints
Database	MySQL 8	Optimized indexes, foreign keys, full-text search
Cache	Redis / File cache	Session storage and query result caching
File Storage	Local / AWS S3	Private signed URLs for digital files
Email	SMTP / Mailgun / SendGrid	Transactional email with template editor
Payments	Razorpay, Stripe, PayPal, UPI	Admin-configurable per environment
Hosting	VPS / cPanel	Compatible with standard PHP hosting
PWA	Service Worker + Manifest	Offline support, push notifications, installable
Analytics	GA4 + Meta Pixel	Full eCommerce event tracking

6. User Journey Flow
Core Purchase Flow

Home Page  →  Browse Products  →  Product Detail  →  Add to Cart  →  Checkout  →  Payment  →  Order Confirmed  →  Download Product

Post-Purchase Flow

User Dashboard  →  Orders & Downloads  →  Download Anytime  →  Re-request Link via Email  →  Leave Review

Affiliate Flow

User Signs Up  →  Gets Referral Link  →  Shares Link  →  Friend Purchases  →  Commission Credited  →  Request Payout

7. UI / UX Requirements
7.1  Mobile View (Native App Experience)
•AppBar at top: logo + hamburger menu
•Bottom Navigation Bar: Home, Products, Cart, Profile (4 tabs)
•Full-width cards with large, touch-friendly buttons (min 48px tap targets)
•Vertical scrollable product list
•Native-style transitions and animations
•PWA installable prompt (Add to Home Screen)  NEW

7.2  Desktop View (Professional eCommerce)
•Top navbar: logo, navigation links, search, Login / Signup
•Product grid: 3–4 products per row with hover effects
•User dashboard with collapsible sidebar navigation
•Professional typography and generous spacing
•Sticky sidebar filters on product listing page  NEW
•Smooth hover effects and animated transitions

7.3  General / Theme
•Dark + Light mode toggle (persisted in localStorage / cookie)
•Mobile-first CSS, scales perfectly to tablet and desktop
•Smooth animations for premium, polished feel
•WCAG AA accessibility compliance (contrast ratios, focus states)
•RTL language layout support  NEW
•Lazy-loaded images and minified CSS/JS for fast page loads

8. Feature Summary — New vs Original
Items marked NEW were added in this enhanced v2.0 PRD beyond the original specification.

28
Original features	35+
New features added	63+
Total features	8
Major new modules


Recommended Folder Structure (Themora Shop v2.0)
themora-shop/
│
├── public/                          # Document root (point your domain here)
│   ├── index.php                    # Front controller (routes all requests)
│   ├── .htaccess                    # URL rewriting, security headers
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   ├── images/
│   │   ├── vendor/                  # Bootstrap, MDB, jQuery, etc.
│   │   └── uploads/                 # Temp user uploads (avatars, etc.)
│   ├── service-worker.js            # PWA
│   └── manifest.json                # PWA manifest
│
├── app/                             # Application core (not publicly accessible)
│   ├── bootstrap.php                # Autoload, env, container init
│   ├── config/
│   │   ├── app.php                  # App name, debug mode, timezone
│   │   ├── database.php             # MySQL connection settings
│   │   ├── cache.php                # Redis/file cache config
│   │   ├── payment.php              # Razorpay, Stripe, PayPal, UPI keys
│   │   ├── mail.php                 # SMTP / Mailgun / SendGrid
│   │   ├── social.php               # Google / GitHub OAuth credentials
│   │   └── ai.php                   # AI API keys (recommendations, chatbot)
│   │
│   ├── Controllers/
│   │   ├── User/
│   │   │   ├── AuthController.php          # Login, register, 2FA, logout
│   │   │   ├── ProfileController.php       # Edit profile, avatar, delete account
│   │   │   ├── OrderController.php         # Order history, downloads, invoices
│   │   │   ├── WishlistController.php
│   │   │   └── AffiliateController.php     # Referral links, earnings
│   │   ├── Shop/
│   │   │   ├── HomeController.php          # Landing page, hero, carousels
│   │   │   ├── ProductController.php       # Listing, detail, search, filters
│   │   │   ├── CartController.php
│   │   │   ├── CheckoutController.php      # Guest checkout, coupons, bundles
│   │   │   └── PaymentCallbackController.php # Razorpay/Stripe webhooks
│   │   ├── Support/
│   │   │   ├── TicketController.php
│   │   │   ├── ChatController.php          # Live chat (Crisp/Tawk proxy)
│   │   │   └── FaqController.php
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── ProductManageController.php
│   │   │   ├── OrderManageController.php
│   │   │   ├── UserManageController.php
│   │   │   ├── CouponController.php
│   │   │   ├── ReportController.php
│   │   │   ├── SupportController.php
│   │   │   ├── SettingsController.php
│   │   │   └── RoleController.php           # RBAC, impersonate
│   │   └── Api/
│   │       ├── V1/
│   │       │   ├── ProductApi.php           # JSON endpoints for frontend
│   │       │   ├── CartApi.php
│   │       │   ├── AuthApi.php
│   │       │   └── WebhookHandler.php
│   │
│   ├── Models/
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── DownloadLink.php
│   │   ├── Coupon.php
│   │   ├── Affiliate.php
│   │   ├── Review.php
│   │   ├── SupportTicket.php
│   │   ├── ActivityLog.php
│   │   ├── Cart.php
│   │   └── Setting.php
│   │
│   ├── Views/
│   │   ├── layouts/
│   │   │   ├── user.php                     # Header/footer for storefront
│   │   │   ├── admin.php
│   │   │   └── email.php
│   │   ├── user/
│   │   │   ├── home.php
│   │   │   ├── products/
│   │   │   ├── cart.php
│   │   │   ├── checkout.php
│   │   │   ├── account/
│   │   │   ├── affiliate/
│   │   │   └── support/
│   │   ├── admin/
│   │   │   ├── dashboard.php
│   │   │   ├── products/
│   │   │   ├── orders/
│   │   │   ├── users/
│   │   │   ├── reports/
│   │   │   ├── coupons/
│   │   │   └── settings/
│   │   ├── emails/
│   │   │   ├── order_confirmation.php
│   │   │   ├── download_links.php
│   │   │   └── password_reset.php
│   │   └── errors/
│   │       ├── 404.php
│   │       └── 503.php
│   │
│   ├── Middleware/
│   │   ├── AuthMiddleware.php
│   │   ├── GuestMiddleware.php
│   │   ├── AdminMiddleware.php
│   │   ├── RoleMiddleware.php
│   │   ├── RateLimiter.php
│   │   ├── CsrfMiddleware.php
│   │   └── SecurityHeaders.php
│   │
│   ├── Core/                               # Framework core
│   │   ├── Router.php
│   │   ├── Request.php
│   │   ├── Response.php
│   │   ├── Database.php                    # PDO wrapper
│   │   ├── Model.php                       # Base ORM
│   │   ├── Controller.php
│   │   ├── View.php
│   │   ├── Session.php
│   │   ├── Validation.php
│   │   └── Helpers.php
│   │
│   ├── Services/
│   │   ├── Payment/
│   │   │   ├── RazorpayService.php
│   │   │   ├── StripeService.php
│   │   │   ├── PaypalService.php
│   │   │   └── UpiService.php
│   │   ├── FileStorage/
│   │   │   ├── LocalStorage.php
│   │   │   ├── S3Storage.php
│   │   │   └── SignedUrlGenerator.php
│   │   ├── Email/
│   │   │   ├── Mailer.php
│   │   │   ├── TemplateEngine.php
│   │   │   └── MailchimpService.php
│   │   ├── Ai/
│   │   │   ├── RecommendationEngine.php
│   │   │   ├── ChatbotService.php
│   │   │   └── SuggestedReplies.php
│   │   ├── Analytics/
│   │   │   ├── GA4Tracker.php
│   │   │   └── ReportGenerator.php
│   │   ├── Notification/
│   │   │   ├── SmsService.php (Twilio)
│   │   │   ├── WebPush.php
│   │   │   └── DiscordAlert.php
│   │   └── Cache/
│   │       ├── RedisCache.php
│   │       └── FileCache.php
│   │
│   ├── Helpers/                            # Global functions
│   │   ├── security.php
│   │   ├── form.php
│   │   ├── date.php
│   │   ├── currency.php
│   │   └── seo.php
│   │
│   └── Traits/
│       ├── HasPermissions.php
│       ├── HasActivityLog.php
│       └── HasTwoFactorAuth.php
│
├── database/
│   ├── migrations/                         # SQL schema versioning
│   │   ├── 001_users.sql
│   │   ├── 002_products.sql
│   │   ├── 003_orders.sql
│   │   └── ...
│   ├── seeds/                              # Dummy data for testing
│   └── themora_shop.sql                    # Full schema dump
│
├── storage/                                # Private files
│   ├── products/                           # Original digital product files
│   ├── temp/                               # Temporary processing
│   ├── logs/
│   │   ├── app.log
│   │   ├── payment.log
│   │   └── error.log
│   ├── cache/                              # File-based cache (fallback)
│   └── exports/                            # Generated CSV/PDF reports
│
├── vendor/                                 # Composer packages
│
├── .env                                    # Environment variables (never commit)
├── .env.example
├── .htaccess                               # Root-level redirect to public/
├── composer.json
├── composer.lock
├── package.json                            # For frontend assets (npm)
├── webpack.mix.js                          # Laravel Mix (optional)
└── README.md

End of Document