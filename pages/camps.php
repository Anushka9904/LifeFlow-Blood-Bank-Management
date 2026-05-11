<?php
session_start();
require_once '../classes/Auth.php';
require_once '../classes/Camp.php';
Auth::requireAdmin();
$user = Auth::currentUser();
$camp = new Camp();
$msg  = ''; $msgType = '';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $action = $_POST['action'] ?? '';
    if ($action==='add')    { $r=$camp->create($_POST);              $msg=$r['message']; $msgType=$r['success']?'success':'error'; }
    elseif($action==='edit'){ $r=$camp->update((int)$_POST['id'],$_POST); $msg=$r['message']; $msgType=$r['success']?'success':'error'; }
    elseif($action==='delete'){ $r=$camp->delete((int)$_POST['id']); $msg=$r['message']; $msgType=$r['success']?'success':'error'; }
}

$filter = $_GET['status'] ?? '';
$camps  = $camp->getAll($filter);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Donation Camps — LifeFlow</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/dashboard.css">
<style>
.field-error{font-size:11px;color:#c53030;margin-top:4px;display:none}
.input-invalid{border-color:#e74c3c!important;box-shadow:0 0 0 3px rgba(231,76,60,.15)!important}
</style>
</head>
<body>
<?php require_once '../includes/sidebar.php'; ?>
<div class="main-wrap">
  <header class="topbar">
    <div class="topbar-left">
      <button class="menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
      <div><h1 class="page-title">Donation Camps</h1><p class="page-sub">Schedule and manage blood donation drives</p></div>
    </div>
    <div class="topbar-right"><button class="btn-add" onclick="openModal('addModal')">+ Schedule Camp</button></div>
  </header>
  <div class="content">
    <?php if($msg): ?><div class="alert alert-<?= $msgType==='success'?'success':'error' ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <div class="card">
      <div class="filter-row" style="flex-wrap:wrap;gap:8px">
        <?php foreach([''=>'All','upcoming'=>'Upcoming','active'=>'Active','completed'=>'Completed','cancelled'=>'Cancelled'] as $k=>$v): ?>
        <a href="?status=<?= $k ?>" class="filter-tab <?= $filter===$k?'active':'' ?>"><?= $v ?></a>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="camps-grid">
      <?php if(empty($camps)): ?>
      <div class="empty-state" style="grid-column:1/-1">No camps found.</div>
      <?php else: ?>
      <?php foreach($camps as $c): ?>
      <div class="camp-card">
        <div class="camp-card-top">
          <span class="status-badge <?= $c['status'] ?>"><?= ucfirst($c['status']) ?></span>
          <div class="action-btns">
            <button class="btn-icon btn-edit" onclick='openEdit(<?= htmlspecialchars(json_encode($c),ENT_QUOTES) ?>)'><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
            <form method="POST" style="display:inline" onsubmit="return confirm('Delete camp?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $c['id'] ?>"><button type="submit" class="btn-icon btn-delete"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></button></form>
          </div>
        </div>
        <h3 class="camp-name"><?= htmlspecialchars($c['name']) ?></h3>
        <div class="camp-detail"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg><?= htmlspecialchars($c['location']?:($c['city']?:'—')) ?></div>
        <div class="camp-detail"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg><?= date('d M Y',strtotime($c['camp_date'])) ?><?= $c['start_time'] ? ' · '.date('h:i A',strtotime($c['start_time'])) : '' ?></div>
        <?php if($c['organizer']): ?><div class="camp-detail"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg><?= htmlspecialchars($c['organizer']) ?></div><?php endif; ?>
        <div class="camp-capacity">
          <span><?= $c['registered'] ?>/<?= $c['max_capacity'] ?> registered</span>
          <div class="inv-bar-wrap"><div class="inv-bar ok" style="width:<?= min(100,round(($c['registered']/max(1,$c['max_capacity']))*100)) ?>%"></div></div>
        </div>
      </div>
      <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Add Modal -->
<div class="modal-overlay" id="addModal">
  <div class="modal">
    <div class="modal-header"><h3>Schedule New Camp</h3><button class="modal-close" onclick="closeModal('addModal')">✕</button></div>
    <form method="POST" id="addCampForm" novalidate><input type="hidden" name="action" value="add">
      <div class="form-grid">
        <div class="field-group" style="grid-column:1/-1">
          <label>Camp Name *</label>
          <input type="text" name="name" id="addCampName" placeholder="e.g. Annual Blood Drive 2025">
          <span class="field-error" id="addCampNameErr">Camp name is required and must be at least 3 characters.</span>
        </div>
        <div class="field-group">
          <label>Location / Venue</label>
          <input type="text" name="location" placeholder="Venue or building name">
        </div>
        <div class="field-group">
          <label>City</label>
          <input type="text" name="city" id="addCampCity" placeholder="City name">
          <span class="field-error" id="addCampCityErr">City name cannot contain numbers.</span>
        </div>
        <div class="field-group">
          <label>Camp Date *</label>
          <input type="date" name="camp_date" id="addCampDate">
          <span class="field-error" id="addCampDateErr">Camp date must be today or a future date.</span>
        </div>
        <div class="field-group">
          <label>Max Capacity</label>
          <input type="number" name="max_capacity" id="addCampCap" value="100" min="1" max="10000">
          <span class="field-error" id="addCampCapErr">Capacity must be between 1 and 10000.</span>
        </div>
        <div class="field-group">
          <label>Start Time</label>
          <input type="time" name="start_time" id="addStartTime">
        </div>
        <div class="field-group">
          <label>End Time</label>
          <input type="time" name="end_time" id="addEndTime">
          <span class="field-error" id="addEndTimeErr">End time must be after start time.</span>
        </div>
        <div class="field-group">
          <label>Organizer</label>
          <input type="text" name="organizer" placeholder="Organizer name">
        </div>
        <div class="field-group">
          <label>Contact</label>
          <input type="text" name="contact" id="addContact" placeholder="10-digit phone">
          <span class="field-error" id="addContactErr">Contact must be exactly 10 digits.</span>
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn-secondary" onclick="closeModal('addModal')">Cancel</button><button type="submit" class="btn-primary">Schedule Camp</button></div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal-overlay" id="editModal">
  <div class="modal">
    <div class="modal-header"><h3>Edit Camp</h3><button class="modal-close" onclick="closeModal('editModal')">✕</button></div>
    <form method="POST" id="editCampForm" novalidate><input type="hidden" name="action" value="edit"><input type="hidden" name="id" id="eid">
      <div class="form-grid">
        <div class="field-group" style="grid-column:1/-1">
          <label>Camp Name *</label>
          <input type="text" name="name" id="ename">
          <span class="field-error" id="editCampNameErr">Camp name is required and must be at least 3 characters.</span>
        </div>
        <div class="field-group">
          <label>Location</label>
          <input type="text" name="location" id="eloc">
        </div>
        <div class="field-group">
          <label>City</label>
          <input type="text" name="city" id="ecity">
          <span class="field-error" id="editCampCityErr">City name cannot contain numbers.</span>
        </div>
        <div class="field-group">
          <label>Camp Date</label>
          <input type="date" name="camp_date" id="edate">
        </div>
        <div class="field-group">
          <label>Max Capacity</label>
          <input type="number" name="max_capacity" id="ecap" min="1" max="10000">
          <span class="field-error" id="editCampCapErr">Capacity must be between 1 and 10000.</span>
        </div>
        <div class="field-group">
          <label>Start Time</label>
          <input type="time" name="start_time" id="etime">
        </div>
        <div class="field-group">
          <label>Organizer</label>
          <input type="text" name="organizer" id="eorg">
        </div>
        <div class="field-group">
          <label>Contact</label>
          <input type="text" name="contact" id="econtact">
          <span class="field-error" id="editContactErr">Contact must be exactly 10 digits.</span>
        </div>
        <div class="field-group">
          <label>Status</label>
          <select name="status" id="estatus"><option value="upcoming">Upcoming</option><option value="active">Active</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option></select>
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn-secondary" onclick="closeModal('editModal')">Cancel</button><button type="submit" class="btn-primary">Save Changes</button></div>
    </form>
  </div>
</div>

<script>
function openModal(id){document.getElementById(id).classList.add('open');}
function closeModal(id){document.getElementById(id).classList.remove('open');}
function openEdit(c){
  document.getElementById('eid').value=c.id; document.getElementById('ename').value=c.name||'';
  document.getElementById('eloc').value=c.location||''; document.getElementById('ecity').value=c.city||'';
  document.getElementById('edate').value=c.camp_date||''; document.getElementById('ecap').value=c.max_capacity||100;
  document.getElementById('etime').value=c.start_time||''; document.getElementById('eorg').value=c.organizer||'';
  document.getElementById('econtact').value=c.contact||''; document.getElementById('estatus').value=c.status||'upcoming';
  clearErrors('editCampForm');
  openModal('editModal');
}
document.querySelectorAll('.modal-overlay').forEach(o=>o.addEventListener('click',e=>{if(e.target===o)o.classList.remove('open');}));

// ── Helpers ──────────────────────────────────────
function showErr(el, errId, msg) {
  el.classList.add('input-invalid');
  const e = document.getElementById(errId);
  if(e){ e.textContent=msg; e.style.display='block'; }
}
function clearErrors(formId) {
  document.querySelectorAll('#'+formId+' .input-invalid').forEach(el=>el.classList.remove('input-invalid'));
  document.querySelectorAll('#'+formId+' .field-error').forEach(el=>el.style.display='none');
}
function isToday(dateVal) {
  const d = new Date(dateVal);
  const t = new Date(); t.setHours(0,0,0,0);
  return d >= t;
}
function validateContact(val) {
  if(!val || !val.trim()) return true; // optional
  return /^\d{10}$/.test(val.trim());
}
function validateCity(val) {
  if(!val || !val.trim()) return true; // optional
  return !/\d/.test(val.trim());
}

// ── Add Camp Validation ──────────────────────────
document.getElementById('addCampForm').addEventListener('submit', function(e) {
  clearErrors('addCampForm');
  let ok = true;
  const name      = document.getElementById('addCampName');
  const city      = document.getElementById('addCampCity');
  const date      = document.getElementById('addCampDate');
  const cap       = document.getElementById('addCampCap');
  const startTime = document.getElementById('addStartTime');
  const endTime   = document.getElementById('addEndTime');
  const contact   = document.getElementById('addContact');

  if(!name.value.trim() || name.value.trim().length < 3) {
    showErr(name, 'addCampNameErr', 'Camp name is required and must be at least 3 characters.'); ok=false;
  }
  if(!validateCity(city.value)) {
    showErr(city, 'addCampCityErr', 'City name cannot contain numbers.'); ok=false;
  }
  if(!date.value) {
    showErr(date, 'addCampDateErr', 'Camp date is required.'); ok=false;
  } else if(!isToday(date.value)) {
    showErr(date, 'addCampDateErr', 'Camp date must be today or a future date.'); ok=false;
  }
  if(cap.value && (parseInt(cap.value) < 1 || parseInt(cap.value) > 10000)) {
    showErr(cap, 'addCampCapErr', 'Capacity must be between 1 and 10000.'); ok=false;
  }
  if(startTime.value && endTime.value && endTime.value <= startTime.value) {
    showErr(endTime, 'addEndTimeErr', 'End time must be after start time.'); ok=false;
  }
  if(!validateContact(contact.value)) {
    showErr(contact, 'addContactErr', 'Contact must be exactly 10 digits.'); ok=false;
  }
  if(!ok) e.preventDefault();
});

// ── Edit Camp Validation ──────────────────────────
document.getElementById('editCampForm').addEventListener('submit', function(e) {
  clearErrors('editCampForm');
  let ok = true;
  const name    = document.getElementById('ename');
  const city    = document.getElementById('ecity');
  const cap     = document.getElementById('ecap');
  const contact = document.getElementById('econtact');

  if(!name.value.trim() || name.value.trim().length < 3) {
    showErr(name, 'editCampNameErr', 'Camp name is required and must be at least 3 characters.'); ok=false;
  }
  if(!validateCity(city.value)) {
    showErr(city, 'editCampCityErr', 'City name cannot contain numbers.'); ok=false;
  }
  if(cap.value && (parseInt(cap.value) < 1 || parseInt(cap.value) > 10000)) {
    showErr(cap, 'editCampCapErr', 'Capacity must be between 1 and 10000.'); ok=false;
  }
  if(!validateContact(contact.value)) {
    showErr(contact, 'editContactErr', 'Contact must be exactly 10 digits.'); ok=false;
  }
  if(!ok) e.preventDefault();
});

// ── Live: only allow digits in contact fields ─────
['addContact','econtact'].forEach(function(id) {
  const el = document.getElementById(id); if(!el) return;
  el.addEventListener('input', function() {
    this.value = this.value.replace(/\D/g,'').slice(0,10);
  });
});
</script>
</body></html>