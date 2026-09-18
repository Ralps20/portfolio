<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . "/../DBConnection.php");

// ── Auth guard ──
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_id'] <= 0) {
    header("Location: login.php");
    exit;
}

// ── Shared DB connection used by every page in admin/ ──
$conn = new DBConnection();

$current_page = basename($_SERVER['PHP_SELF']);
$barangay_name = isset($_SESSION['system_info']['barangay_name']) ? $_SESSION['system_info']['barangay_name'] : 'Barangay Management';
$admin_name    = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Administrator';
$admin_type    = (isset($_SESSION['type']) && $_SESSION['type'] == 1) ? 'Administrator' : 'Staff';
$admin_initial = strtoupper(substr($admin_name, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($barangay_name) ?> | Barangay Management System</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700;9..144,900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<!-- Loaded here (in <head>) on purpose: every page's own inline <script> block
     runs immediately as the HTML is parsed and expects $ / bootstrap to already
     exist. Loading these at the bottom of footer.php was the bug that made
     Add New / Edit / Delete / Print appear to do nothing. -->
<script src="../js/jquery-3.6.0.min.js"></script>
<script src="../js/popper.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<style>
  :root{
    --ink:#182620; --ink-soft:#5b6960; --paper:#f4f2ea; --card:#fffdf8; --line:#e6e1d2;
    --primary:#1f6f54; --primary-dark:#0f3d2c; --primary-light:#e6f2ec;
    --gold:#c9973b; --gold-light:#faf1de;
    --coral:#c73e51; --coral-light:#fbe9ec;
    --sidebar-w:264px;
  }
  *{ box-sizing:border-box; }
  body{ margin:0; background:var(--paper); color:var(--ink); font-family:'Inter',system-ui,sans-serif; -webkit-font-smoothing:antialiased; }
  h1,h2,h3,.brand-word{ font-family:'Fraunces',serif; }
  a{ text-decoration:none; color:inherit; }

  .seal{ width:42px; height:42px; border-radius:50%; background:radial-gradient(circle at 32% 28%, #2c8a68, var(--primary-dark) 78%); display:flex; align-items:center; justify-content:center; box-shadow:0 0 0 3px rgba(201,151,59,.35), inset 0 0 0 1px rgba(255,255,255,.15); flex-shrink:0; }
  .seal i{ color:var(--gold); font-size:17px; }

  .sidebar{ position:fixed; top:0; left:0; bottom:0; width:var(--sidebar-w); background:linear-gradient(190deg, var(--primary-dark) 0%, var(--primary) 130%); color:#eef7f1; display:flex; flex-direction:column; z-index:40; transition:transform .25s ease; }
  .sidebar-brand{ display:flex; align-items:center; gap:12px; padding:24px 22px 20px 22px; border-bottom:1px solid rgba(255,255,255,.12); }
  .sidebar-brand .brand-word{ font-weight:700; font-size:19px; color:#fff; line-height:1.1; }
  .sidebar-brand .brand-sub{ font-size:11px; color:rgba(238,247,241,.65); letter-spacing:.4px; }
  .sidebar-nav{ padding:18px 12px; flex:1; overflow-y:auto; }
  .nav-group-label{ font-size:11px; text-transform:uppercase; letter-spacing:.8px; color:rgba(238,247,241,.45); padding:14px 12px 6px 12px; font-weight:700; }
  .nav-item{ display:flex; align-items:center; gap:12px; padding:11px 14px; margin:2px 0; border-radius:10px; color:rgba(238,247,241,.8); font-weight:500; font-size:14.5px; position:relative; cursor:pointer; }
  .nav-item i{ width:18px; text-align:center; font-size:15px; color:rgba(238,247,241,.65); }
  .nav-item:hover{ background:rgba(255,255,255,.08); color:#fff; }
  .nav-item.active{ background:rgba(255,255,255,.12); color:#fff; }
  .nav-item.active::before{ content:""; position:absolute; left:-12px; top:8px; bottom:8px; width:4px; border-radius:0 4px 4px 0; background:var(--gold); }
  .nav-item.active i{ color:var(--gold); }
  .sidebar-foot{ padding:16px; border-top:1px solid rgba(255,255,255,.12); display:flex; align-items:center; gap:10px; }
  .admin-chip{ width:36px; height:36px; border-radius:50%; background:var(--gold); display:flex; align-items:center; justify-content:center; font-weight:700; color:var(--primary-dark); flex-shrink:0; }
  .sidebar-foot .name{ font-size:13.5px; font-weight:600; color:#fff; }
  .sidebar-foot .role{ font-size:11px; color:rgba(238,247,241,.6); }
  .sidebar-foot .logout{ margin-left:auto; color:rgba(238,247,241,.6); font-size:15px; cursor:pointer; }

  .main{ margin-left:var(--sidebar-w); min-height:100vh; }
  .topbar{ display:flex; align-items:center; gap:16px; padding:16px 30px; background:var(--card); border-bottom:1px solid var(--line); }
  .topbar-title{ font-weight:700; font-size:15px; }
  .topbar-date{ font-size:12px; color:var(--ink-soft); }
  .topbar-right{ margin-left:auto; display:flex; align-items:center; gap:16px; }
  .topbar-icon{ position:relative; font-size:16px; color:var(--ink-soft); cursor:pointer; }
  .topbar-icon .dot{ position:absolute; top:-2px; right:-3px; width:7px; height:7px; border-radius:50%; background:var(--coral); }
  .hamburger{ display:none; background:none; border:none; font-size:18px; cursor:pointer; color:var(--ink); }
  .backdrop{ display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:30; }
  .content{ padding:26px 30px 50px; }
  .page-head h1{ margin:0 0 4px; font-size:26px; }
  .page-head p{ margin:0 0 20px; color:var(--ink-soft); font-size:14px; }

  .card{ background:var(--card); border:1px solid var(--line); border-radius:12px; }
  .card-header{ padding:16px 20px; border-bottom:1px solid var(--line); align-items:center; }
  .card-title{ margin:0; font-size:17px; }
  .card-body{ padding:18px 20px; }
  .table{ margin-bottom:0; }
  .btn-dark{ background:var(--primary-dark); border-color:var(--primary-dark); }
  .btn-primary{ background:var(--primary); border-color:var(--primary); }

  @media (max-width: 991px){
    .sidebar{ transform:translateX(-100%); }
    .sidebar.show{ transform:translateX(0); }
    .main{ margin-left:0; }
    .hamburger{ display:block; }
    .backdrop.show{ display:block; }
  }
</style>
</head>
<body>

<div class="backdrop" id="backdrop"></div>

<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="seal"><i class="fa-solid fa-house-chimney-window"></i></div>
    <div>
      <div class="brand-word">BMS</div>
      <div class="brand-sub"><?php echo htmlspecialchars($barangay_name) ?></div>
    </div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-group-label">Overview</div>
    <a class="nav-item <?php echo $current_page=='index.php'?'active':'' ?>" href="index.php"><i class="fa-solid fa-house"></i> Home</a>

    <div class="nav-group-label">Records</div>
    <a class="nav-item <?php echo $current_page=='household.php'?'active':'' ?>" href="household.php"><i class="fa-solid fa-table-list"></i> Households</a>
    <a class="nav-item <?php echo $current_page=='appointed_officials.php'?'active':'' ?>" href="appointed_officials.php"><i class="fa-solid fa-user-tie"></i> Officials</a>
    <a class="nav-item <?php echo $current_page=='clearance.php'?'active':'' ?>" href="clearance.php"><i class="fa-solid fa-file-lines"></i> Individual Clearance</a>
    <a class="nav-item <?php echo $current_page=='business_clearance.php'?'active':'' ?>" href="business_clearance.php"><i class="fa-solid fa-briefcase"></i> Business Clearance</a>
    <a class="nav-item <?php echo $current_page=='complaints.php'?'active':'' ?>" href="complaints.php"><i class="fa-solid fa-list-check"></i> Complaints</a>

    <div class="nav-group-label">Administration</div>
    <?php if($admin_type == 'Administrator'): ?>
    <a class="nav-item <?php echo $current_page=='admin.php'?'active':'' ?>" href="admin.php"><i class="fa-solid fa-users"></i> Users</a>
    <a class="nav-item <?php echo $current_page=='position.php'?'active':'' ?>" href="position.php"><i class="fa-solid fa-id-badge"></i> Positions</a>
    <a class="nav-item <?php echo $current_page=='purok.php'?'active':'' ?>" href="purok.php"><i class="fa-solid fa-map-location-dot"></i> Purok</a>
    <a class="nav-item <?php echo $current_page=='system_info.php'?'active':'' ?>" href="system_info.php"><i class="fa-solid fa-gear"></i> Settings</a>
    <?php endif; ?>
  </nav>
  <div class="sidebar-foot">
    <div class="admin-chip"><?php echo $admin_initial ?></div>
    <div>
      <div class="name"><?php echo htmlspecialchars($admin_name) ?></div>
      <div class="role"><?php echo htmlspecialchars($admin_type) ?></div>
    </div>
    <a href="javascript:void(0)" id="logout_btn" title="Logout"><i class="fa-solid fa-arrow-right-from-bracket logout"></i></a>
  </div>
</aside>

<div class="main">
  <div class="topbar">
    <button class="hamburger" id="hamburger"><i class="fa-solid fa-bars"></i></button>
    <div>
      <div class="topbar-title">Dashboard</div>
      <div class="topbar-date"><?php echo date('l, F j, Y') ?></div>
    </div>
    <div class="topbar-right">
      <a href="javascript:void(0)" id="my_account" class="topbar-icon" title="My Account"><i class="fa-regular fa-id-card"></i></a>
      <div class="admin-chip" style="width:34px;height:34px;font-size:13px;"><?php echo $admin_initial ?></div>
    </div>
  </div>

  <div class="content">
    <?php if(isset($_SESSION['flashdata'])): ?>
      <div class="alert alert-<?php echo ($_SESSION['flashdata']['type']=='success') ? 'success' : 'danger' ?> alert-dismissible fade show rounded-0" role="alert">
        <?php echo $_SESSION['flashdata']['msg']; unset($_SESSION['flashdata']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
