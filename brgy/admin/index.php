<?php require_once(__DIR__.'/header.php'); ?>

<style>
  .page-head{ margin-bottom:24px; position:relative; }
  .page-head h1{ font-size:30px; font-weight:700; margin:0 0 6px 0; color:var(--primary-dark); }
  .page-head p{ margin:0; color:var(--ink-soft); font-size:14.5px; }

  .stats-grid{ display:grid; grid-template-columns:repeat(6,1fr); gap:18px; margin-bottom:30px; }
  @media (max-width: 1400px){ .stats-grid{ grid-template-columns:repeat(3,1fr); } }
  @media (max-width: 640px){ .stats-grid{ grid-template-columns:repeat(2,1fr); } }
  .stat-card{ background:var(--card); border:1px solid var(--line); border-radius:16px; padding:20px; position:relative; overflow:hidden; }
  .stat-card::after{ content:attr(data-icon); font-family:"Font Awesome 6 Free"; font-weight:900; position:absolute; right:-6px; bottom:-14px; font-size:76px; opacity:.06; }
  .stat-card .icon-badge{ width:40px; height:40px; border-radius:11px; display:flex; align-items:center; justify-content:center; font-size:16px; margin-bottom:14px; }
  .stat-card .value{ font-family:'Fraunces',serif; font-size:30px; font-weight:700; line-height:1; margin-bottom:4px; }
  .stat-card .label{ font-size:12px; color:var(--ink-soft); text-transform:uppercase; letter-spacing:.5px; font-weight:600; }
  .stat-card .bar{ height:3px; border-radius:3px; margin-top:14px; }

  .c-population .icon-badge{ background:var(--primary-light); color:var(--primary); }
  .c-population .bar{ background:var(--primary); }
  .c-households .icon-badge{ background:#eaf1ff; color:#2f6fed; }
  .c-households .bar{ background:#2f6fed; }
  .c-officials .icon-badge{ background:var(--gold-light); color:var(--gold); }
  .c-officials .bar{ background:var(--gold); }
  .c-complaints .icon-badge{ background:var(--coral-light); color:var(--coral); }
  .c-complaints .bar{ background:var(--coral); }
  .c-clearances .icon-badge{ background:#f0edf9; color:#6f5fa3; }
  .c-clearances .bar{ background:#6f5fa3; }
  .c-users .icon-badge{ background:#eaedfb; color:#4457c9; }
  .c-users .bar{ background:#4457c9; }

  .panel{ background:var(--card); border:1px solid var(--line); border-radius:16px; overflow:hidden; }
  .panel-head{ padding:20px 24px; border-bottom:1px solid var(--line); display:flex; flex-wrap:wrap; align-items:center; gap:14px; }
  .panel-head h2{ font-size:19px; font-weight:700; margin:0; display:flex; align-items:center; gap:10px; }
  .panel-head h2 i{ color:var(--primary); font-size:16px; }
  .count-badge{ background:var(--primary-light); color:var(--primary-dark); font-weight:700; padding:4px 12px; border-radius:20px; font-size:12.5px; }
  .search-box{ margin-left:auto; position:relative; width:230px; }
  .search-box i{ position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--ink-soft); font-size:13px; }
  .search-box input{ width:100%; padding:9px 12px 9px 32px; border-radius:9px; border:1px solid var(--line); background:var(--paper); font-size:13.5px; font-family:'Inter',sans-serif; color:var(--ink); }
  .search-box input:focus{ outline:2px solid var(--primary); outline-offset:1px; }
  .chip-row{ display:flex; gap:8px; padding:14px 24px 0 24px; flex-wrap:wrap; }
  .chip{ padding:6px 13px; border-radius:20px; font-size:12.5px; font-weight:600; border:1px solid var(--line); color:var(--ink-soft); cursor:pointer; background:var(--card); }
  .chip.active{ background:var(--primary); color:#fff; border-color:var(--primary); }
  table.pop-table{ width:100%; border-collapse:collapse; margin-top:8px; }
  table.pop-table thead th{ text-align:left; padding:12px 24px; font-size:11px; text-transform:uppercase; letter-spacing:.5px; color:var(--ink-soft); font-weight:700; border-bottom:1px solid var(--line); }
  table.pop-table td{ padding:12px 24px; border-bottom:1px solid var(--line); font-size:13.8px; }
  table.pop-table tbody tr:hover{ background:var(--paper); }
  table.pop-table tbody tr:last-child td{ border-bottom:none; }
  .name-cell{ display:flex; align-items:center; gap:11px; }
  .avatar{ width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12.5px; font-weight:700; color:#fff; flex-shrink:0; }
  .purok-tag{ font-size:12.5px; color:var(--ink-soft); }
  .status-pill{ padding:3px 11px; border-radius:20px; font-size:11.5px; font-weight:700; }
  .status-Single{ background:#eaf1ff; color:#2f6fed; }
  .status-Married{ background:var(--primary-light); color:var(--primary); }
  .status-Widowed{ background:#eee; color:var(--ink-soft); }
  .no-results{ padding:40px; text-align:center; color:var(--ink-soft); display:none; }
</style>

<?php
// ── Live counters, pulled through the shared $conn from header.php ──
function safe_count($conn, $sql) {
    $res = @$conn->query($sql);
    if ($res && $row = $res->fetchArray()) {
        return (int) $row['count'];
    }
    return 0;
}

$total_households  = safe_count($conn, "SELECT COUNT(household_id) as `count` FROM `household_list`");
$total_officials    = safe_count($conn, "SELECT COUNT(official_id) as `count` FROM `official_list`");
$total_complaints   = safe_count($conn, "SELECT COUNT(complaint_id) as `count` FROM `complaint_list`");
$total_clearances   = safe_count($conn, "SELECT COUNT(clearance_id) as `count` FROM `clearance_list`")
                     + safe_count($conn, "SELECT COUNT(business_clearance_id) as `count` FROM `business_clearance_list`");
$total_users        = safe_count($conn, "SELECT COUNT(admin_id) as `count` FROM `admin_list`");

// ── Household residents, for the browsable panel below ──
$residents = [];
$res = $conn->query("SELECT CONCAT(lastname, ', ', firstname, ', ', middlename) as fullname, house_no, contact, p.purok
                      FROM household_list h
                      INNER JOIN `purok_list` p ON h.purok_id = p.purok_id
                      ORDER BY fullname ASC");
if ($res) {
    while ($row = $res->fetchArray()) {
        $residents[] = $row;
    }
}
$puroks = array_values(array_unique(array_column($residents, 'purok')));
sort($puroks);
?>

<div class="page-head">
  <h1>Welcome back, <?php echo htmlspecialchars($admin_name) ?></h1>
  <p>Here's what's happening in the barangay today, <?php echo date('F j, Y') ?>.</p>
</div>

<div class="stats-grid">
  <div class="stat-card c-households" data-icon="&#xf03a;">
    <div class="icon-badge"><i class="fa-solid fa-table-list"></i></div>
    <div class="value"><?php echo number_format($total_households) ?></div>
    <div class="label">Households</div>
    <div class="bar"></div>
  </div>
  <div class="stat-card c-officials" data-icon="&#xf508;">
    <div class="icon-badge"><i class="fa-solid fa-user-tie"></i></div>
    <div class="value"><?php echo number_format($total_officials) ?></div>
    <div class="label">Officials</div>
    <div class="bar"></div>
  </div>
  <div class="stat-card c-complaints" data-icon="&#xf071;">
    <div class="icon-badge"><i class="fa-solid fa-triangle-exclamation"></i></div>
    <div class="value"><?php echo number_format($total_complaints) ?></div>
    <div class="label">Complaints</div>
    <div class="bar"></div>
  </div>
  <div class="stat-card c-clearances" data-icon="&#xf15c;">
    <div class="icon-badge"><i class="fa-solid fa-file-lines"></i></div>
    <div class="value"><?php echo number_format($total_clearances) ?></div>
    <div class="label">Clearances Issued</div>
    <div class="bar"></div>
  </div>
  <div class="stat-card c-users" data-icon="&#xf007;">
    <div class="icon-badge"><i class="fa-solid fa-user-check"></i></div>
    <div class="value"><?php echo number_format($total_users) ?></div>
    <div class="label">Active Users</div>
    <div class="bar"></div>
  </div>
</div>

<div class="panel">
  <div class="panel-head">
    <h2><i class="fa-solid fa-people-group"></i> Household Residents</h2>
    <span class="count-badge" id="countBadge"><?php echo count($residents) ?> residents</span>
    <div class="search-box">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="text" id="searchInput" placeholder="Search by name...">
    </div>
  </div>
  <div class="chip-row" id="chipRow">
    <span class="chip active" data-purok="all">All Puroks</span>
    <?php foreach ($puroks as $pk): ?>
      <span class="chip" data-purok="<?php echo htmlspecialchars($pk) ?>"><?php echo htmlspecialchars($pk) ?></span>
    <?php endforeach; ?>
  </div>
  <div class="table-responsive" style="overflow-x:auto;">
    <table class="pop-table" id="popTable">
      <thead>
        <tr>
          <th>#</th>
          <th>Full Name</th>
          <th>Household #</th>
          <th>Contact</th>
          <th>Purok</th>
        </tr>
      </thead>
      <tbody>
        <?php $avatarColors = ['#1f6f54','#2f6fed','#c9973b','#c73e51','#6f5fa3'];
        foreach ($residents as $i => $p):
          $initials = strtoupper(substr($p['fullname'],0,1));
          $avColor = $avatarColors[$i % 5];
        ?>
        <tr data-purok="<?php echo htmlspecialchars($p['purok']) ?>" data-name="<?php echo htmlspecialchars(strtolower($p['fullname'])) ?>">
          <td><?php echo $i + 1 ?></td>
          <td>
            <div class="name-cell">
              <div class="avatar" style="background:<?php echo $avColor ?>;"><?php echo $initials ?></div>
              <?php echo htmlspecialchars($p['fullname']) ?>
            </div>
          </td>
          <td><?php echo htmlspecialchars($p['house_no']) ?></td>
          <td><?php echo htmlspecialchars($p['contact']) ?></td>
          <td><span class="purok-tag"><?php echo htmlspecialchars($p['purok']) ?></span></td>
        </tr>
        <?php endforeach; ?>
        <?php if (count($residents) === 0): ?>
        <tr><td colspan="5" class="text-center text-muted py-4">No households recorded yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div class="no-results" id="noResults">No residents match your search.</div>
  </div>
</div>

<script>
  const chips = document.querySelectorAll('.chip');
  const searchInput = document.getElementById('searchInput');
  const rows = document.querySelectorAll('#popTable tbody tr[data-name]');
  const noResults = document.getElementById('noResults');
  const countBadge = document.getElementById('countBadge');
  let activePurok = 'all';

  function applyFilters(){
    const term = searchInput.value.trim().toLowerCase();
    let visible = 0;
    rows.forEach(row => {
      const matchesPurok = activePurok === 'all' || row.dataset.purok === activePurok;
      const matchesSearch = row.dataset.name.includes(term);
      const show = matchesPurok && matchesSearch;
      row.style.display = show ? '' : 'none';
      if (show) visible++;
    });
    noResults.style.display = visible === 0 ? 'block' : 'none';
    countBadge.textContent = visible + ' resident' + (visible === 1 ? '' : 's');
  }

  chips.forEach(chip => {
    chip.addEventListener('click', () => {
      chips.forEach(c => c.classList.remove('active'));
      chip.classList.add('active');
      activePurok = chip.dataset.purok;
      applyFilters();
    });
  });
  if(searchInput) searchInput.addEventListener('input', applyFilters);
</script>

<?php require_once(__DIR__.'/footer.php'); ?>
