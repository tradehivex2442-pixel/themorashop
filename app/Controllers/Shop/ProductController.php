<?php
// ============================================================
// THEMORA SHOP — Product Controller
// ============================================================

namespace App\Controllers\Shop;

use App\Core\{Controller, Request, Database, Response};

class ProductController extends Controller
{
    // ── Product Listing ───────────────────────────────────────
    public function index(Request $req): void
    {
        $page       = max(1, (int)$req->get('page', 1));
        $perPage    = max(1, (int)setting('products_per_page', 120));
        $category   = $req->get('category', '');
        $minPrice   = (float)$req->get('min_price', 0);
        $maxPrice   = (float)$req->get('max_price', 9999);
        $rating     = (float)$req->get('rating', 0);
        $sort       = $req->get('sort', 'latest');
        $search     = trim($req->get('q', ''));
        $tag        = $req->get('tag', '');

        $where  = ['p.status = "active"'];
        $params = [];

        if ($category) {
            $where[] = 'c.slug = ?';
            $params[] = $category;
        }
        if ($minPrice > 0)  { $where[] = 'p.price >= ?'; $params[] = $minPrice; }
        if ($maxPrice < 9999){ $where[] = 'p.price <= ?'; $params[] = $maxPrice; }
        if ($rating > 0)    { $where[] = 'p.avg_rating >= ?'; $params[] = $rating; }
        if ($search)        { $where[] = '(p.title LIKE ? OR p.description LIKE ?)'; $params[] = "%{$search}%"; $params[] = "%{$search}%"; }
        if ($tag)           { $where[] = 'EXISTS(SELECT 1 FROM product_tags pt WHERE pt.product_id = p.id AND pt.tag = ?)'; $params[] = $tag; }

        $orderBy = match($sort) {
            'price_asc'  => 'p.price ASC',
            'price_desc' => 'p.price DESC',
            'popular'    => 'p.total_sales DESC',
            'rating'     => 'p.avg_rating DESC',
            default      => 'p.created_at DESC',
        };

        $totalSql = 'SELECT COUNT(*) as cnt
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE ' . implode(' AND ', $where);
        $total = \App\Core\Database::fetchOne($totalSql, $params)['cnt'] ?? 0;

        // Force show all for the user's request
        $perPage = 500; 
        $offset  = 0; // Always start from beginning to show "all"

        $sql = 'SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE ' . implode(' AND ', $where) . "
                ORDER BY {$orderBy} LIMIT {$perPage} OFFSET {$offset}";

        $items = \App\Core\Database::fetchAll($sql, $params);
        $result = $this->paginate($items, (int)$total, $perPage);
        $result['pages'] = 1; // Force 1 page UI
        $categories = \App\Core\Database::fetchAll('SELECT * FROM categories ORDER BY sort_order ASC');

        $this->view('user/products/index', [
            'title'      => 'Products — ' . setting('site_name'),
            'products'   => $result['items'],
            'pagination' => $result,
            'categories' => $categories,
            'filters'    => compact('category', 'minPrice', 'maxPrice', 'rating', 'sort', 'search', 'tag'),
        ]);
    }

