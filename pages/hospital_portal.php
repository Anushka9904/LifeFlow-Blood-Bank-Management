<?php
session_start();
require_once '../classes/Auth.php';
require_once '../config/db.php';
Auth::requireHospital();
$user = Auth::currentUser();
$db   = Database::getInstance();
$msg  = ''; $msgType = '';

if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='add') {
    $stmt = $db->prepare("INSERT INTO blood_requests (hospital_id,patient_name,blood_group,units_needed,urgency,notes) VALUES (?,?,?,?,?,?)");
    $stmt->execute([
        $user['id'],
        trim($_POST['patient_name'] ?? ''),
        $_POST['blood_group'],
        (int)$_POST['units_needed'],
        $_POST['urgency'] ?? 'normal',
        trim($_POST['notes'] ?? '')
    ]);
    $msg = 'Blood request submitted successfully.'; $msgType = 'success';
}

$myReqs = $db->prepare("SELECT * FROM blood_requests WHERE hospital_id=? ORDER BY requested_at DESC");
$myReqs->execute([$user['id']]);
$requests = $myReqs->fetchAll();

$pending   = array_filter($requests, fn($r) => $r['status']==='pending');
$fulfilled = array_filter($requests, fn($r) => $r['status']==='fulfilled');

$inventory = $db->query("SELECT blood_group, units, critical_level FROM blood_inventory ORDER BY FIELD(blood_group,'O+','O-','A+','A-','B+','B-','AB+','AB-')")->fetchAll();
$groups    = ['A+','A-','B+','B-','AB+','AB-','O+','O-'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Hospital Portal — LifeFlow</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
<aside class="sidebar" id="sidebar">
  <a href="/bloodbank/public/home.php" class="sidebar-logo" style="text-decoration:none"><span class="logo-drop"></span><span class="logo-text">LifeFlow</span></a>
  <nav class="sidebar-nav">
    <span class="nav-section">Hospital</span>
    <a href="hospital_portal.php" class="nav-item active">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
      <span>My Requests</span>
    </a>
  </nav>
  <div class="sidebar-footer">
    <div class="user-pill">
      <div class="user-avatar" style="background:var(--blue-soft);color:var(--blue)"><?= strtoupper(substr($user['name'],0,2)) ?></div>
      <div class="user-info"><span class="user-name"><?= htmlspecialchars($user['name']) ?></span><span class="user-role">Hospital</span></div>
    </div>
    <a href="/bloodbank/logout.php" class="logout-btn" title="Logout"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></a>
  </div>
</aside>
<div class="main-wrap">
  <header class="topbar">
    <div class="topbar-left">
      <button class="menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
      <div><h1 class="page-title">Hospital Portal</h1><p class="page-sub">Welcome, <?= htmlspecialchars($user['name']) ?></p></div>
    </div>
    <div class="topbar-right"><button class="btn-add" onclick="openModal('addModal')">+ New Request</button></div>
  </header>
  <div class="content">
    <?php if($msg): ?><div class="alert alert-<?= $msgType==='success'?'success':'error' ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

    <div class="stats-grid" style="grid-template-columns:repeat(3,minmax(0,1fr))">
      <div class="stat-card"><div class="stat-icon icon-amber"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div><div class="stat-body"><span class="stat-label">Pending</span><span class="stat-value"><?= count($pending) ?></span><span class="stat-sub">Awaiting approval</span></div></div>
      <div class="stat-card"><div class="stat-icon icon-green"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div><div class="stat-body"><span class="stat-label">Fulfilled</span><span class="stat-value"><?= count($fulfilled) ?></span><span class="stat-sub">Completed</span></div></div>
      <div class="stat-card"><div class="stat-icon icon-blue"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2L8 7H4l2 13h12L20 7h-4L12 2z"/></svg></div><div class="stat-body"><span class="stat-label">Total Requests</span><span class="stat-value"><?= count($requests) ?></span><span class="stat-sub">All time</span></div></div>
    </div>

    <div class="card">
      <div class="card-header"><h3>Current Blood Availability</h3></div>
      <div class="inventory-legend">
        <?php foreach($inventory as $inv):
          $pct = min(100, ($inv['units'] / max(1, $inv['critical_level']*3)) * 100);
          $cls = $inv['units'] <= $inv['critical_level'] ? 'crit' : ($inv['units'] <= $inv['critical_level']*1.5 ? 'warn' : 'ok');
        ?>
        <div class="inv-item">
          <span class="blood-badge <?= $cls ?>"><?= $inv['blood_group'] ?></span>
          <div class="inv-bar-wrap"><div class="inv-bar <?= $cls ?>" style="width:<?= round($pct) ?>%"></div></div>
          <span class="inv-units"><?= $inv['units'] ?> units</span>
          <span class="status-badge <?= $cls==='ok'?'fulfilled':($cls==='warn'?'pending':'rejected') ?>"><?= $cls==='ok'?'Available':($cls==='warn'?'Low':'Critical') ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3>My Blood Requests</h3></div>
      <?php if(empty($requests)): ?>
      <div class="empty-state">No requests yet. <button class="link-btn" onclick="openModal('addModal')">Submit one →</button></div>
      <?php else: ?>
      <div class="table-wrap"><table class="data-table">
        <thead><tr><th>Patient</th><th>Blood Group</th><th>Units</th><th>Urgency</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
        <?php foreach($requests as $r): ?>
        <tr>
          <td><?= htmlspecialchars($r['patient_name']??'—') ?></td>
          <td><span class="blood-badge"><?= $r['blood_group'] ?></span></td>
          <td><?= $r['units_needed'] ?></td>
          <td><span class="urgency-badge <?= $r['urgency'] ?>"><?= ucfirst($r['urgency']) ?></span></td>
          <td><span class="status-badge <?= $r['status'] ?>"><?= ucfirst($r['status']) ?></span></td>
          <td><?= date('d M Y',strtotime($r['requested_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table></div>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="modal-overlay" id="addModal">
  <div class="modal">
    <div class="modal-header"><h3>New Blood Request</h3><button class="modal-close" onclick="closeModal('addModal')">✕</button></div>
    <form method="POST"><input type="hidden" name="action" value="add">
      <div class="form-grid">
        <div class="field-group"><label>Patient Name</label><input type="text" name="patient_name" placeholder="Patient name"></div>
        <div class="field-group"><label>Blood Group *</label><select name="blood_group" required><?php foreach($groups as $g): ?><option value="<?= $g ?>"><?= $g ?></option><?php endforeach; ?></select></div>
        <div class="field-group"><label>Units Needed *</label><input type="number" name="units_needed" min="1" value="1" required></div>
        <div class="field-group"><label>Urgency</label><select name="urgency"><option value="normal">Normal</option><option value="urgent">Urgent</option><option value="critical">Critical</option></select></div>
        <div class="field-group" style="grid-column:1/-1"><label>Notes</label><textarea name="notes" rows="3" placeholder="Additional notes..."></textarea></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn-secondary" onclick="closeModal('addModal')">Cancel</button><button type="submit" class="btn-primary">Submit Request</button></div>
    </form>
  </div>
</div>
<script>
function openModal(id){document.getElementById(id).classList.add('open');}
function closeModal(id){document.getElementById(id).classList.remove('open');}
document.querySelectorAll('.modal-overlay').forEach(o=>o.addEventListener('click',e=>{if(e.target===o)o.classList.remove('open');}));
</script>
</body></html>
