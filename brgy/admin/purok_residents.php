<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . "/../DBConnection.php");
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_id'] <= 0) {
    http_response_code(403);
    exit('Access denied.');
}
$conn = new DBConnection();

$purok_id = isset($_GET['purok_id']) ? (int) $_GET['purok_id'] : 0;
$purok_name = '';
$residents = [];

if ($purok_id > 0) {
    $p = $conn->query("SELECT purok FROM `purok_list` WHERE purok_id = '{$purok_id}'");
    if ($p && $row = $p->fetchArray()) {
        $purok_name = $row['purok'];
    }

    $res = $conn->query("SELECT household_id, house_no, CONCAT(lastname, ', ', firstname, ', ', middlename) as fullname, contact
                          FROM household_list
                          WHERE purok_id = '{$purok_id}'
                          ORDER BY lastname ASC");
    if ($res) {
        while ($row = $res->fetchArray()) {
            $residents[] = $row;
        }
    }
}
?>
<div class="w-100 d-flex border-bottom border-dark py-1 mb-1">
    <div class="fs-5 col-auto flex-grow-1">
        <b>Residents in <?php echo htmlspecialchars($purok_name ?: '—') ?></b>
    </div>
    <div class="col-auto flex-grow-0 d-flex justify-content-end align-items-center">
        <span class="badge bg-success rounded-pill"><?php echo count($residents) ?> resident<?php echo count($residents) === 1 ? '' : 's' ?></span>
    </div>
</div>
<div class="h-100 overflow-auto border rounded-1 border-dark">
    <?php if (count($residents) === 0): ?>
        <div class="text-center text-muted py-4">No residents recorded in this purok yet.</div>
    <?php else: ?>
        <ul class="list-group">
            <?php foreach ($residents as $r): ?>
            <li class="list-group-item d-flex align-items-center">
                <div class="col-auto flex-grow-1">
                    <div><?php echo htmlspecialchars($r['fullname']) ?></div>
                    <small class="text-muted">#<?php echo htmlspecialchars($r['house_no']) ?> &middot; <?php echo htmlspecialchars($r['contact']) ?></small>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
