<?php
session_start();
require_once __DIR__.'/classes/Auth.php';
if (Auth::isLoggedIn()) { header('Location: /bloodbank/index.php'); exit; }

// Store error in session so we can redirect after POST
$error = '';
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $auth   = new Auth();
    $result = $auth->login($_POST['email']??'', $_POST['password']??'');
    if ($result['success']) {
        // Success — redirect to portal
        header('Location: '.$result['redirect']); exit;
    } else {
        // Fail — store error in session, redirect back to GET login page
        // This prevents browser resubmission on back button
        $_SESSION['login_error'] = $result['message'];
        $_SESSION['login_email'] = $_POST['email'] ?? '';
        header('Location: /bloodbank/login.php'); exit;
    }
}

$prefillEmail = $_SESSION['login_email'] ?? '';
unset($_SESSION['login_email']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>LifeFlow — Login</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
<div class="auth-layout">
  <div class="auth-brand">
    <div class="brand-content">
      <div class="brand-logo">
        <a href="/bloodbank/public/home.php" style="display:flex;align-items:center;gap:10px;text-decoration:none">
          <span class="logo-drop"></span>
          <span class="logo-text">LifeFlow</span>
        </a>
      </div>
      <h1 class="brand-headline">Every drop<br>counts.</h1>
      <p class="brand-sub">A blood bank management system connecting donors, hospitals, and lives — in real time.</p>
      <div class="brand-stats">
        <div class="stat"><span class="stat-num">3</span><span class="stat-label">APIs Ready</span></div>
        <div class="stat"><span class="stat-num">8</span><span class="stat-label">Modules</span></div>
        <div class="stat"><span class="stat-num">3</span><span class="stat-label">User Roles</span></div>
      </div>
    </div>
    <div class="drop-grid"><?php for($i=0;$i<20;$i++) echo '<span class="drop-dot"></span>'; ?></div>
  </div>
  <div class="auth-form-panel">
    <div class="form-card">
      <div class="form-header"><h2>Welcome back</h2><p>Sign in to your account</p></div>
      <?php if($error): ?>
      <div class="alert alert-error">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="7" stroke="currentColor" stroke-width="1.5"/><path d="M8 5v3.5M8 11h.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        <?= htmlspecialchars($error) ?>
      </div>
      <?php endif; ?>
      <form method="POST" class="auth-form" novalidate>
        <div class="field-group">
          <label>Email address</label>
          <input type="email" name="email" placeholder="you@example.com"
                 value="<?= htmlspecialchars($prefillEmail) ?>" required autocomplete="email">
        </div>
        <div class="field-group">
          <label>Password</label>
          <div class="input-eye-wrap">
            <input type="password" id="pwd" name="password" placeholder="••••••••" required>
            <button type="button" class="eye-btn" onclick="togglePwd()">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
        </div>
        <button type="submit" class="btn-primary">
          Sign in
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
      </form>
      <p class="switch-link">Don't have an account? <a href="register.php">Create one</a></p>
      <div class="demo-hint">
        <strong>Demo login:</strong> admin@lifeflow.com / admin123
      </div>
    </div>
  </div>
</div>
<script>
function togglePwd(){const i=document.getElementById('pwd');i.type=i.type==='text'?'password':'text';}
</script>
</body>
</html>
