<?php
session_start();
require_once '../classes/Auth.php';
require_once '../config/db.php';
Auth::requireAdmin();
$user = Auth::currentUser();
$db   = Database::getInstance();

$totalDonors    = (int)$db->query("SELECT COUNT(*) FROM donors")->fetchColumn();
$availDonors    = (int)$db->query("SELECT COUNT(*) FROM donors WHERE is_available=1")->fetchColumn();
$totalUnits     = (int)$db->query("SELECT COALESCE(SUM(units),0) FROM blood_inventory")->fetchColumn();
$pendingReqs    = (int)$db->query("SELECT COUNT(*) FROM blood_requests WHERE status='pending'")->fetchColumn();
$upcomingCamps  = (int)$db->query("SELECT COUNT(*) FROM donation_camps WHERE status='upcoming' AND camp_date>=CURDATE()")->fetchColumn();
$criticalGroups = (int)$db->query("SELECT COUNT(*) FROM blood_inventory WHERE units<=critical_level")->fetchColumn();

$inventory = $db->query("SELECT blood_group, units, critical_level FROM blood_inventory ORDER BY FIELD(blood_group,'O+','O-','A+','A-','B+','B-','AB+','AB-')")->fetchAll();

$recentReqs = $db->query("SELECT br.id, br.blood_group, br.units_needed, br.urgency, br.status, br.requested_at, u.name as hospital_name FROM blood_requests br JOIN users u ON br.hospital_id=u.id ORDER BY br.requested_at DESC LIMIT 6")->fetchAll();

$recentDonors = $db->query("SELECT d.id, d.blood_group, d.city, d.last_donated, d.is_available, d.created_at, u.name, u.email FROM donors d JOIN users u ON d.user_id=u.id ORDER BY d.created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Dashboard — LifeFlow</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
<?php require_once '../includes/sidebar.php'; ?>
<div class="main-wrap">
  <header class="topbar">
    <div class="topbar-left">
      <button class="menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
      <div><h1 class="page-title">Dashboard</h1><p class="page-sub">Welcome back, <?= htmlspecialchars($user['name']) ?></p></div>
    </div>
    <div class="topbar-right">
      <?php if($criticalGroups>0): ?>
      <div class="alert-badge"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg><?= $criticalGroups ?> critical</div>
      <?php endif; ?>
      <div class="topbar-date"><?= date('D, d M Y') ?></div>
    </div>
  </header>
  <div class="content">

    <div class="stats-grid">
      <div class="stat-card"><div class="stat-icon icon-red"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></div><div class="stat-body"><span class="stat-label">Total Donors</span><span class="stat-value"><?= $totalDonors ?></span><span class="stat-sub"><?= $availDonors ?> available</span></div></div>
      <div class="stat-card"><div class="stat-icon icon-blue"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2L8 7H4l2 13h12L20 7h-4L12 2z"/></svg></div><div class="stat-body"><span class="stat-label">Blood Units</span><span class="stat-value"><?= $totalUnits ?></span><span class="stat-sub <?= $criticalGroups>0?'text-danger':'' ?>"><?= $criticalGroups ?> groups critical</span></div></div>
      <div class="stat-card"><div class="stat-icon icon-amber"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div><div class="stat-body"><span class="stat-label">Pending Requests</span><span class="stat-value"><?= $pendingReqs ?></span><span class="stat-sub">Awaiting approval</span></div></div>
      <div class="stat-card"><div class="stat-icon icon-green"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div><div class="stat-body"><span class="stat-label">Upcoming Camps</span><span class="stat-value"><?= $upcomingCamps ?></span><span class="stat-sub">Scheduled ahead</span></div></div>
    </div>

    <div class="grid-2">
      <div class="card">
        <div class="card-header"><h3>Blood Inventory</h3><a href="inventory.php" class="card-link">View all →</a></div>
        <div class="chart-wrap"><canvas id="invChart"></canvas></div>
        <div class="inventory-legend">
          <?php foreach($inventory as $inv):
            $pct = min(100, ($inv['units'] / max(1, $inv['critical_level']*3)) * 100);
            $cls = $inv['units'] <= $inv['critical_level'] ? 'crit' : ($inv['units'] <= $inv['critical_level']*1.5 ? 'warn' : 'ok');
          ?>
          <div class="inv-item">
            <span class="blood-badge <?= $cls ?>"><?= $inv['blood_group'] ?></span>
            <div class="inv-bar-wrap"><div class="inv-bar <?= $cls ?>" style="width:<?= round($pct) ?>%"></div></div>
            <span class="inv-units"><?= $inv['units'] ?>u</span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="card">
        <div class="card-header"><h3>Recent Requests</h3><a href="requests.php" class="card-link">View all →</a></div>
        <?php if(empty($recentReqs)): ?>
        <div class="empty-state">No requests yet.</div>
        <?php else: ?>
        <div class="table-wrap"><table class="data-table">
          <thead><tr><th>Hospital</th><th>Group</th><th>Units</th><th>Urgency</th><th>Status</th></tr></thead>
          <tbody>
          <?php foreach($recentReqs as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['hospital_name']) ?></td>
            <td><span class="blood-badge"><?= $r['blood_group'] ?></span></td>
            <td><?= $r['units_needed'] ?></td>
            <td><span class="urgency-badge <?= $r['urgency'] ?>"><?= ucfirst($r['urgency']) ?></span></td>
            <td><span class="status-badge <?= $r['status'] ?>"><?= ucfirst($r['status']) ?></span></td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table></div>
        <?php endif; ?>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3>Recent Donors</h3><a href="donors.php" class="card-link">View all →</a></div>
      <?php if(empty($recentDonors)): ?>
      <div class="empty-state">No donors yet. <a href="donors.php">Add one →</a></div>
      <?php else: ?>
      <div class="table-wrap"><table class="data-table">
        <thead><tr><th>Name</th><th>Blood Group</th><th>City</th><th>Last Donated</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach($recentDonors as $d): ?>
        <tr>
          <td><div class="donor-cell"><div class="donor-avatar"><?= strtoupper(substr($d['name'],0,2)) ?></div><div><div><?= htmlspecialchars($d['name']) ?></div><div class="cell-sub"><?= htmlspecialchars($d['email']) ?></div></div></div></td>
          <td><span class="blood-badge"><?= $d['blood_group'] ?></span></td>
          <td><?= htmlspecialchars($d['city']??'—') ?></td>
          <td><?= $d['last_donated'] ? date('d M Y', strtotime($d['last_donated'])) : '—' ?></td>
          <td><span class="status-badge <?= $d['is_available']?'fulfilled':'rejected' ?>"><?= $d['is_available']?'Available':'Unavailable' ?></span></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table></div>
      <?php endif; ?>
    </div>

  </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
const inv = <?= json_encode(array_map(fn($i)=>['g'=>$i['blood_group'],'u'=>(int)$i['units'],'c'=>(int)$i['critical_level']], $inventory)) ?>;
new Chart(document.getElementById('invChart'), {
  type:'bar',
  data:{labels:inv.map(d=>d.g),datasets:[{data:inv.map(d=>d.u),backgroundColor:inv.map(d=>d.u<=d.c?'#e74c3c':d.u<=d.c*1.5?'#e67e22':'#00d4aa'),borderRadius:6,borderSkipped:false}]},
  options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{grid:{color:'rgba(255,255,255,0.04)'},ticks:{color:'#8a9bb5'}},y:{grid:{color:'rgba(255,255,255,0.04)'},ticks:{color:'#8a9bb5'},beginAtZero:true}}}
});
</script>
</body></html>
