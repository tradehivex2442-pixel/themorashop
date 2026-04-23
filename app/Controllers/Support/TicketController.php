<?php
// ============================================================
// THEMORA SHOP — TicketController (CORRECTED to match DB schema)
// Tables: tickets, ticket_messages
// ============================================================
namespace App\Controllers\Support;

use App\Core\{Controller, Database, Session};

class TicketController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $userId  = auth()['id'];
        $tickets = Database::fetchAll(
            "SELECT t.*,
                (SELECT COUNT(*) FROM ticket_messages WHERE ticket_id = t.id) AS reply_count
             FROM tickets t
             WHERE t.user_id = ?
             ORDER BY t.updated_at DESC",
            [$userId]
        );
        $this->view('user/support/tickets', compact('tickets'), 'user', 'My Tickets');
    }

    public function createForm(): void
    {
        $this->requireAuth();
        $userId = auth()['id'];
        $orders = Database::fetchAll(
            "SELECT id, total, created_at FROM orders
             WHERE user_id = ? AND status = 'paid'
             ORDER BY created_at DESC LIMIT 20",
            [$userId]
        );
        $this->view('user/support/new-ticket', compact('orders'), 'user', 'New Ticket');
    }

    public function create(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $subject  = $this->input('subject', '');
        $priority = in_array($this->input('priority'), ['low','medium','high','urgent'])
                        ? $this->input('priority') : 'medium';
        $message  = trim($this->input('message', ''));
        $orderId  = (int)$this->input('related_order_id', 0) ?: null;
        $userId   = auth()['id'];

        if (empty($subject) || empty($message)) {
            Session::flash('error', 'Please fill in all required fields.');
            $this->redirect(url('dashboard/tickets/new'));
        }

        // Insert ticket
        Database::query(
            "INSERT INTO tickets (user_id, subject, priority, status) VALUES (?,?,?,'open')",
            [$userId, $subject, $priority]
        );
        $ticketId = Database::getInstance()->lastInsertId();

        // Insert first message
        Database::query(
            "INSERT INTO ticket_messages (ticket_id, sender_id, body, is_admin) VALUES (?,?,?,0)",
            [$ticketId, $userId, $message]
        );

        log_activity('ticket_created', "Ticket #{$ticketId}: {$subject}", $userId);
        Session::flash('success', "Ticket #$ticketId opened. We'll reply soon!");
        $this->redirect("dashboard/tickets/{$ticketId}");
    }

    public function show(int $id): void
    {
        $this->requireAuth();
        $userId = auth()['id'];

        $ticket = Database::fetchOne(
            "SELECT t.*, u.name AS user_name, u.email AS user_email
             FROM tickets t
             LEFT JOIN users u ON u.id = t.user_id
             WHERE t.id = ? AND t.user_id = ?",
            [$id, $userId]
        );

        if (!$ticket) { $this->abort(404); }

        $replies = Database::fetchAll(
            "SELECT m.*, u.name AS sender_name
             FROM ticket_messages m
             LEFT JOIN users u ON u.id = m.sender_id
             WHERE m.ticket_id = ?
             ORDER BY m.created_at ASC",
            [$id]
        );

        // Map is_admin → is_admin_reply alias for the view
        $replies = array_map(fn($r) => array_merge($r, ['is_admin_reply' => $r['is_admin']]), $replies);

        $this->view('user/support/ticket-detail', compact('ticket', 'replies'), 'user', "Ticket #$id");
    }

    public function reply(int $id): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $userId  = auth()['id'];
        $message = trim($this->input('message', ''));

        if (empty($message)) {
            Session::flash('error', 'Reply cannot be empty.');
            $this->redirect("dashboard/tickets/{$id}");
        }

        $ticket = Database::fetchOne(
            'SELECT id, status FROM tickets WHERE id = ? AND user_id = ?',
            [$id, $userId]
        );

        if (!$ticket || in_array($ticket['status'], ['resolved', 'closed'])) {
            Session::flash('error', 'This ticket is closed.');
            $this->redirect("dashboard/tickets");
        }

        Database::query(
            "INSERT INTO ticket_messages (ticket_id, sender_id, body, is_admin) VALUES (?,?,?,0)",
            [$id, $userId, $message]
        );

        Database::query(
            "UPDATE tickets SET status='open', updated_at=NOW() WHERE id=?",
            [$id]
        );

        Session::flash('success', 'Reply sent!');
        $this->redirect("dashboard/tickets/{$id}");
    }
}
