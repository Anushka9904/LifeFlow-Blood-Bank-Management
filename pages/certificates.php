<?php
session_start();
require_once '../classes/Auth.php';
require_once '../config/db.php';
Auth::requireAdmin();
$user = Auth::currentUser();
$db   = Database::getInstance();
$msg  = ''; $msgType = '';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $donor_id      = (int)$_POST['donor_id'];
    $donation_date = $_POST['donation_date'];
    $blood_group   = $_POST['blood_group'];
    $units         = (float)($_POST['units'] ?? 1);
    $cert_no       = 'LF-'.date('Y').'-'.strtoupper(substr(md5(uniqid()),0,6));

    try {
        $db->prepare("INSERT INTO donations (donor_id,blood_group,units,donated_at) VALUES (?,?,?,?)")->execute([$donor_id,$blood_group,$units,$donation_date]);
        $don_id = (int)$db->lastInsertId();
        $db->prepare("UPDATE blood_inventory SET units=units+? WHERE blood_group=?")->execute([$units,$blood_group]);
        $db->prepare("UPDATE donors SET last_donated=?,total_donations=total_donations+1 WHERE id=?")->execute([$donation_date,$donor_id]);
        $db->prepare("INSERT INTO certificates (donor_id,donation_id,certificate_no) VALUES (?,?,?)")->execute([$donor_id,$don_id,$cert_no]);
        $msg = "Certificate $cert_no issued successfully!"; $msgType = 'success';
    } catch (Exception $e) {
        $msg = 'Error: '.$e->getMessage(); $msgType = 'error';
    }
}

$donors = $db->query("SELECT d.id, d.blood_group, u.name, u.email FROM donors d JOIN users u ON d.user_id=u.id ORDER BY u.name")->fetchAll();
$certs  = $db->query("SELECT c.id, c.certificate_no, c.issued_at, d.blood_group, u.name as donor_name FROM certificates c JOIN donors d ON c.donor_id=d.id JOIN users u ON d.user_id=u.id ORDER BY c.issued_at DESC LIMIT 30")->fetchAll();
$groups = ['A+','A-','B+','B-','AB+','AB-','O+','O-'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Certificates — LifeFlow</title>
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
      <div><h1 class="page-title">Certificates</h1><p class="page-sub">Issue donation certificates to donors</p></div>
    </div>
    <div class="topbar-right"><button class="btn-add" onclick="openModal('addModal')">+ Issue Certificate</button></div>
  </header>
  <div class="content">
    <?php if($msg): ?><div class="alert alert-<?= $msgType==='success'?'success':'error' ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <div class="card">
      <div class="card-header"><h3>Issued Certificates (<?= count($certs) ?>)</h3></div>
      <?php if(empty($certs)): ?>
      <div class="empty-state">No certificates issued yet. <button class="link-btn" onclick="openModal('addModal')">Issue one →</button></div>
      <?php else: ?>
      <div class="table-wrap"><table class="data-table">
        <thead><tr><th>Certificate No.</th><th>Donor</th><th>Blood Group</th><th>Issued Date</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach($certs as $c): ?>
        <tr>
          <td><code style="color:var(--teal);font-size:13px;font-family:monospace"><?= htmlspecialchars($c['certificate_no']) ?></code></td>
          <td><?= htmlspecialchars($c['donor_name']) ?></td>
          <td><span class="blood-badge"><?= $c['blood_group'] ?></span></td>
          <td><?= date('d M Y',strtotime($c['issued_at'])) ?></td>
          <td><a href="certificate_print.php?id=<?= $c['id'] ?>" target="_blank" class="btn-sm btn-blue">Print / PDF</a></td>
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
    <div class="modal-header"><h3>Issue Donation Certificate</h3><button class="modal-close" onclick="closeModal('addModal')">✕</button></div>
    <form method="POST">
      <div class="form-grid">
        <div class="field-group" style="grid-column:1/-1">
          <label>Select Donor *</label>
          <select name="donor_id" id="donorSelect" required onchange="setBloodGroup(this)">
            <option value="">Choose a donor...</option>
            <?php foreach($donors as $d): ?>
            <option value="<?= $d['id'] ?>" data-bg="<?= $d['blood_group'] ?>"><?= htmlspecialchars($d['name']) ?> (<?= $d['blood_group'] ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field-group">
          <label>Blood Group *</label>
          <select name="blood_group" id="bgSelect" required>
            <?php foreach($groups as $g): ?><option value="<?= $g ?>"><?= $g ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="field-group"><label>Units Donated</label><input type="number" name="units" value="1" min="0.5" step="0.5"></div>
        <div class="field-group" style="grid-column:1/-1"><label>Donation Date *</label><input type="date" name="donation_date" value="<?= date('Y-m-d') ?>" required></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn-secondary" onclick="closeModal('addModal')">Cancel</button><button type="submit" class="btn-primary">Issue Certificate</button></div>
    </form>
  </div>
</div>
<script>
function openModal(id){document.getElementById(id).classList.add('open');}
function closeModal(id){document.getElementById(id).classList.remove('open');}
function setBloodGroup(sel){const opt=sel.options[sel.selectedIndex];if(opt.dataset.bg)document.getElementById('bgSelect').value=opt.dataset.bg;}
document.querySelectorAll('.modal-overlay').forEach(o=>o.addEventListener('click',e=>{if(e.target===o)o.classList.remove('open');}));
</script>
</body></html>
