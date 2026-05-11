<?php
session_start();
require_once '../classes/Auth.php';
require_once '../classes/Donor.php';
Auth::requireAdmin();
$user   = Auth::currentUser();
$donor  = new Donor();
$msg    = ''; $msgType = '';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $action = $_POST['action'] ?? '';
    if ($action==='add')    { $r=$donor->create($_POST);             $msg=$r['message']; $msgType=$r['success']?'success':'error'; }
    elseif($action==='edit'){ $r=$donor->update((int)$_POST['id'],$_POST); $msg=$r['message']; $msgType=$r['success']?'success':'error'; }
    elseif($action==="link"){ $r=$donor->linkExistingUser((int)$_POST["user_id"],$_POST); $msg=$r["message"]; $msgType=$r["success"]?"success":"error"; }
    elseif($action==="delete"){ $r=$donor->delete((int)$_POST['id']); $msg=$r['message']; $msgType=$r['success']?'success':'error'; }
}

$search  = $_GET['search'] ?? '';
$bg      = $_GET['blood_group'] ?? '';
$donors  = $donor->getAll($search, $bg);
$stats   = $donor->getStats();
$unlinked = $donor->getUnlinkedDonors();
$groups  = ['A+','A-','B+','B-','AB+','AB-','O+','O-'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Donors — LifeFlow</title>
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
      <div><h1 class="page-title">Donors</h1><p class="page-sub">Manage blood donors</p></div>
    </div>
    <div class="topbar-right"><button class="btn-add" onclick="openModal('addModal')">+ Add Donor</button></div>
  </header>
  <div class="content">
    <?php if($msg): ?><div class="alert alert-<?= $msgType==='success'?'success':'error' ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

    <div class="stats-grid" style="grid-template-columns:repeat(3,minmax(0,1fr))">
      <div class="stat-card"><div class="stat-icon icon-red"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></div><div class="stat-body"><span class="stat-label">Total Donors</span><span class="stat-value"><?= $stats['total'] ?></span></div></div>
      <div class="stat-card"><div class="stat-icon icon-green"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div><div class="stat-body"><span class="stat-label">Available Now</span><span class="stat-value"><?= $stats['available'] ?></span></div></div>
      <div class="stat-card"><div class="stat-icon icon-amber"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div><div class="stat-body"><span class="stat-label">Unavailable</span><span class="stat-value"><?= $stats['total']-$stats['available'] ?></span></div></div>
    </div>

    <div class="card">
      <form method="GET" class="filter-row">
        <input type="text" name="search" placeholder="Search name, email, city..." value="<?= htmlspecialchars($search) ?>">
        <select name="blood_group">
          <option value="">All blood groups</option>
          <?php foreach($groups as $g): ?><option value="<?= $g ?>" <?= $bg===$g?'selected':'' ?>><?= $g ?></option><?php endforeach; ?>
        </select>
        <button type="submit" class="btn-filter">Search</button>
        <a href="donors.php" class="btn-clear">Clear</a>
      </form>
    </div>

    <div class="card">
      <div class="card-header"><h3>All Donors (<?= count($donors) ?>)</h3></div>
      <?php if(empty($donors)): ?>
      <div class="empty-state">No donors found. <button class="link-btn" onclick="openModal('addModal')">Add one →</button></div>
      <?php else: ?>
      <div class="table-wrap"><table class="data-table">
        <thead><tr><th>Donor</th><th>Blood Group</th><th>Phone</th><th>City</th><th>Last Donated</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach($donors as $d): ?>
        <tr>
          <td><div class="donor-cell"><div class="donor-avatar"><?= strtoupper(substr($d['name'],0,2)) ?></div><div><div><?= htmlspecialchars($d['name']) ?></div><div class="cell-sub"><?= htmlspecialchars($d['email']) ?></div></div></div></td>
          <td><span class="blood-badge"><?= $d['blood_group'] ?></span></td>
          <td><?= htmlspecialchars($d['phone']??'—') ?></td>
          <td><?= htmlspecialchars($d['city']??'—') ?></td>
          <td><?= $d['last_donated'] ? date('d M Y',strtotime($d['last_donated'])) : 'Never' ?></td>
          <td><span class="status-badge <?= $d['is_available']?'fulfilled':'rejected' ?>"><?= $d['is_available']?'Available':'Unavailable' ?></span></td>
          <td>
            <div class="action-btns">
              <button class="btn-icon btn-edit" onclick='openEdit(<?= htmlspecialchars(json_encode($d),ENT_QUOTES) ?>)' title="Edit"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
              <form method="POST" style="display:inline" onsubmit="return confirm('Delete this donor? This cannot be undone.')">
                <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $d['id'] ?>">
                <button type="submit" class="btn-icon btn-delete"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></button>
              </form>
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

<!-- Add Modal -->
<div class="modal-overlay" id="addModal">
  <div class="modal">
    <div class="modal-header"><h3>Add New Donor</h3><button class="modal-close" onclick="closeModal('addModal')">✕</button></div>
    <form method="POST" id="addForm" novalidate><input type="hidden" name="action" value="add">
      <div class="form-grid">
        <div class="field-group">
          <label>Full Name *</label>
          <input type="text" name="name" id="addName" placeholder="Enter full name">
          <span class="field-error" id="addNameErr">Name must be at least 2 characters.</span>
        </div>
        <div class="field-group">
          <label>Email *</label>
          <input type="email" name="email" id="addEmail" placeholder="email@example.com">
          <span class="field-error" id="addEmailErr">Please enter a valid email address.</span>
        </div>
        <div class="field-group">
          <label>Password (login)</label>
          <input type="password" name="password" placeholder="Default: donor123">
        </div>
        <div class="field-group">
          <label>Blood Group *</label>
          <select name="blood_group" required><?php foreach($groups as $g): ?><option value="<?= $g ?>"><?= $g ?></option><?php endforeach; ?></select>
        </div>
        <div class="field-group">
          <label>Phone</label>
          <input type="text" name="phone" id="addPhone" placeholder="10-digit number">
          <span class="field-error" id="addPhoneErr">Phone must be exactly 10 digits.</span>
        </div>
        <div class="field-group">
          <label>City</label>
          <input type="text" name="city" id="addCity" placeholder="City name">
          <span class="field-error" id="addCityErr">City name cannot contain numbers.</span>
        </div>
        <div class="field-group">
          <label>Date of Birth</label>
          <input type="date" name="dob" id="addDob">
          <span class="field-error" id="addDobErr">Donor must be at least 18 years old.</span>
        </div>
        <div class="field-group">
          <label>Gender</label>
          <select name="gender"><option value="">Select</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select>
        </div>
        <div class="field-group" style="grid-column:1/-1">
          <label>Address</label>
          <input type="text" name="address" placeholder="Full address">
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn-secondary" onclick="closeModal('addModal')">Cancel</button><button type="submit" class="btn-primary">Add Donor</button></div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal-overlay" id="editModal">
  <div class="modal">
    <div class="modal-header"><h3>Edit Donor</h3><button class="modal-close" onclick="closeModal('editModal')">✕</button></div>
    <form method="POST" id="editForm" novalidate><input type="hidden" name="action" value="edit"><input type="hidden" name="id" id="eid">
      <div class="form-grid">
        <div class="field-group">
          <label>Full Name *</label>
          <input type="text" name="name" id="ename">
          <span class="field-error" id="editNameErr">Name must be at least 2 characters.</span>
        </div>
        <div class="field-group">
          <label>Blood Group</label>
          <select name="blood_group" id="ebg"><?php foreach($groups as $g): ?><option value="<?= $g ?>"><?= $g ?></option><?php endforeach; ?></select>
        </div>
        <div class="field-group">
          <label>Phone</label>
          <input type="text" name="phone" id="ephone">
          <span class="field-error" id="editPhoneErr">Phone must be exactly 10 digits.</span>
        </div>
        <div class="field-group">
          <label>City</label>
          <input type="text" name="city" id="ecity">
          <span class="field-error" id="editCityErr">City name cannot contain numbers.</span>
        </div>
        <div class="field-group">
          <label>Date of Birth</label>
          <input type="date" name="dob" id="edob">
          <span class="field-error" id="editDobErr">Donor must be at least 18 years old.</span>
        </div>
        <div class="field-group">
          <label>Gender</label>
          <select name="gender" id="egender"><option value="">Select</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select>
        </div>
        <div class="field-group">
          <label>Available</label>
          <select name="is_available" id="eavail"><option value="1">Yes</option><option value="0">No</option></select>
        </div>
        <div class="field-group" style="grid-column:1/-1">
          <label>Address</label>
          <input type="text" name="address" id="eaddress">
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn-secondary" onclick="closeModal('editModal')">Cancel</button><button type="submit" class="btn-primary">Save Changes</button></div>
    </form>
  </div>
</div>

<!-- Link Modal -->
<div class="modal-overlay" id="linkModal">
  <div class="modal">
    <div class="modal-header"><h3>Setup Donor Profile</h3><button class="modal-close" onclick="closeModal('linkModal')">✕</button></div>
    <form method="POST" id="linkForm" novalidate><input type="hidden" name="action" value="link"><input type="hidden" name="user_id" id="luid">
      <p style="color:var(--text-secondary);font-size:14px;margin-bottom:20px">Setting up profile for: <strong id="lname_display" style="color:var(--text-primary)"></strong></p>
      <div class="form-grid">
        <div class="field-group">
          <label>Blood Group *</label>
          <select name="blood_group" required><?php foreach(["A+","A-","B+","B-","AB+","AB-","O+","O-"] as $g): ?><option value="<?= $g ?>"><?= $g ?></option><?php endforeach; ?></select>
        </div>
        <div class="field-group">
          <label>Phone</label>
          <input type="text" name="phone" id="lphone" placeholder="Phone number">
          <span class="field-error" id="linkPhoneErr">Phone must be exactly 10 digits.</span>
        </div>
        <div class="field-group">
          <label>City</label>
          <input type="text" name="city" placeholder="City">
        </div>
        <div class="field-group">
          <label>Date of Birth</label>
          <input type="date" name="dob" id="ldob">
          <span class="field-error" id="linkDobErr">Donor must be at least 18 years old.</span>
        </div>
        <div class="field-group">
          <label>Gender</label>
          <select name="gender"><option value="">Select</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select>
        </div>
        <div class="field-group">
          <label>Weight (kg)</label>
          <input type="number" name="weight_kg" id="lweight" placeholder="65" min="40" max="200">
          <span class="field-error" id="linkWeightErr">Weight must be between 40 and 200 kg.</span>
        </div>
        <div class="field-group" style="grid-column:1/-1">
          <label>Address</label>
          <input type="text" name="address" placeholder="Full address">
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn-secondary" onclick="closeModal('linkModal')">Cancel</button><button type="submit" class="btn-primary">Save Profile</button></div>
    </form>
  </div>
</div>

<script>
function openModal(id){document.getElementById(id).classList.add('open');}
function closeModal(id){document.getElementById(id).classList.remove('open');}
function openEdit(d){
  document.getElementById('eid').value=d.id;
  document.getElementById('ename').value=d.name||'';
  document.getElementById('ebg').value=d.blood_group||'';
  document.getElementById('ephone').value=d.phone||'';
  document.getElementById('ecity').value=d.city||'';
  document.getElementById('edob').value=d.dob||'';
  document.getElementById('egender').value=d.gender||'';
  document.getElementById('eavail').value=d.is_available;
  document.getElementById('eaddress').value=d.address||'';
  clearErrors('editForm');
  openModal('editModal');
}
function openLinkModal(userId, userName) {
  document.getElementById("luid").value = userId;
  document.getElementById("lname_display").textContent = userName;
  clearErrors('linkForm');
  openModal("linkModal");
}
document.querySelectorAll(".modal-overlay").forEach(o=>o.addEventListener('click',e=>{if(e.target===o)o.classList.remove('open');}));

// ── Validation helpers ──────────────────────────
function showErr(inputEl, errId, msg) {
  inputEl.classList.add('input-invalid');
  const e = document.getElementById(errId);
  if(e){ e.textContent = msg; e.style.display = 'block'; }
}
function clearErr(inputEl, errId) {
  inputEl.classList.remove('input-invalid');
  const e = document.getElementById(errId);
  if(e) e.style.display = 'none';
}
function clearErrors(formId) {
  document.querySelectorAll('#'+formId+' .input-invalid').forEach(el=>el.classList.remove('input-invalid'));
  document.querySelectorAll('#'+formId+' .field-error').forEach(el=>el.style.display='none');
}
function isAdult(dobVal) {
  if(!dobVal) return true; // dob is optional
  const dob = new Date(dobVal);
  const today = new Date();
  const age = today.getFullYear() - dob.getFullYear() - (today < new Date(today.getFullYear(), dob.getMonth(), dob.getDate()) ? 1 : 0);
  return age >= 18;
}
function validatePhone(val) {
  if(!val) return true; // phone is optional
  return /^\d{10}$/.test(val.trim());
}
function validateCity(val) {
  if(!val) return true; // city is optional
  return !/\d/.test(val.trim());
}

// ── Add Form Validation ──────────────────────────
document.getElementById('addForm').addEventListener('submit', function(e) {
  clearErrors('addForm');
  let ok = true;
  const name  = document.getElementById('addName');
  const email = document.getElementById('addEmail');
  const phone = document.getElementById('addPhone');
  const city  = document.getElementById('addCity');
  const dob   = document.getElementById('addDob');

  if(!name.value.trim() || name.value.trim().length < 2) {
    showErr(name, 'addNameErr', 'Name must be at least 2 characters.'); ok=false;
  }
  if(!email.value.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
    showErr(email, 'addEmailErr', 'Please enter a valid email address.'); ok=false;
  }
  if(!validatePhone(phone.value)) {
    showErr(phone, 'addPhoneErr', 'Phone must be exactly 10 digits.'); ok=false;
  }
  if(!validateCity(city.value)) {
    showErr(city, 'addCityErr', 'City name cannot contain numbers.'); ok=false;
  }
  if(dob.value && !isAdult(dob.value)) {
    showErr(dob, 'addDobErr', 'Donor must be at least 18 years old.'); ok=false;
  }
  if(!ok) e.preventDefault();
});

// ── Edit Form Validation ──────────────────────────
document.getElementById('editForm').addEventListener('submit', function(e) {
  clearErrors('editForm');
  let ok = true;
  const name  = document.getElementById('ename');
  const phone = document.getElementById('ephone');
  const city  = document.getElementById('ecity');
  const dob   = document.getElementById('edob');

  if(!name.value.trim() || name.value.trim().length < 2) {
    showErr(name, 'editNameErr', 'Name must be at least 2 characters.'); ok=false;
  }
  if(!validatePhone(phone.value)) {
    showErr(phone, 'editPhoneErr', 'Phone must be exactly 10 digits.'); ok=false;
  }
  if(!validateCity(city.value)) {
    showErr(city, 'editCityErr', 'City name cannot contain numbers.'); ok=false;
  }
  if(dob.value && !isAdult(dob.value)) {
    showErr(dob, 'editDobErr', 'Donor must be at least 18 years old.'); ok=false;
  }
  if(!ok) e.preventDefault();
});

// ── Link Form Validation ──────────────────────────
document.getElementById('linkForm').addEventListener('submit', function(e) {
  clearErrors('linkForm');
  let ok = true;
  const phone  = document.getElementById('lphone');
  const dob    = document.getElementById('ldob');
  const weight = document.getElementById('lweight');

  if(!validatePhone(phone.value)) {
    showErr(phone, 'linkPhoneErr', 'Phone must be exactly 10 digits.'); ok=false;
  }
  if(dob.value && !isAdult(dob.value)) {
    showErr(dob, 'linkDobErr', 'Donor must be at least 18 years old.'); ok=false;
  }
  if(weight.value && (weight.value < 40 || weight.value > 200)) {
    showErr(weight, 'linkWeightErr', 'Weight must be between 40 and 200 kg.'); ok=false;
  }
  if(!ok) e.preventDefault();
});

// ── Live feedback on phone fields ──────────────────
['addPhone','ephone','lphone'].forEach(function(id) {
  const el = document.getElementById(id); if(!el) return;
  el.addEventListener('input', function() {
    if(this.value && !/^\d{0,10}$/.test(this.value.trim())) {
      this.value = this.value.replace(/\D/g,'').slice(0,10);
    }
  });
});
</script>
</body></html>
<!-- This file already has the add/edit/delete functionality -->
<!-- The fix for linking existing users is handled via the email check in Donor::create() -->