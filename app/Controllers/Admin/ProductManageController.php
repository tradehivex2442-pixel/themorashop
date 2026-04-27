<?php
// ============================================================
// THEMORA SHOP — Admin Product Management Controller
// ============================================================

namespace App\Controllers\Admin;

use App\Core\{Controller, Request, Database};

class ProductManageController extends Controller
{
    public function index(Request $req): void
    {
        $search   = $req->get('q', '');
        $category = $req->get('category', '');
        $status   = $req->get('status', '');
        $page     = max(1, (int)$req->get('page', 1));

        $where  = ['1=1'];
        $params = [];
        if ($search)   { $where[] = 'p.title LIKE ?'; $params[] = "%{$search}%"; }
        if ($category) { $where[] = 'p.category_id = ?'; $params[] = $category; }
        if ($status)   { $where[] = 'p.status = ?'; $params[] = $status; }

        $whereSql = implode(' AND ', $where);
        
        $countSql = "SELECT COUNT(*) AS c FROM products p WHERE {$whereSql}";
        $total = (int)(Database::fetchOne($countSql, $params)['c'] ?? 0);
        
        $perPage = 50;
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE {$whereSql} ORDER BY p.created_at DESC LIMIT {$perPage} OFFSET {$offset}";
        $products = Database::fetchAll($sql, $params);
        $result = $this->paginate($products, $total, $perPage);
        $categories = Database::fetchAll('SELECT * FROM categories ORDER BY sort_order');

        $this->view('admin/products/index', [
            'title'      => 'Products — Admin',
            'products'   => $result['items'],
            'pagination' => $result,
            'categories' => $categories,
            'filters'    => compact('search', 'category', 'status'),
        ], 'admin');
    }

    public function create(Request $req): void
    {
        $categories = Database::fetchAll('SELECT * FROM categories ORDER BY sort_order');
        $this->view('admin/products/form', [
            'title'      => 'Add Product — Admin',
            'product'    => null,
            'categories' => $categories,
        ], 'admin');
    }

    public function store(Request $req): void
    {
        $data = $this->extractProductData($req);
        $errors = $this->validateProduct($data);

        if ($errors) {
            flash_error(implode('<br>', $errors));
            $this->redirect(url('admin/products/create'));
        }

        $filePath    = $this->uploadFile($req->file('product_file'), 'products');
        $thumbnail   = $this->uploadFile($req->file('thumbnail'), 'thumbnails', true);
        $previewFile = $this->uploadFile($req->file('preview_file'), 'previews');

        $data['slug']         = $this->uniqueSlug($data['title']);
        $data['file_path']    = $filePath;
        $data['thumbnail']    = $thumbnail;
        $data['preview_file'] = $previewFile;

        $productId = Database::insert(
            'INSERT INTO products (title, slug, description, short_desc, price, original_price, category_id, file_path, preview_file, thumbnail, status, download_limit, meta_title, meta_desc, demo_video_url, version)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
            [
                $data['title'], $data['slug'], $data['description'], $data['short_desc'],
                $data['price'], $data['original_price'], $data['category_id'],
                $data['file_path'], $data['preview_file'], $data['thumbnail'],
                $data['status'], $data['download_limit'], $data['meta_title'], $data['meta_desc'],
                $data['demo_video_url'], $data['version']
            ]
        );

        // Tags
        if (!empty($data['tags'])) {
            foreach (explode(',', $data['tags']) as $tag) {
                $tag = trim($tag);
                if ($tag) Database::execute('INSERT INTO product_tags (product_id, tag) VALUES (?,?)', [$productId, $tag]);
            }
        }

        Database::execute('INSERT INTO activity_logs (admin_id, action, target_type, target_id) VALUES (?,?,?,?)', [auth()['id'], 'Created product', 'product', $productId]);
        flash_success('Product created successfully!');
        $this->redirect(url('admin/products'));
    }

    public function edit(Request $req): void
    {
        $product    = Database::fetchOne('SELECT * FROM products WHERE id=?', [$req->param('id')]);
        if (!$product) { flash_error('Product not found.'); $this->redirect(url('admin/products')); }
        $tags       = Database::fetchAll('SELECT tag FROM product_tags WHERE product_id=?', [$product['id']]);
        $categories = Database::fetchAll('SELECT * FROM categories ORDER BY sort_order');
        $product['tags'] = implode(', ', array_column($tags, 'tag'));

        $this->view('admin/products/form', [
            'title'      => 'Edit Product — Admin',
            'product'    => $product,
            'categories' => $categories,
        ], 'admin');
    }