    // ── Product Detail ────────────────────────────────────────
    public function show(Request $req): void
    {
        $slug    = $req->param('slug');
        $product = Database::fetchOne(
            'SELECT p.*, c.name as category_name, c.slug as cat_slug
             FROM products p LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.slug = ? AND p.status = "active"',
            [$slug]
        );

        if (!$product) { Response::notFound(); }

        // Check flash sale
        if (!empty($product['flash_sale_price']) && !empty($product['flash_sale_ends']) && $product['flash_sale_ends'] > date('Y-m-d H:i:s')) {
            $product['display_price']    = $product['flash_sale_price'];
            $product['original_display'] = $product['price'];
            $product['on_flash_sale']    = true;
        } else {
            $product['display_price']    = $product['price'];
            $product['original_display'] = $product['original_price'];
            $product['on_flash_sale']    = false;
        }

        $images   = Database::fetchAll('SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order', [$product['id']]);
        $tags     = Database::fetchAll('SELECT tag FROM product_tags WHERE product_id = ?', [$product['id']]);
        $reviews  = Database::fetchAll(
            'SELECT r.*, u.name as reviewer_name, u.avatar as reviewer_avatar
             FROM reviews r JOIN users u ON u.id = r.user_id
             WHERE r.product_id = ? AND r.is_approved = 1
             ORDER BY r.created_at DESC LIMIT 10',
            [$product['id']]
        );

        $related = Database::fetchAll(
            'SELECT p.*, c.name as category_name FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.category_id = ? AND p.id != ? AND p.status = "active"
             ORDER BY p.avg_rating DESC LIMIT 4',
            [$product['category_id'], $product['id']]
        );

        // Review counts breakdown
        $ratingBreakdown = Database::fetchAll(
            'SELECT rating, COUNT(*) as cnt FROM reviews WHERE product_id = ? AND is_approved = 1 GROUP BY rating ORDER BY rating DESC',
            [$product['id']]
        );
        $totalReviews = array_sum(array_column($ratingBreakdown, 'cnt'));

        // Has user purchased this product?
        $hasPurchased = false;
        $userReview   = null;
        if (logged_in()) {
            $user         = auth();
            $hasPurchased = (bool)Database::fetchOne(
                'SELECT 1 FROM order_items oi JOIN orders o ON o.id=oi.order_id
                 WHERE o.user_id=? AND oi.product_id=? AND o.status="paid"',
                [$user['id'], $product['id']]
            );
            $userReview = Database::fetchOne(
                'SELECT * FROM reviews WHERE product_id=? AND user_id=?',
                [$product['id'], $user['id']]
            );
        }

        $this->view('user/products/show', [
            'title'           => $product['meta_title'] ?: $product['title'] . ' — ' . setting('site_name'),
            'meta_desc'       => $product['meta_desc'] ?: $product['short_desc'],
            'product'         => $product,
            'images'          => $images,
            'tags'            => array_column($tags, 'tag'),
            'reviews'         => $reviews,
            'related'         => $related,
            'ratingBreakdown' => $ratingBreakdown,
            'totalReviews'    => $totalReviews,
            'hasPurchased'    => $hasPurchased,
            'userReview'      => $userReview,
        ]);
    }

    // ── Universal Download Handler ───────────────────────────────────
    public function universalDownload(Request $req): void
    {
        $input = $req->param('tokenOrId');
        
        // 1. Check if it's a Signed URL (Numeric ID + GET params)
        if (is_numeric($input) && $req->get('token') && $req->get('expires')) {
            $this->processSignedDownload($req, (int)$input);
            return;
        }

        // 2. Otherwise treat it as a Legacy/Direct Token
        $this->processTokenDownload($req, $input);
    }

    // ── Secure Signed Download ───────────────────────────────────────
    protected function processSignedDownload(Request $req, int $id): void
    {
        $expires = (int)$req->get('expires');
        $token   = $req->get('token', '');

        if (!verify_download_link($id, $expires, $token)) {
            http_response_code(403);
            die('Invalid or tampered download link.');
        }

        $item = Database::fetchOne(
            'SELECT oi.*, p.file_path, p.title, o.status as order_status 
             FROM order_items oi
             JOIN products p ON p.id = oi.product_id
             JOIN orders o ON o.id = oi.order_id
             WHERE oi.id = ?',
            [$id]
        );

        if (!$item || $item['order_status'] !== 'paid') {
            Response::notFound();
        }

        if ($item['download_count'] >= $item['max_downloads']) {
            http_response_code(429);
            die('Download limit reached for this item.');
        }

        if (time() > $expires) {
            http_response_code(410);
            die('This download link has expired.');
        }

        Database::execute('UPDATE order_items SET download_count = download_count + 1 WHERE id = ?', [$id]);

        $filePath = STORAGE . '/' . $item['file_path'];
        if (!file_exists($filePath)) {
            die('File not found on server.');
        }

        $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $item['title']);
        $ext       = pathinfo($filePath, PATHINFO_EXTENSION);
        Response::download($filePath, "{$cleanName}.{$ext}");
    }

    // ── Token-based Download ────────────────────────────────────────
    protected function processTokenDownload(Request $req, string $token): void
    {
        $item = Database::fetchOne(
            'SELECT oi.*, p.file_path, p.title FROM order_items oi
             JOIN products p ON p.id = oi.product_id
             JOIN orders o ON o.id = oi.order_id
             WHERE oi.download_token = ? AND o.status = "paid"',
            [$token]
        );

        if (!$item) { 
            Response::notFound(); 
        }

        if ($item['token_expires'] && $item['token_expires'] < date('Y-m-d H:i:s')) {
            http_response_code(410);
            die('This download link has expired.');
        }

        if ($item['download_count'] >= $item['max_downloads']) {
            http_response_code(429);
            die('Download limit reached.');
        }

        Database::execute('UPDATE order_items SET download_count = download_count + 1 WHERE id = ?', [$item['id']]);

        $filePath = STORAGE . '/' . $item['file_path'];
        if (!file_exists($filePath)) {
            die('File not found.');
        }
        
        $filename = sanitize_filename($item['title']) . '_' . substr($token, 0, 8) . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
        Response::download($filePath, $filename);
    }
}

function sanitize_filename(string $name): string
{
    return preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
}
