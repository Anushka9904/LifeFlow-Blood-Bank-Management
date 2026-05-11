<?php
session_start();
require_once __DIR__.'/classes/Auth.php';
if (Auth::isLoggedIn()) { header('Location: /bloodbank/index.php'); exit; }
$error = ''; $success = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $auth   = new Auth();
    $result = $auth->register($_POST);
    if ($result['success']) $success = $result['message'];
    else $error = $result['message'];
}
$selectedRole = $_POST['role'] ?? 'donor';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>LifeFlow — Register</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
<div class="auth-layout">
  <div class="auth-brand">
    <div class="brand-content">
      <div class="brand-logo"><span class="logo-drop"></span><span class="logo-text">LifeFlow</span></div>
      <h1 class="brand-headline">Join the<br>movement.</h1>
      <p class="brand-sub">Register as a donor, hospital, or administrator and be part of saving lives.</p>
      <div class="role-cards">
        <div class="role-card <?= $selectedRole==='donor'?'active':'' ?>" data-role="donor">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
          <span>Donor</span>
        </div>
        <div class="role-card <?= $selectedRole==='hospital'?'active':'' ?>" data-role="hospital">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M12 8v8M8 12h8"/></svg>
          <span>Hospital</span>
        </div>
        <div class="role-card <?= $selectedRole==='admin'?'active':'' ?>" data-role="admin">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
          <span>Admin</span>
        </div>
      </div>
    </div>
    <div class="drop-grid"><?php for($i=0;$i<20;$i++) echo '<span class="drop-dot"></span>'; ?></div>
  </div>

  <div class="auth-form-panel">
    <div class="form-card">
      <div class="form-header"><h2>Create account</h2><p>Fill in your details to get started</p></div>
      <?php if($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
      <?php if($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?> <a href="login.php">Sign in →</a></div><?php endif; ?>

      <form method="POST" class="auth-form" novalidate id="regForm">

        <!-- ROLE — visible radio buttons, no JS dependency -->
        <div class="field-group">
          <label>Register as</label>
          <div class="role-radio-group">
            <label class="role-radio <?= $selectedRole==='donor'?'selected':'' ?>">
              <input type="radio" name="role" value="donor" <?= $selectedRole==='donor'?'checked':'' ?>>
              <span>Blood Donor</span>
            </label>
            <label class="role-radio <?= $selectedRole==='hospital'?'selected':'' ?>">
              <input type="radio" name="role" value="hospital" <?= $selectedRole==='hospital'?'checked':'' ?>>
              <span>Hospital</span>
            </label>
            <label class="role-radio <?= $selectedRole==='admin'?'selected':'' ?>">
              <input type="radio" name="role" value="admin" <?= $selectedRole==='admin'?'checked':'' ?>>
              <span>Admin</span>
            </label>
          </div>
        </div>

        <div class="field-group">
          <label>Full name</label>
          <input type="text" name="name" placeholder="John Doe" value="<?= htmlspecialchars($_POST['name']??'') ?>" required>
        </div>
        <div class="field-group">
          <label>Email address</label>
          <input type="email" name="email" placeholder="you@example.com" value="<?= htmlspecialchars($_POST['email']??'') ?>" required>
        </div>
        <div class="field-row">
          <div class="field-group">
            <label>Password</label>
            <div class="input-eye-wrap">
              <input type="password" id="pwd" name="password" placeholder="Min 6 chars" required>
              <button type="button" class="eye-btn" onclick="togglePwd('pwd')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
          </div>
          <div class="field-group">
            <label>Confirm</label>
            <div class="input-eye-wrap">
              <input type="password" id="cpwd" name="confirm" placeholder="Repeat" required>
              <button type="button" class="eye-btn" onclick="togglePwd('cpwd')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
          </div>
        </div>
        <div class="strength-wrap"><div class="strength-bar" id="sbar"></div><span class="strength-label" id="slabel"></span></div>

        <button type="submit" class="btn-primary">
          Create account
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
      </form>
      <p class="switch-link">Already have an account? <a href="login.php">Sign in</a></p>
    </div>
  </div>
</div>
<style>
.role-radio-group{display:flex;gap:10px;flex-wrap:wrap}
.role-radio{display:flex;align-items:center;gap:8px;padding:10px 16px;background:var(--bg-input);border:1px solid var(--border);border-radius:var(--radius);cursor:pointer;font-size:14px;color:var(--text-secondary);transition:var(--transition);flex:1;min-width:100px}
.role-radio input[type=radio]{accent-color:var(--red);width:16px;height:16px;cursor:pointer;flex-shrink:0}
.role-radio:hover{border-color:var(--border-hover);color:var(--text-primary)}
.role-radio:has(input:checked){border-color:var(--red);color:var(--red);background:var(--red-soft)}
.role-radio span{font-weight:500}
</style>
<script>
function togglePwd(id){const i=document.getElementById(id);i.type=i.type==='text'?'password':'text';}
document.getElementById('pwd').addEventListener('input',function(){
  const v=this.value,c=['','#e74c3c','#e67e22','#f1c40f','#2ecc71','#00d4aa'],l=['','Weak','Fair','Good','Strong','Very strong'];
  let s=0;if(v.length>=6)s++;if(v.length>=10)s++;if(/[A-Z]/.test(v))s++;if(/[0-9]/.test(v))s++;if(/[^A-Za-z0-9]/.test(v))s++;
  document.getElementById('sbar').style.cssText='width:'+(s*20)+'%;background:'+(c[s]||'transparent');
  document.getElementById('slabel').textContent=l[s]||'';document.getElementById('slabel').style.color=c[s]||'';
});
document.getElementById('regForm').addEventListener('submit',function(e){
  if(document.getElementById('pwd').value!==document.getElementById('cpwd').value){
    e.preventDefault();alert('Passwords do not match.');
  }
});
// Sync role cards on left with radio buttons
document.querySelectorAll('.role-radio input').forEach(radio=>{
  radio.addEventListener('change',function(){
    document.querySelectorAll('.role-card').forEach(c=>c.classList.remove('active'));
    const card=document.querySelector('.role-card[data-role="'+this.value+'"]');
    if(card) card.classList.add('active');
  });
});
document.querySelectorAll('.role-card').forEach(card=>{
  card.addEventListener('click',function(){
    const role=this.dataset.role;
    const radio=document.querySelector('input[name="role"][value="'+role+'"]');
    if(radio){radio.checked=true;radio.dispatchEvent(new Event('change'));}
    document.querySelectorAll('.role-card').forEach(c=>c.classList.remove('active'));
    this.classList.add('active');
  });
});
</script>
</body>
</html>
