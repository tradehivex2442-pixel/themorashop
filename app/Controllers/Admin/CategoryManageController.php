<?php
// ============================================================
// THEMORA SHOP — Admin Category Management Controller
// ============================================================

namespace App\Controllers\Admin;

use App\Core\{Controller, Request, Database};

class CategoryManageController extends Controller
{
    public function index(Request $req): void
    {
        $categories = Database::fetchAll('SELECT * FROM categories ORDER BY sort_order ASC');
        $total      = count($categories);
        
        $this->view('admin/categories/index', [
            'title'      => 'Categories — Admin',
            'categories' => $categories,
            'total'      => $total,
        ], 'admin');
    }

    public function create(Request $req): void
    {
        $this->view('admin/categories/form', [
            'title'    => 'Add Category — Admin',
            'category' => null,
        ], 'admin');
    }

    public function store(Request $req): void
    {
        $name      = trim($req->post('name', ''));
        $slug      = trim($req->post('slug', '')) ?: slugify($name);
        $icon      = trim($req->post('icon', 'bi-folder'));
        $sortOrder = (int)$req->post('sort_order', 0);

        if (!$name) {
            flash_error('Category name is required.');
            $this->redirect(url('admin/categories/create'));
        }

        // Check if slug exists
        if (Database::fetchOne('SELECT id FROM categories WHERE slug = ?', [$slug])) {
            $slug .= '-' . rand(100, 999);
        }

        Database::execute(
            'INSERT INTO categories (name, slug, icon, sort_order) VALUES (?, ?, ?, ?)',
            [$name, $slug, $icon, $sortOrder]
        );

        log_activity('Created category', "Category: {$name}");
        flash_success('Category created successfully!');
        $this->redirect(url('admin/categories'));
    }

    public function edit(Request $req): void
    {
        $id = $req->param('id');
        $category = Database::fetchOne('SELECT * FROM categories WHERE id = ?', [$id]);

        if (!$category) {
            flash_error('Category not found.');
            $this->redirect(url('admin/categories'));
        }

        $this->view('admin/categories/form', [
            'title'    => 'Edit Category — Admin',
            'category' => $category,
        ], 'admin');
    }

    public function update(Request $req): void
    {
        $id        = $req->param('id');
        $name      = trim($req->post('name', ''));
        $slug      = trim($req->post('slug', '')) ?: slugify($name);
        $icon      = trim($req->post('icon', 'bi-folder'));
        $sortOrder = (int)$req->post('sort_order', 0);

        if (!$name) {
            flash_error('Category name is required.');
            $this->redirect(url("admin/categories/{$id}/edit"));
        }

        // Check if slug exists for others
        $exists = Database::fetchOne('SELECT id FROM categories WHERE slug = ? AND id != ?', [$slug, $id]);
        if ($exists) {
            $slug .= '-' . rand(100, 999);
        }

        Database::execute(
            'UPDATE categories SET name = ?, slug = ?, icon = ?, sort_order = ? WHERE id = ?',
            [$name, $slug, $icon, $sortOrder, $id]
        );

        log_activity('Updated category', "Category ID: {$id}");
        flash_success('Category updated successfully!');
        $this->redirect(url('admin/categories'));
    }

    public function delete(Request $req): void
    {
        $id = $req->param('id');
        
        // Check if products exist in this category
        $hasProducts = Database::fetchOne('SELECT id FROM products WHERE category_id = ? LIMIT 1', [$id]);
        if ($hasProducts) {
            flash_error('Cannot delete category. It contains products.');
            $this->redirect(url('admin/categories'));
        }

        Database::execute('DELETE FROM categories WHERE id = ?', [$id]);

        log_activity('Deleted category', "Category ID: {$id}");
        flash_success('Category deleted.');
        $this->redirect(url('admin/categories'));
    }
}
