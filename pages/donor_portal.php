<?php
session_start();
require_once '../classes/Auth.php';
require_once '../config/db.php';
Auth::requireDonor();
$user = Auth::currentUser();
$db   = Database::getInstance();

$dstmt = $db->prepare("SELECT d.*, u.name, u.email FROM donors d JOIN users u ON d.user_id=u.id WHERE d.user_id=?");
$dstmt->execute([$user['id']]);
$donor = $dstmt->fetch();

$history = [];
$certs   = [];
if ($donor) {
    $h = $db->prepare("SELECT * FROM donations WHERE donor_id=? ORDER BY donated_at DESC");
    $h->execute([$donor['id']]); $history = $h->fetchAll();
    $c = $db->prepare("SELECT c.*, do.donated_at FROM certificates c LEFT JOIN donations do ON c.donation_id=do.id WHERE c.donor_id=? ORDER BY c.issued_at DESC");
    $c->execute([$donor['id']]); $certs = $c->fetchAll();
}

$inventory = $db->query("SELECT blood_group, units, critical_level FROM blood_inventory ORDER BY FIELD(blood_group,'O+','O-','A+','A-','B+','B-','AB+','AB-')")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Donor Portal — LifeFlow</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
<aside class="sidebar" id="sidebar">
  <a href="/bloodbank/public/home.php" class="sidebar-logo" style="text-decoration:none"><span class="logo-drop"></span><span class="logo-text">LifeFlow</span></a>
  <nav class="sidebar-nav">
    <span class="nav-section">Donor</span>
    <a href="donor_portal.php" class="nav-item active">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
      <span>My Profile</span>
    </a>
  </nav>
  <div class="sidebar-footer">
    <div class="user-pill">
      <div class="user-avatar"><?= strtoupper(substr($user['name'],0,2)) ?></div>
      <div class="user-info"><span class="user-name"><?= htmlspecialchars($user['name']) ?></span><span class="user-role">Donor</span></div>
    </div>
    <a href="/bloodbank/logout.php" class="logout-btn" title="Logout"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></a>
  </div>
</aside>
<div class="main-wrap">
  <header class="topbar">
    <div class="topbar-left">
      <button class="menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
      <div><h1 class="page-title">Donor Portal</h1><p class="page-sub">Welcome, <?= htmlspecialchars($user['name']) ?></p></div>
    </div>
  </header>
  <div class="content">
    <?php if(!$donor): ?>
    <div class="card" style="text-align:center;padding:60px 40px">
      <div style="font-size:56px;margin-bottom:20px">🩸</div>
      <h2 style="font-family:var(--font-display);font-size:22px;margin-bottom:12px">Profile not set up yet</h2>
      <p style="color:var(--text-secondary);margin-bottom:8px;font-size:15px">Your account is registered but your donor profile hasn't been created by the admin yet.</p>
      <p style="color:var(--text-muted);font-size:13px">Please contact the admin to add your donor details and blood group.</p>
    </div>
    <?php else: ?>

    <div class="stats-grid" style="grid-template-columns:repeat(3,minmax(0,1fr))">
      <div class="stat-card"><div class="stat-icon icon-red"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></div><div class="stat-body"><span class="stat-label">Blood Group</span><span class="stat-value"><?= htmlspecialchars($donor['blood_group']) ?></span></div></div>
      <div class="stat-card"><div class="stat-icon icon-blue"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2L8 7H4l2 13h12L20 7h-4L12 2z"/></svg></div><div class="stat-body"><span class="stat-label">Total Donations</span><span class="stat-value"><?= (int)$donor['total_donations'] ?></span></div></div>
      <div class="stat-card"><div class="stat-icon icon-green"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg></div><div class="stat-body"><span class="stat-label">Certificates</span><span class="stat-value"><?= count($certs) ?></span></div></div>
    </div>

    <div class="grid-2">
      <div class="card">
        <div class="card-header"><h3>My Profile</h3></div>
        <table class="data-table">
          <tbody>
            <tr><td style="color:var(--text-muted);width:130px">Name</td><td><?= htmlspecialchars($donor['name']) ?></td></tr>
            <tr><td style="color:var(--text-muted)">Email</td><td><?= htmlspecialchars($donor['email']) ?></td></tr>
            <tr><td style="color:var(--text-muted)">Blood Group</td><td><span class="blood-badge"><?= $donor['blood_group'] ?></span></td></tr>
            <tr><td style="color:var(--text-muted)">Phone</td><td><?= htmlspecialchars($donor['phone']??'—') ?></td></tr>
            <tr><td style="color:var(--text-muted)">City</td><td><?= htmlspecialchars($donor['city']??'—') ?></td></tr>
            <tr><td style="color:var(--text-muted)">Gender</td><td><?= $donor['gender'] ? ucfirst($donor['gender']) : '—' ?></td></tr>
            <tr><td style="color:var(--text-muted)">Last Donated</td><td><?= $donor['last_donated'] ? date('d M Y',strtotime($donor['last_donated'])) : 'Never' ?></td></tr>
            <tr><td style="color:var(--text-muted)">Status</td><td><span class="status-badge <?= $donor['is_available']?'fulfilled':'rejected' ?>"><?= $donor['is_available']?'Available to donate':'Not available' ?></span></td></tr>
          </tbody>
        </table>
      </div>

      <div class="card">
        <div class="card-header"><h3>Blood Bank Availability</h3></div>
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
    </div>

    <div class="card">
      <div class="card-header"><h3>Donation History</h3></div>
      <?php if(empty($history)): ?>
      <div class="empty-state">No donations recorded yet.</div>
      <?php else: ?>
      <div class="table-wrap"><table class="data-table">
        <thead><tr><th>Date</th><th>Blood Group</th><th>Units</th><th>Certificate</th></tr></thead>
        <tbody>
        <?php foreach($history as $h):
          $matchCert = null;
          foreach($certs as $c) { if($c['donation_id']==$h['id']) { $matchCert=$c; break; } }
        ?>
        <tr>
          <td><?= date('d M Y',strtotime($h['donated_at'])) ?></td>
          <td><span class="blood-badge"><?= $h['blood_group'] ?></span></td>
          <td><?= number_format((float)$h['units'],1) ?> unit(s)</td>
          <td><?php if($matchCert): ?><a href="certificate_print.php?id=<?= $matchCert['id'] ?>" target="_blank" class="btn-sm btn-blue">View Certificate</a><?php else: ?>—<?php endif; ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table></div>
      <?php endif; ?>
    </div>

    <?php endif; ?>
  </div>
</div>
</body></html>
