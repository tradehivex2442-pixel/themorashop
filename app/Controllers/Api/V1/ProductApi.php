<?php
// ============================================================
// THEMORA SHOP — API: Product Search, Newsletter, Recommendations
// ============================================================
namespace App\Controllers\Api\V1;
use App\Core\{Controller, Request, Database, Response};

class ProductApi extends Controller
{
    public function search(Request $req): void
    {
        $q = trim($req->get('q', ''));
        if (strlen($q) < 2) { Response::json(['results' => []]); }

        $results = Database::fetchAll(
            'SELECT p.id, p.title, p.slug, p.thumbnail, p.price, p.avg_rating, c.name as category
             FROM products p LEFT JOIN categories c ON c.id=p.category_id
             WHERE p.status="active" AND (p.title LIKE ? OR p.short_desc LIKE ?)
             LIMIT 8',
            ["%{$q}%", "%{$q}%"]
        );

        foreach ($results as &$r) {
            $r['url']   = url("products/{$r['slug']}");
            $r['price'] = currency((float)$r['price']);
        }

        Response::json(['results' => $results]);
    }

    public function newsletter(Request $req): void
    {
        $email = strtolower(trim($req->post('email', '')));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { Response::error('Invalid email address.'); }

        $exists = Database::fetchOne('SELECT id FROM newsletters WHERE email=?', [$email]);
        if ($exists) { Response::success('You are already subscribed!'); }

        Database::execute('INSERT INTO newsletters (email) VALUES (?)', [$email]);
        Response::success('Subscribed! Check your inbox for a welcome discount 🎉');
    }

    public function recommendations(Request $req): void
    {
        $userId = auth() ? auth()['id'] : null;
        if ($userId) {
            $products = Database::fetchAll(
                'SELECT p.id, p.title, p.slug, p.thumbnail, p.price, p.avg_rating, c.name as category
                 FROM products p LEFT JOIN categories c ON c.id=p.category_id
                 WHERE p.status="active" AND p.id NOT IN (
                   SELECT oi.product_id FROM order_items oi JOIN orders o ON o.id=oi.order_id WHERE o.user_id=?
                 )
                 ORDER BY p.avg_rating DESC LIMIT 4',
                [$userId]
            );
        } else {
            $products = Database::fetchAll(
                'SELECT p.id,p.title,p.slug,p.thumbnail,p.price,p.avg_rating,c.name as category
                 FROM products p LEFT JOIN categories c ON c.id=p.category_id
                 WHERE p.status="active" ORDER BY p.total_sales DESC LIMIT 4'
            );
        }
        foreach ($products as &$p) { $p['url'] = url("products/{$p['slug']}"); }
        Response::json(['products' => $products]);
    }
}
