<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Enrolled Hospitals — LifeFlow</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--red:#c0392b;--red-bright:#e74c3c;--red-soft:rgba(192,57,43,0.08);--dark:#ffffff;--dark2:#f8f9fc;--dark3:#f1f4f9;--border:#e2e8f0;--teal:#059669;--amber:#d97706;--text:#1a202c;--text2:#4a5568;--text3:#a0aec0;--font-d:'Syne',sans-serif;--font-b:'DM Sans',sans-serif;}
html{scroll-behavior:smooth}body{font-family:var(--font-b);background:var(--dark2);color:var(--text);-webkit-font-smoothing:antialiased}
nav{position:fixed;top:0;left:0;right:0;z-index:100;background:rgba(255,255,255,0.92);backdrop-filter:blur(12px);border-bottom:1px solid var(--border);padding:0 5%;box-shadow:0 1px 8px rgba(0,0,0,0.06)}
.nav-inner{max-width:1200px;margin:0 auto;height:68px;display:flex;align-items:center;justify-content:space-between}
.nav-logo{display:flex;align-items:center;gap:10px;text-decoration:none}
.nav-drop{width:22px;height:28px;background:var(--red);border-radius:50% 50% 50% 50%/40% 40% 60% 60%;transform:rotate(-10deg)}
.nav-name{font-family:var(--font-d);font-size:20px;font-weight:800;color:var(--text);letter-spacing:-.5px}
.nav-links{display:flex;gap:32px}
.nav-links a{color:var(--text2);text-decoration:none;font-size:14px;font-weight:500;transition:.2s}
.nav-links a:hover,.nav-links a.active{color:var(--text)}
.nav-btns{display:flex;gap:12px}
.btn-o{padding:9px 20px;border:1px solid var(--border);border-radius:8px;color:var(--text2);text-decoration:none;font-size:14px;transition:.2s}
.btn-o:hover{border-color:var(--red);color:var(--red)}
.btn-s{padding:9px 20px;background:var(--red);border-radius:8px;color:#fff;text-decoration:none;font-size:14px;transition:.2s}
.btn-s:hover{background:var(--red-bright)}
.page-hero{padding:140px 5% 60px;background:var(--dark2);border-bottom:1px solid var(--border);text-align:center}
.page-hero h1{font-family:var(--font-d);font-size:clamp(36px,5vw,58px);font-weight:800;letter-spacing:-2px;margin-bottom:16px}
.page-hero h1 span{color:var(--red)}
.page-hero p{font-size:17px;color:var(--text2);max-width:500px;margin:0 auto;line-height:1.7}
.tag{display:inline-block;padding:5px 14px;background:var(--red-soft);border:1px solid rgba(192,57,43,.2);border-radius:999px;font-size:11px;font-weight:600;color:var(--red-bright);letter-spacing:1px;text-transform:uppercase;margin-bottom:16px}
.stats-bar{background:var(--dark2);border-top:1px solid var(--border);border-bottom:1px solid var(--border);padding:40px 5%}
.stats-bar-inner{max-width:1200px;margin:0 auto;display:grid;grid-template-columns:repeat(4,1fr);text-align:center}
.sbar-stat{padding:10px;border-right:1px solid var(--border)}
.sbar-stat:last-child{border-right:none}
.sbar-num{font-family:var(--font-d);font-size:36px;font-weight:800;color:var(--red-bright);display:block}
.sbar-label{font-size:13px;color:var(--text3);margin-top:4px;display:block}
section{padding:80px 5%}
.si{max-width:1200px;margin:0 auto}
.search-wrap{margin-bottom:36px}
.search-wrap input{width:100%;max-width:420px;padding:12px 16px;background:var(--bg,#fff);border:1px solid var(--border);border-radius:10px;color:var(--text);font-family:var(--font-b);font-size:14px;outline:none;transition:.2s}
.search-wrap input:focus{border-color:var(--red)}
.search-wrap input::placeholder{color:var(--text3)}
.grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px}
.card{background:var(--bg,#fff);border:1px solid var(--border);border-radius:16px;padding:28px;transition:.2s}
.card:hover{border-color:#2e3d56;transform:translateY(-3px)}
.card-top{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:16px}
.avatar{width:52px;height:52px;border-radius:12px;background:var(--red-soft);border:1px solid rgba(192,57,43,.2);display:flex;align-items:center;justify-content:center;font-family:var(--font-d);font-size:20px;font-weight:800;color:var(--red-bright)}
.badge{padding:4px 10px;border-radius:999px;font-size:11px;font-weight:500;background:rgba(0,212,170,.1);color:var(--teal)}
.hname{font-family:var(--font-d);font-size:16px;font-weight:700;margin-bottom:5px}
.hemail{font-size:12px;color:var(--text3);margin-bottom:5px;word-break:break-all}
.hmember{font-size:12px;color:var(--text3)}
.divider{height:1px;background:var(--border);margin:16px 0}
.hstats{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;text-align:center}
.hsnum{font-family:var(--font-d);font-size:22px;font-weight:800;display:block;line-height:1;margin-bottom:3px}
.hslabel{font-size:11px;color:var(--text3)}
.hbtn{display:block;width:100%;text-align:center;padding:10px;background:var(--red-soft);border:1px solid rgba(192,57,43,.2);border-radius:8px;color:var(--red-bright);text-decoration:none;font-size:13px;font-weight:500;margin-top:16px;transition:.2s}
.hbtn:hover{background:var(--red);color:#fff}
.empty{text-align:center;padding:80px 20px;color:var(--text3)}
.empty h3{font-family:var(--font-d);font-size:22px;margin-bottom:12px;color:var(--text2)}
.empty p{font-size:15px;margin-bottom:24px;line-height:1.7}
.empty a{color:var(--red-bright);text-decoration:none;font-weight:500;padding:12px 24px;background:var(--red-soft);border:1px solid rgba(192,57,43,.2);border-radius:8px;display:inline-block}
footer{background:var(--dark2);border-top:1px solid var(--border);padding:32px 5%}
.fb{max-width:1200px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
.fb p{font-size:13px;color:var(--text3)}
.fb span{color:var(--red-bright)}
@media(max-width:900px){.grid{grid-template-columns:1fr 1fr}.nav-links{display:none}.stats-bar-inner{grid-template-columns:repeat(2,1fr)}}
@media(max-width:600px){.grid{grid-template-columns:1fr}.stats-bar-inner{grid-template-columns:1fr 1fr}}
</style>
</head>
<body>
<?php
require_once '../config/db.php';
$db = Database::getInstance();

$hospitals = $db->query("
    SELECT u.id, u.name, u.email, u.created_at,
        (SELECT COUNT(*) FROM blood_requests WHERE hospital_id=u.id) as total_requests,
        (SELECT COUNT(*) FROM blood_requests WHERE hospital_id=u.id AND status='fulfilled') as fulfilled,
        (SELECT COUNT(*) FROM blood_requests WHERE hospital_id=u.id AND status='pending') as pending
    FROM users u WHERE u.role='hospital' AND u.is_active=1
    ORDER BY u.created_at DESC
")->fetchAll();

$totalHospitals = count($hospitals);
$totalRequests  = array_sum(array_column($hospitals,'total_requests'));
$totalFulfilled = array_sum(array_column($hospitals,'fulfilled'));
$fulfillRate    = $totalRequests>0 ? round(($totalFulfilled/$totalRequests)*100) : 0;
?>
<nav>
  <div class="nav-inner">
    <a href="home.php" class="nav-logo"><div class="nav-drop"></div><span class="nav-name">LifeFlow</span></a>
    <div class="nav-links">
      <a href="home.php">Home</a><a href="about.php">About</a>
      <a href="hospitals.php" class="active">Hospitals</a><a href="donors.php">Donors</a>
    </div>
    <div class="nav-btns">
      <a href="../login.php" class="btn-o">Login</a>
      <a href="../register.php" class="btn-s">Register</a>
    </div>
  </div>
</nav>

<div class="page-hero">
  <div class="tag">Network</div>
  <h1>Enrolled <span>Hospitals</span></h1>
  <p><?= $totalHospitals ?> hospital<?= $totalHospitals!=1?'s':'' ?> currently registered on LifeFlow<?= $totalHospitals>0?', managing blood requests in real time.':'. Be the first to join!' ?></p>
</div>

<div class="stats-bar">
  <div class="stats-bar-inner">
    <div class="sbar-stat"><span class="sbar-num"><?= $totalHospitals ?></span><span class="sbar-label">Hospitals Enrolled</span></div>
    <div class="sbar-stat"><span class="sbar-num"><?= $totalRequests ?></span><span class="sbar-label">Total Requests</span></div>
    <div class="sbar-stat"><span class="sbar-num"><?= $totalFulfilled ?></span><span class="sbar-label">Requests Fulfilled</span></div>
    <div class="sbar-stat"><span class="sbar-num"><?= $fulfillRate ?>%</span><span class="sbar-label">Fulfillment Rate</span></div>
  </div>
</div>

<section>
  <div class="si">
    <?php if(!empty($hospitals)): ?>
    <div class="search-wrap">
      <input type="text" id="searchInput" placeholder="Search hospitals by name or email..." oninput="filterCards()">
    </div>
    <div class="grid" id="grid">
      <?php foreach($hospitals as $h):
        $n = $h['name'];
        $initials = strtoupper(substr($n,0,1).(strpos($n,' ')!==false?substr(strrchr($n,' '),1,1):substr($n,1,1)));
        $rate = $h['total_requests']>0 ? round(($h['fulfilled']/$h['total_requests'])*100) : 0;
        $rc = $rate>=90?'var(--teal)':($rate>=70?'var(--amber)':'var(--red-bright)');
      ?>
      <div class="card" data-search="<?= strtolower(htmlspecialchars($h['name'].' '.$h['email'])) ?>">
        <div class="card-top">
          <div class="avatar"><?= htmlspecialchars($initials) ?></div>
          <span class="badge">Active</span>
        </div>
        <div class="hname"><?= htmlspecialchars($h['name']) ?></div>
        <div class="hemail"><?= htmlspecialchars($h['email']) ?></div>
        <div class="hmember">Member since <?= date('M Y',strtotime($h['created_at'])) ?></div>
        <div class="divider"></div>
        <div class="hstats">
          <div><span class="hsnum"><?= $h['total_requests'] ?></span><span class="hslabel">Requests</span></div>
          <div><span class="hsnum" style="color:var(--teal)"><?= $h['fulfilled'] ?></span><span class="hslabel">Fulfilled</span></div>
          <div><span class="hsnum" style="color:<?= $rc ?>"><?= $rate ?>%</span><span class="hslabel">Rate</span></div>
        </div>
        <a href="../register.php" class="hbtn">Request Blood →</a>
      </div>
      <?php endforeach; ?>
    </div>
    <p id="noRes" style="display:none;text-align:center;color:var(--text3);padding:40px;font-size:14px">No hospitals found.</p>

    <?php else: ?>
    <div class="empty">
      <h3>No hospitals enrolled yet</h3>
      <p>Be the first hospital to join the LifeFlow network.<br>Registration is free and gives you real-time access to blood inventory.</p>
      <a href="../register.php">Register your hospital →</a>
    </div>
    <?php endif; ?>
  </div>
</section>

<footer>
  <div class="fb">
    <p>© 2025 LifeFlow Blood Bank. Made with <span>♥</span> to save lives.</p>
    <a href="home.php" style="color:var(--text3);font-size:13px;text-decoration:none">← Back to Home</a>
  </div>
</footer>
<script>
function filterCards(){
  const q=document.getElementById('searchInput').value.toLowerCase();
  let v=0;
  document.querySelectorAll('.card').forEach(c=>{
    const show=c.dataset.search.includes(q);
    c.style.display=show?'':'none';if(show)v++;
  });
  document.getElementById('noRes').style.display=v===0?'block':'none';
}
</script>
</body>
</html>
