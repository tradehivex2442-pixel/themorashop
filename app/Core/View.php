<?php
// ============================================================
// THEMORA SHOP — View Renderer
// ============================================================

namespace App\Core;

class View
{
    private string $viewPath;

    public function __construct()
    {
        $this->viewPath = APP_PATH . '/Views/';
    }

    public function render(string $view, array $data = [], string $layout = 'user'): void
    {
        extract($data);

        // Buffer the view content
        ob_start();
        $file = $this->viewPath . str_replace('.', '/', $view) . '.php';
        if (!file_exists($file)) {
            echo "<p>View not found: {$view}</p>";
        } else {
            require $file;
        }
        $content = ob_get_clean();

        // Render layout
        $layoutFile = $this->viewPath . 'layouts/' . $layout . '.php';
        if ($layout && file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    public function partial(string $partial, array $data = []): void
    {
        extract($data);
        $file = $this->viewPath . 'partials/' . $partial . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }

    public static function e(mixed $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
