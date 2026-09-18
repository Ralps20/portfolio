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

$position_id = isset($_GET['position_id']) ? (int) $_GET['position_id'] : 0;
$position_name = '';
$officials = [];

if ($position_id > 0) {
    $p = $conn->query("SELECT position FROM `position_list` WHERE position_id = '{$position_id}'");
    if ($p && $row = $p->fetchArray()) {
        $position_name = $row['position'];
    }

    $res = $conn->query("SELECT official_id, CONCAT(lastname, ', ', firstname, ', ', middlename) as fullname, contact
                          FROM official_list
                          WHERE position_id = '{$position_id}'
                          ORDER BY lastname ASC");
    if ($res) {
        while ($row = $res->fetchArray()) {
            $officials[] = $row;
        }
    }
}
?>
<div class="w-100 d-flex border-bottom border-dark py-1 mb-1">
    <div class="fs-5 col-auto flex-grow-1">
        <b>Officials holding <?php echo htmlspecialchars($position_name ?: '—') ?></b>
    </div>
    <div class="col-auto flex-grow-0 d-flex justify-content-end align-items-center">
        <span class="badge bg-success rounded-pill"><?php echo count($officials) ?></span>
    </div>
</div>
<div class="h-100 overflow-auto border rounded-1 border-dark">
    <?php if (count($officials) === 0): ?>
        <div class="text-center text-muted py-4">No official currently holds this position.</div>
    <?php else: ?>
        <ul class="list-group">
            <?php foreach ($officials as $o): ?>
            <li class="list-group-item d-flex align-items-center">
                <div class="col-auto flex-grow-1">
                    <div><?php echo htmlspecialchars($o['fullname']) ?></div>
                    <small class="text-muted"><?php echo htmlspecialchars($o['contact']) ?></small>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
