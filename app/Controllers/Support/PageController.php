<?php
// ============================================================
// THEMORA SHOP — Page Controller (Legal & Misc)
// ============================================================

namespace App\Controllers\Support;

use App\Core\{Controller, Request};

class PageController extends Controller
{
    public function privacy(Request $req): void
    {
        $this->view('user/pages/privacy', [
            'title' => 'Privacy Policy — ' . setting('site_name')
        ]);
    }

    public function terms(Request $req): void
    {
        $this->view('user/pages/terms', [
            'title' => 'Terms of Service — ' . setting('site_name')
        ]);
    }

    public function refund(Request $req): void
    {
        $this->view('user/pages/refund', [
            'title' => 'Refund Policy — ' . setting('site_name')
        ]);
    }
}
