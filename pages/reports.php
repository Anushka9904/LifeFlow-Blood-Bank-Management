<?php
session_start();
require_once '../classes/Auth.php';
require_once '../config/db.php';
Auth::requireAdmin();
$user = Auth::currentUser();
$db   = Database::getInstance();

// All queries safe for strict GROUP BY mode
$inventory   = $db->query("SELECT blood_group, units FROM blood_inventory ORDER BY FIELD(blood_group,'O+','O-','A+','A-','B+','B-','AB+','AB-')")->fetchAll();
$byGroup     = $db->query("SELECT blood_group, COUNT(*) as cnt FROM donors GROUP BY blood_group ORDER BY cnt DESC")->fetchAll();
$reqStats    = $db->query("SELECT status, COUNT(*) as cnt FROM blood_requests GROUP BY status")->fetchAll();

// Monthly donations — safe GROUP BY with alias
$monthly = $db->query("
    SELECT
        DATE_FORMAT(donated_at,'%b %Y') as month_label,
        DATE_FORMAT(donated_at,'%Y-%m') as month_key,
        COUNT(*) as cnt
    FROM donations
    GROUP BY DATE_FORMAT(donated_at,'%Y-%m'), DATE_FORMAT(donated_at,'%b %Y')
    ORDER BY month_key DESC
    LIMIT 6
")->fetchAll();
$monthly = array_reverse($monthly);

$totalDonors    = (int)$db->query("SELECT COUNT(*) FROM donors")->fetchColumn();
$totalDonations = (int)$db->query("SELECT COUNT(*) FROM donations")->fetchColumn();
$totalCerts     = (int)$db->query("SELECT COUNT(*) FROM certificates")->fetchColumn();
$totalCamps     = (int)$db->query("SELECT COUNT(*) FROM donation_camps")->fetchColumn();
$totalUnits     = (int)$db->query("SELECT COALESCE(SUM(units),0) FROM blood_inventory")->fetchColumn();
$pendingReqs    = (int)$db->query("SELECT COUNT(*) FROM blood_requests WHERE status='pending'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Reports — LifeFlow</title>
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
      <div><h1 class="page-title">Reports & Analytics</h1><p class="page-sub">System-wide statistics and insights</p></div>
    </div>
  </header>
  <div class="content">

    <div class="stats-grid">
      <div class="stat-card"><div class="stat-icon icon-red"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></div><div class="stat-body"><span class="stat-label">Total Donors</span><span class="stat-value"><?= $totalDonors ?></span></div></div>
      <div class="stat-card"><div class="stat-icon icon-blue"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2L8 7H4l2 13h12L20 7h-4L12 2z"/></svg></div><div class="stat-body"><span class="stat-label">Total Donations</span><span class="stat-value"><?= $totalDonations ?></span></div></div>
      <div class="stat-card"><div class="stat-icon icon-green"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg></div><div class="stat-body"><span class="stat-label">Certificates</span><span class="stat-value"><?= $totalCerts ?></span></div></div>
      <div class="stat-card"><div class="stat-icon icon-amber"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div><div class="stat-body"><span class="stat-label">Camps Held</span><span class="stat-value"><?= $totalCamps ?></span></div></div>
    </div>

    <div class="grid-2">
      <div class="card">
        <div class="card-header"><h3>Blood inventory levels</h3></div>
        <div class="chart-wrap"><canvas id="invChart"></canvas></div>
      </div>
      <div class="card">
        <div class="card-header"><h3>Donors by blood group</h3></div>
        <div class="chart-wrap"><canvas id="donorChart"></canvas></div>
      </div>
    </div>

    <div class="grid-2">
      <div class="card">
        <div class="card-header"><h3>Monthly donations</h3></div>
        <div class="chart-wrap">
          <?php if(empty($monthly)): ?>
          <div class="empty-state" style="padding:60px 0">No donation records yet.</div>
          <?php else: ?>
          <canvas id="monthlyChart"></canvas>
          <?php endif; ?>
        </div>
      </div>
      <div class="card">
        <div class="card-header"><h3>Request status breakdown</h3></div>
        <div class="chart-wrap">
          <?php if(empty($reqStats)): ?>
          <div class="empty-state" style="padding:60px 0">No requests yet.</div>
          <?php else: ?>
          <canvas id="reqChart"></canvas>
          <?php endif; ?>
        </div>
      </div>
    </div>

  </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
const palette = ['#e74c3c','#3498db','#00d4aa','#e67e22','#9b59b6','#1abc9c','#f39c12','#c0392b'];
const gridColor = 'rgba(255,255,255,0.04)';
const tickColor = '#8a9bb5';
const baseOpts = {responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{grid:{color:gridColor},ticks:{color:tickColor}},y:{grid:{color:gridColor},ticks:{color:tickColor},beginAtZero:true}}};

const inv = <?= json_encode($inventory) ?>;
new Chart(document.getElementById('invChart'),{type:'bar',data:{labels:inv.map(d=>d.blood_group),datasets:[{data:inv.map(d=>+d.units),backgroundColor:palette,borderRadius:6,borderSkipped:false}]},options:baseOpts});

const bg = <?= json_encode($byGroup) ?>;
if(bg.length){
  new Chart(document.getElementById('donorChart'),{type:'doughnut',data:{labels:bg.map(d=>d.blood_group),datasets:[{data:bg.map(d=>+d.cnt),backgroundColor:palette,borderWidth:0,hoverOffset:6}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'right',labels:{color:tickColor,boxWidth:12,padding:14}}}}});
}

const mo = <?= json_encode($monthly) ?>;
if(mo.length && document.getElementById('monthlyChart')){
  new Chart(document.getElementById('monthlyChart'),{type:'line',data:{labels:mo.map(d=>d.month_label),datasets:[{data:mo.map(d=>+d.cnt),borderColor:'#00d4aa',backgroundColor:'rgba(0,212,170,0.08)',fill:true,tension:0.4,pointBackgroundColor:'#00d4aa',pointRadius:4}]},options:baseOpts});
}

const rs = <?= json_encode($reqStats) ?>;
const rsColors = {pending:'#e67e22',approved:'#3498db',fulfilled:'#00d4aa',rejected:'#e74c3c'};
if(rs.length && document.getElementById('reqChart')){
  new Chart(document.getElementById('reqChart'),{type:'doughnut',data:{labels:rs.map(d=>d.status),datasets:[{data:rs.map(d=>+d.cnt),backgroundColor:rs.map(d=>rsColors[d.status]||'#888'),borderWidth:0,hoverOffset:6}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'right',labels:{color:tickColor,boxWidth:12,padding:14}}}}});
}
</script>
</body></html>
