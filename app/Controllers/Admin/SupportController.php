<?php
// ============================================================
// THEMORA SHOP — Admin SupportController (CORRECTED schema)
// Tables: tickets, ticket_messages
// ============================================================
namespace App\Controllers\Admin;

use App\Core\{Controller, Database, Session};

class SupportController extends Controller
{
    public function index(): void
    {
        $this->requireAdmin();
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset  = ($page - 1) * $perPage;

        $where  = '1=1';
        $params = [];
        $filters = [];

        if (!empty($_GET['q'])) {
            $where  .= ' AND t.subject LIKE ?';
            $params[] = '%' . $_GET['q'] . '%';
            $filters['search'] = $_GET['q'];
        }
        if (!empty($_GET['status']) && in_array($_GET['status'], ['open','in-progress','resolved','closed'])) {
            $where .= ' AND t.status = ?';
            $params[] = $_GET['status'];
            $filters['status'] = $_GET['status'];
        }
        if (!empty($_GET['priority']) && in_array($_GET['priority'], ['low','medium','high','urgent'])) {
            $where .= ' AND t.priority = ?';
            $params[] = $_GET['priority'];
            $filters['priority'] = $_GET['priority'];
        }

        $total = Database::fetchOne("SELECT COUNT(*) as c FROM tickets t WHERE {$where}", $params)['c'];

        $tickets = Database::fetchAll(
            "SELECT t.*, u.name AS user_name, u.email AS user_email,
                (SELECT COUNT(*) FROM ticket_messages WHERE ticket_id=t.id) AS reply_count
             FROM tickets t
             LEFT JOIN users u ON u.id = t.user_id
             WHERE {$where}
             ORDER BY t.updated_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        $openCount  = Database::fetchOne("SELECT COUNT(*) as c FROM tickets WHERE status IN ('open','in-progress')")['c'];
        $totalCount = Database::fetchOne('SELECT COUNT(*) as c FROM tickets')['c'];
        $pagination = $this->paginate($tickets, $total, $perPage);

        $this->view('admin/support/index', compact('tickets','filters','pagination','openCount','totalCount'), 'admin', 'Support Tickets');
    }

    public function show(int $id): void
    {
        $this->requireAdmin();
        $ticket = Database::fetchOne(
            "SELECT t.*, u.name AS user_name, u.email AS user_email
             FROM tickets t LEFT JOIN users u ON u.id = t.user_id WHERE t.id = ?",
            [$id]
        );
        if (!$ticket) $this->abort(404);

        $replies = Database::fetchAll(
            "SELECT m.*, u.name AS sender_name
             FROM ticket_messages m LEFT JOIN users u ON u.id = m.sender_id
             WHERE m.ticket_id = ? ORDER BY m.created_at ASC",
            [$id]
        );
        $replies = array_map(fn($r) => array_merge($r, ['is_admin_reply' => $r['is_admin']]), $replies);

        $this->view('admin/support/show', compact('ticket','replies'), 'admin', "Ticket #{$id}");
    }

    public function reply(int $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();

        $message = trim($_POST['message'] ?? '');
        if (empty($message)) {
            Session::flash('error', 'Reply cannot be empty.');
            $this->redirect("admin/tickets/{$id}");
        }

        $adminId = auth()['id'];
        Database::query(
            "INSERT INTO ticket_messages (ticket_id, sender_id, body, is_admin) VALUES (?,?,?,1)",
            [$id, $adminId, $message]
        );

        $newStatus = isset($_POST['resolve']) ? 'resolved' : 'in-progress';
        Database::query(
            "UPDATE tickets SET status=?, updated_at=NOW() WHERE id=?",
            [$newStatus, $id]
        );

        log_activity('ticket_replied', "Ticket #{$id}", $adminId);
        Session::flash('success', 'Reply sent!');
        $this->redirect("admin/tickets/{$id}");
    }

    public function assign(int $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();

        $status = $_POST['status'] ?? 'open';
        if (!in_array($status, ['open','in-progress','resolved','closed'])) {
            $this->redirect("admin/tickets/{$id}");
        }

        Database::query("UPDATE tickets SET status=?, updated_at=NOW() WHERE id=?", [$status, $id]);
        Session::flash('success', 'Status updated.');
        $this->redirect("admin/tickets/{$id}");
    }

    public function faqs(): void
    {
        $this->requireAdmin();
        $faqs = Database::fetchAll("SELECT * FROM faqs ORDER BY sort_order ASC, id ASC");
        $this->view('admin/faqs/index', compact('faqs'), 'admin', 'Manage FAQs');
    }

    public function storeFaq(): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();

        Database::query(
            "INSERT INTO faqs (question, answer, sort_order, is_published) VALUES (?,?,?,?)",
            [
                trim($_POST['question'] ?? ''),
                trim($_POST['answer'] ?? ''),
                (int)($_POST['sort_order'] ?? 0),
                isset($_POST['is_published']) ? 1 : 0,
            ]
        );
        Session::flash('success', 'FAQ added!');
        $this->redirect('admin/faqs');
    }

    public function deleteFaq(int $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();
        Database::query("DELETE FROM faqs WHERE id=?", [$id]);
        Session::flash('success', 'FAQ deleted.');
        $this->redirect('admin/faqs');
    }
}
