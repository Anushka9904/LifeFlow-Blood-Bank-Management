<?php
session_start();
require_once '../classes/Auth.php';
require_once '../classes/BloodRequest.php';
Auth::requireAdmin();
$user = Auth::currentUser();
$req  = new BloodRequest();
$msg  = ''; $msgType = '';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $action = $_POST['action'] ?? '';
    if ($action==='add')    { $r=$req->create($_POST,(int)$user['id']); $msg=$r['message']; $msgType=$r['success']?'success':'error'; }
    elseif($action==='status'){ $r=$req->updateStatus((int)$_POST['id'],$_POST['status']); $msg=$r['message']; $msgType=$r['success']?'success':'error'; }
    elseif($action==='delete'){ $r=$req->delete((int)$_POST['id']); $msg=$r['message']; $msgType=$r['success']?'success':'error'; }
}

$filter   = $_GET['status'] ?? '';
$requests = $req->getAll($filter);
$groups   = ['A+','A-','B+','B-','AB+','AB-','O+','O-'];
$counts   = [
    'all'       => count($req->getAll()),
    'pending'   => count($req->getAll('pending')),
    'approved'  => count($req->getAll('approved')),
    'fulfilled' => count($req->getAll('fulfilled')),
    'rejected'  => count($req->getAll('rejected')),
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Blood Requests — LifeFlow</title>
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
      <div><h1 class="page-title">Blood Requests</h1><p class="page-sub">Manage hospital blood requests</p></div>
    </div>
    <div class="topbar-right"><button class="btn-add" onclick="openModal('addModal')">+ New Request</button></div>
  </header>
  <div class="content">
    <?php if($msg): ?><div class="alert alert-<?= $msgType==='success'?'success':'error' ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <div class="card">
      <div class="filter-row" style="flex-wrap:wrap;gap:8px">
        <?php foreach([''=>'All ('.$counts['all'].')','pending'=>'Pending ('.$counts['pending'].')','approved'=>'Approved ('.$counts['approved'].')','fulfilled'=>'Fulfilled ('.$counts['fulfilled'].')','rejected'=>'Rejected ('.$counts['rejected'].')'] as $k=>$v): ?>
        <a href="?status=<?= $k ?>" class="filter-tab <?= $filter===$k?'active':'' ?>"><?= $v ?></a>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="card">
      <div class="card-header"><h3>Requests (<?= count($requests) ?>)</h3></div>
      <?php if(empty($requests)): ?><div class="empty-state">No requests found.</div>
      <?php else: ?>
      <div class="table-wrap"><table class="data-table">
        <thead><tr><th>Hospital</th><th>Patient</th><th>Blood Group</th><th>Units</th><th>Urgency</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach($requests as $r): ?>
        <tr>
          <td><?= htmlspecialchars($r['hospital_name']) ?></td>
          <td><?= htmlspecialchars($r['patient_name']??'—') ?></td>
          <td><span class="blood-badge"><?= $r['blood_group'] ?></span></td>
          <td><?= $r['units_needed'] ?></td>
          <td><span class="urgency-badge <?= $r['urgency'] ?>"><?= ucfirst($r['urgency']) ?></span></td>
          <td><span class="status-badge <?= $r['status'] ?>"><?= ucfirst($r['status']) ?></span></td>
          <td><?= date('d M Y',strtotime($r['requested_at'])) ?></td>
          <td>
            <div class="action-btns">
              <?php if($r['status']==='pending'): ?>
              <form method="POST" style="display:inline"><input type="hidden" name="action" value="status"><input type="hidden" name="id" value="<?= $r['id'] ?>"><input type="hidden" name="status" value="approved"><button type="submit" class="btn-sm btn-green">Approve</button></form>
              <form method="POST" style="display:inline"><input type="hidden" name="action" value="status"><input type="hidden" name="id" value="<?= $r['id'] ?>"><input type="hidden" name="status" value="rejected"><button type="submit" class="btn-sm btn-red">Reject</button></form>
              <?php elseif($r['status']==='approved'): ?>
              <form method="POST" style="display:inline"><input type="hidden" name="action" value="status"><input type="hidden" name="id" value="<?= $r['id'] ?>"><input type="hidden" name="status" value="fulfilled"><button type="submit" class="btn-sm btn-blue">Fulfill</button></form>
              <?php endif; ?>
              <form method="POST" style="display:inline" onsubmit="return confirm('Delete this request?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button type="submit" class="btn-icon btn-delete"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></button></form>
            </div>
          </td>
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
