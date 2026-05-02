<?php
// ============================================================
// THEMORA SHOP — Support / FAQ / Ticket Controller
// ============================================================
namespace App\Controllers\Support;
use App\Core\{Controller, Request, Database, Session};

class FaqController extends Controller
{
    public function index(Request $req): void
    {
        $search = trim($req->get('q', ''));
        if ($search) {
            $faqs = Database::fetchAll('SELECT * FROM faqs WHERE is_published=1 AND (question ILIKE ? OR answer ILIKE ?) ORDER BY sort_order', ["%{$search}%", "%{$search}%"]);
        } else {
            $faqs = Database::fetchAll('SELECT * FROM faqs WHERE is_published=1 ORDER BY sort_order');
        }
        $this->view('user/support/faq', ['title' => 'FAQ — ' . setting('site_name'), 'faqs' => $faqs, 'search' => $search]);
    }

    public function contact(Request $req): void
    {
        $this->view('user/support/contact', ['title' => 'Contact Us', 'success' => Session::getFlash('success'), 'error' => Session::getFlash('error')]);
    }

    public function sendContact(Request $req): void
    {
        $name    = trim($req->post('name', ''));
        $email   = trim($req->post('email', ''));
        $message = trim($req->post('message', ''));
        if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$message) {
            flash_error('Please fill all fields correctly.'); $this->redirect(url('contact'));
        }
        // Log as ticket
        $userId = logged_in() ? auth()['id'] : null;
        if (!$userId) {
            // Try to find or create minimal user entry for guest
            $existing = Database::fetchOne('SELECT id FROM users WHERE email=?', [$email]);
            $userId   = $existing['id'] ?? Database::insert('INSERT INTO users (name, email, password, role, email_verified) VALUES (?,?,?,?,1)', [$name, $email, '', 'user']);
        }
        $ticketId = Database::insert('INSERT INTO tickets (user_id, subject) VALUES (?,?)', [$userId, "Contact: {$name}"]);
        Database::execute('INSERT INTO ticket_messages (ticket_id, sender_id, body) VALUES (?,?,?)', [$ticketId, $userId, $message]);
        flash_success('Your message has been received! We\'ll respond within 24 hours.');
        $this->redirect(url('contact'));
    }
}

class TicketController extends Controller
{
    public function index(Request $req): void
    {
        $this->requireAuth();
        $tickets = Database::fetchAll('SELECT * FROM tickets WHERE user_id=? ORDER BY updated_at DESC', [auth()['id']]);
        $this->view('user/support/tickets', ['title' => 'My Tickets', 'tickets' => $tickets]);
    }

    public function createForm(Request $req): void
    {
        $this->requireAuth();
        $this->view('user/support/new-ticket', ['title' => 'Open Ticket']);
    }

    public function create(Request $req): void
    {
        $this->requireAuth();
        $subject = trim($req->post('subject', ''));
        $body    = trim($req->post('body', ''));
        if (!$subject || !$body) { flash_error('Both fields required.'); $this->redirect(url('dashboard/tickets/new')); }
        $tid = Database::insert('INSERT INTO tickets (user_id, subject) VALUES (?,?)', [auth()['id'], $subject]);
        Database::execute('INSERT INTO ticket_messages (ticket_id, sender_id, body) VALUES (?,?,?)', [$tid, auth()['id'], $body]);
        flash_success('Ticket opened!');
        $this->redirect(url("dashboard/tickets/{$tid}"));
    }

    public function show(Request $req): void
    {
        $this->requireAuth();
        $ticket   = Database::fetchOne('SELECT * FROM tickets WHERE id=? AND user_id=?', [$req->param('id'), auth()['id']]);
        if (!$ticket) { flash_error('Ticket not found.'); $this->redirect(url('dashboard/tickets')); }
        $messages = Database::fetchAll('SELECT tm.*, u.name as sender_name FROM ticket_messages tm JOIN users u ON u.id=tm.sender_id WHERE tm.ticket_id=? ORDER BY tm.created_at ASC', [$ticket['id']]);
        $this->view('user/support/ticket-detail', ['title' => $ticket['subject'], 'ticket' => $ticket, 'messages' => $messages]);
    }

    public function reply(Request $req): void
    {
        $this->requireAuth();
        $id   = $req->param('id');
        $body = trim($req->post('body', ''));
        $tick = Database::fetchOne('SELECT id FROM tickets WHERE id=? AND user_id=?', [$id, auth()['id']]);
        if (!$tick || !$body) { $this->redirect(url("dashboard/tickets/{$id}")); }
        Database::execute('INSERT INTO ticket_messages (ticket_id, sender_id, body) VALUES (?,?,?)', [$id, auth()['id'], $body]);
        Database::execute('UPDATE tickets SET updated_at=NOW(), status="open" WHERE id=?', [$id]);
        flash_success('Reply sent.');
        $this->redirect(url("dashboard/tickets/{$id}"));
    }
}
