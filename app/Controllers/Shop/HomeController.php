<?php
// ============================================================
// THEMORA SHOP — Home Controller
// ============================================================

namespace App\Controllers\Shop;

use App\Core\{Controller, Request, Database};

class HomeController extends Controller
{
    public function index(Request $req): void
    {
        // Featured products (active, sorted by sales)
        $featured = Database::fetchAll(
            'SELECT p.*, c.name as category_name FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.status = "active"
             ORDER BY p.total_sales DESC, p.avg_rating DESC
             LIMIT 8'
        );

        // Trending (top rated last 30 days)
        $trending = Database::fetchAll(
            'SELECT p.*, c.name as category_name FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.status = "active"
             ORDER BY p.avg_rating DESC, p.total_sales DESC
             LIMIT 6'
        );

        // Categories
        $categories = Database::fetchAll(
            'SELECT c.*, COUNT(p.id) as product_count
             FROM categories c
             LEFT JOIN products p ON p.category_id = c.id AND p.status = "active"
             GROUP BY c.id ORDER BY c.sort_order ASC'
        );

        // Stats
        $stats = [
            'products' => Database::fetchOne('SELECT COUNT(*) as c FROM products WHERE status="active"')['c'],
            'buyers'   => Database::fetchOne('SELECT COUNT(DISTINCT user_id) as c FROM orders WHERE status="paid"')['c'],
            'sales'    => Database::fetchOne('SELECT SUM(total) as c FROM orders WHERE status="paid"')['c'] ?? 0,
            'rating'   => Database::fetchOne('SELECT AVG(rating) as c FROM reviews WHERE is_approved=1')['c'] ?? 5,
        ];

        // Testimonials (approved reviews with users)
        $testimonials = Database::fetchAll(
            'SELECT r.*, u.name as user_name, u.avatar, p.title as product_title
             FROM reviews r
             JOIN users u ON u.id = r.user_id
             JOIN products p ON p.id = r.product_id
             WHERE r.is_approved = 1 AND r.body IS NOT NULL AND r.body != ""
             ORDER BY r.helpful_count DESC, r.created_at DESC
             LIMIT 6'
        );

        // FAQs for landing
        $faqs = Database::fetchAll(
            'SELECT * FROM faqs WHERE is_published = 1 ORDER BY sort_order ASC LIMIT 6'
        );

        // AI recommendations (simple: products user hasn't bought)
        $recommendations = [];
        if (logged_in()) {
            $user = auth();
            $recommendations = Database::fetchAll(
                'SELECT p.*, c.name as category_name FROM products p
                 LEFT JOIN categories c ON p.category_id = c.id
                 WHERE p.status = "active"
                 AND p.id NOT IN (
                     SELECT oi.product_id FROM order_items oi
                     JOIN orders o ON o.id = oi.order_id
                     WHERE o.user_id = ?
                 )
                 ORDER BY p.avg_rating DESC LIMIT 4',
                [$user['id']]
            );
        }

        $this->view('user/home', [
            'title'           => setting('site_name') . ' — ' . setting('site_tagline', 'Premium Digital Products'),
            'featured'        => $featured,
            'trending'        => $trending,
            'categories'      => $categories,
            'stats'           => $stats,
            'testimonials'    => $testimonials,
            'faqs'            => $faqs,
            'recommendations' => $recommendations,
        ]);
    }
}