    public function update(Request $req): void
    {
        $id   = $req->param('id');
        $data = $this->extractProductData($req);

        $product = Database::fetchOne('SELECT * FROM products WHERE id=?', [$id]);
        if (!$product) { flash_error('Product not found.'); $this->redirect(url('admin/products')); }

        // Handle new file uploads
        if ($req->file('product_file') && $req->file('product_file')['error'] === 0) {
            $data['file_path'] = $this->uploadFile($req->file('product_file'), 'products');
        } else {
            $data['file_path'] = $product['file_path'];
        }
        if ($req->file('thumbnail') && $req->file('thumbnail')['error'] === 0) {
            $data['thumbnail'] = $this->uploadFile($req->file('thumbnail'), 'thumbnails', true);
        } else {
            $data['thumbnail'] = $product['thumbnail'];
        }

        Database::execute(
            'UPDATE products SET title=?, description=?, short_desc=?, price=?, original_price=?,
             category_id=?, file_path=?, thumbnail=?, status=?, download_limit=?,
             meta_title=?, meta_desc=?, demo_video_url=?, version=? WHERE id=?',
            [
                $data['title'], $data['description'], $data['short_desc'], $data['price'],
                $data['original_price'], $data['category_id'], $data['file_path'],
                $data['thumbnail'], $data['status'], $data['download_limit'],
                $data['meta_title'], $data['meta_desc'], $data['demo_video_url'],
                $data['version'], $id,
            ]
        );

        // Refresh tags
        Database::execute('DELETE FROM product_tags WHERE product_id=?', [$id]);
        if (!empty($data['tags'])) {
            foreach (explode(',', $data['tags']) as $tag) {
                $tag = trim($tag);
                if ($tag) Database::execute('INSERT INTO product_tags (product_id, tag) VALUES (?,?)', [$id, $tag]);
            }
        }

        Database::execute('INSERT INTO activity_logs (admin_id, action, target_type, target_id) VALUES (?,?,?,?)', [auth()['id'], 'Updated product', 'product', $id]);
        flash_success('Product updated!');
        $this->redirect(url('admin/products'));
    }

    public function delete(Request $req): void
    {
        $id = $req->param('id');
        Database::execute('DELETE FROM products WHERE id=?', [$id]);
        Database::execute('INSERT INTO activity_logs (admin_id, action, target_type, target_id) VALUES (?,?,?,?)', [auth()['id'], 'Deleted product', 'product', $id]);
        flash_success('Product deleted.');
        $this->redirect(url('admin/products'));
    }

    public function reviews(Request $req): void
    {
        $reviews = Database::fetchAll(
            'SELECT r.*, u.name as user_name, p.title as product_title FROM reviews r JOIN users u ON u.id=r.user_id JOIN products p ON p.id=r.product_id ORDER BY r.created_at DESC'
        );
        $this->view('admin/reviews', ['title' => 'Reviews — Admin', 'reviews' => $reviews], 'admin');
    }

    public function reviewAction(Request $req): void
    {
        $id     = $req->param('id');
        $action = $req->post('action');
        if ($action === 'approve') Database::execute('UPDATE reviews SET is_approved=1 WHERE id=?', [$id]);
        elseif ($action === 'delete') {
            Database::execute('DELETE FROM reviews WHERE id=?', [$id]);
            // Recalculate avg rating
            $review = Database::fetchOne('SELECT product_id FROM reviews WHERE id=?', [$id]) 
                   ?? Database::fetchOne('SELECT id as noop FROM products LIMIT 1');
        }
        flash_success('Review updated.');
        $this->redirect(url('admin/reviews'));
    }

    // ── Private helpers ───────────────────────────────────────
    private function extractProductData(Request $req): array
    {
        return [
            'title'          => trim($req->post('title', '')),
            'description'    => $req->post('description', ''),
            'short_desc'     => trim($req->post('short_desc', '')),
            'price'          => (float)$req->post('price', 0),
            'original_price' => $req->post('original_price') ?: null,
            'category_id'    => (int)$req->post('category_id'),
            'status'         => $req->post('status', 'active'),
            'download_limit' => (int)$req->post('download_limit', 5),
            'meta_title'     => trim($req->post('meta_title', '')),
            'meta_desc'      => trim($req->post('meta_desc', '')),
            'demo_video_url' => trim($req->post('demo_video_url', '')),
            'tags'           => trim($req->post('tags', '')),
            'version'        => trim($req->post('version', '1.0')),
        ];
    }

    private function validateProduct(array $data): array
    {
        $errors = [];
        if (!$data['title'])        $errors[] = 'Title is required.';
        if ($data['price'] < 0)     $errors[] = 'Price must be non-negative.';
        if (!$data['category_id'])   $errors[] = 'Category is required.';
        return $errors;
    }

    private function uploadFile(?array $file, string $dir, bool $isPublic = false): ?string
    {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) return null;

        $dest = $isPublic
            ? PUB_PATH . '/assets/uploads/' . $dir
            : STORAGE . '/' . $dir;

        if (!is_dir($dest)) @mkdir($dest, 0755, true);

        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = uniqid('', true) . '.' . $ext;
        move_uploaded_file($file['tmp_name'], $dest . '/' . $filename);

        return $isPublic ? 'uploads/' . $dir . '/' . $filename : $dir . '/' . $filename;
    }

    private function uniqueSlug(string $title): string
    {
        $base  = slugify($title);
        $slug  = $base;
        $count = 1;
        while (Database::fetchOne('SELECT id FROM products WHERE slug=?', [$slug])) {
            $slug = $base . '-' . $count++;
        }
        return $slug;
    }
}
