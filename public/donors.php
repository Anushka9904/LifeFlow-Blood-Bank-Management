<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Our Donors — LifeFlow</title>
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
.filter-row{display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-bottom:36px}
.filter-row input{padding:10px 16px;background:var(--bg,#fff);border:1px solid var(--border);border-radius:10px;color:var(--text);font-family:var(--font-b);font-size:14px;outline:none;min-width:220px;flex:1}
.filter-row input:focus{border-color:var(--red)}
.filter-row input::placeholder{color:var(--text3)}
.bg-btn{padding:8px 16px;border-radius:999px;border:1px solid var(--border);background:var(--bg,#fff);color:var(--text2);font-family:var(--font-d);font-size:13px;font-weight:700;cursor:pointer;transition:.2s}
.bg-btn:hover,.bg-btn.active{background:var(--red-soft);border-color:rgba(192,57,43,.3);color:var(--red-bright)}
.bg-btn.all-btn{font-family:var(--font-b);font-weight:500;font-size:13px}
.grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}
.dcard{background:var(--bg,#fff);border:1px solid var(--border);border-radius:16px;padding:24px;text-align:center;transition:.2s}
.dcard:hover{border-color:#2e3d56;transform:translateY(-2px)}
.davatar{width:60px;height:60px;border-radius:50%;margin:0 auto 14px;display:flex;align-items:center;justify-content:center;font-family:var(--font-d);font-size:20px;font-weight:800}
.dname{font-family:var(--font-d);font-size:15px;font-weight:700;margin-bottom:6px}
.dcity{font-size:12px;color:var(--text3);margin-bottom:12px;display:flex;align-items:center;justify-content:center;gap:4px}
.blood-pill{display:inline-block;padding:4px 12px;border-radius:999px;font-size:14px;font-weight:700;font-family:var(--font-d);background:var(--red-soft);border:1px solid rgba(192,57,43,.25);color:var(--red-bright);margin-bottom:14px}
.ddonations{font-size:12px;color:var(--text3);margin-bottom:10px}
.ddonations strong{color:var(--teal);font-family:var(--font-d);font-size:14px}
.avail{display:inline-block;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:500}
.avail-yes{background:rgba(0,212,170,.1);color:var(--teal)}
.avail-no{background:rgba(255,255,255,.05);color:var(--text3)}
.empty{text-align:center;padding:80px 20px;color:var(--text3)}
.empty h3{font-family:var(--font-d);font-size:22px;margin-bottom:12px;color:var(--text2)}
.empty p{font-size:15px;margin-bottom:24px;line-height:1.7}
.empty a{color:var(--red-bright);text-decoration:none;font-weight:500;padding:12px 24px;background:var(--red-soft);border:1px solid rgba(192,57,43,.2);border-radius:8px;display:inline-block}
.cta-band{background:var(--red);padding:60px 5%;text-align:center}
.cta-band h2{font-family:var(--font-d);font-size:36px;font-weight:800;letter-spacing:-1px;margin-bottom:14px;color:#fff}
.cta-band p{font-size:16px;color:rgba(255,255,255,.8);margin-bottom:32px}
.cta-btns{display:flex;gap:14px;justify-content:center;flex-wrap:wrap}
.btn-w{padding:13px 28px;background:#fff;color:var(--red);border-radius:10px;font-size:15px;font-weight:600;text-decoration:none}
.btn-t{padding:13px 28px;background:transparent;color:#fff;border:2px solid rgba(255,255,255,.4);border-radius:10px;font-size:15px;font-weight:600;text-decoration:none}
footer{background:var(--dark2);border-top:1px solid var(--border);padding:32px 5%}
.fb{max-width:1200px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
.fb p{font-size:13px;color:var(--text3)}
.fb span{color:var(--red-bright)}
@media(max-width:1000px){.grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:700px){.grid{grid-template-columns:1fr 1fr}.nav-links{display:none}.stats-bar-inner{grid-template-columns:1fr 1fr}}
@media(max-width:440px){.grid{grid-template-columns:1fr}}
</style>
</head>
<body>
<?php
require_once '../config/db.php';
$db = Database::getInstance();

// Real donors from DB — only show name initials, city, blood group, availability (no email for privacy)
$donors = $db->query("
    SELECT d.id, d.blood_group, d.city, d.is_available, d.total_donations,
           u.name
    FROM donors d
    JOIN users u ON d.user_id = u.id
    WHERE u.is_active = 1
    ORDER BY d.total_donations DESC, d.created_at DESC
")->fetchAll();

$totalDonors     = count($donors);
$availableDonors = count(array_filter($donors, fn($d) => $d['is_available']));
$totalDonations  = array_sum(array_column($donors,'total_donations'));

// Blood group counts
$bgCounts = [];
foreach($donors as $d) {
    $bgCounts[$d['blood_group']] = ($bgCounts[$d['blood_group']] ?? 0) + 1;
}

// Avatar color palette (cycles)
$colors = [
    ['bg'=>'rgba(0,212,170,.12)','tc'=>'#00d4aa'],
    ['bg'=>'rgba(192,57,43,.12)','tc'=>'#e74c3c'],
    ['bg'=>'rgba(52,152,219,.12)','tc'=>'#3498db'],
    ['bg'=>'rgba(230,126,34,.12)','tc'=>'#e67e22'],
    ['bg'=>'rgba(155,89,182,.12)','tc'=>'#9b59b6'],
];
?>
<nav>
  <div class="nav-inner">
    <a href="home.php" class="nav-logo"><div class="nav-drop"></div><span class="nav-name">LifeFlow</span></a>
    <div class="nav-links">
      <a href="home.php">Home</a><a href="about.php">About</a>
      <a href="hospitals.php">Hospitals</a><a href="donors.php" class="active">Donors</a>
    </div>
    <div class="nav-btns">
      <a href="../login.php" class="btn-o">Login</a>
      <a href="../register.php" class="btn-s">Register</a>
    </div>
  </div>
</nav>

<div class="page-hero">
  <div class="tag">Our Heroes</div>
  <h1>Meet our <span>Donors</span></h1>
  <p><?= $totalDonors ?> registered donor<?= $totalDonors!=1?'s':'' ?> on LifeFlow<?= $totalDonors>0?', each one a hero who gives the gift of life.':'. Be the first to join!' ?></p>
</div>

<div class="stats-bar">
  <div class="stats-bar-inner">
    <div class="sbar-stat"><span class="sbar-num"><?= $totalDonors ?></span><span class="sbar-label">Registered Donors</span></div>
    <div class="sbar-stat"><span class="sbar-num"><?= $availableDonors ?></span><span class="sbar-label">Available Now</span></div>
    <div class="sbar-stat"><span class="sbar-num"><?= $totalDonations ?></span><span class="sbar-label">Total Donations</span></div>
    <div class="sbar-stat"><span class="sbar-num"><?= count($bgCounts) ?></span><span class="sbar-label">Blood Groups</span></div>
  </div>
</div>

<section>
  <div class="si">
    <?php if(!empty($donors)): ?>
    <div class="filter-row">
      <input type="text" id="searchInput" placeholder="Search by name or city..." oninput="filterDonors()">
      <button class="bg-btn all-btn active" onclick="filterBG('all',this)">All Groups</button>
      <?php foreach(array_keys($bgCounts) as $g): ?>
      <button class="bg-btn" onclick="filterBG('<?= $g ?>',this)"><?= $g ?> <span style="font-size:10px;opacity:.6">(<?= $bgCounts[$g] ?>)</span></button>
      <?php endforeach; ?>
    </div>

    <div class="grid" id="donorsGrid">
      <?php foreach($donors as $i=>$d):
        $n = $d['name'];
        $initials = strtoupper(substr($n,0,1).(strpos($n,' ')!==false?substr(strrchr($n,' '),1,1):substr($n,1,1)));
        $col = $colors[$i % count($colors)];
        // Show only first name + last initial for privacy
        $nameParts = explode(' ', $n);
        $displayName = $nameParts[0].(count($nameParts)>1?' '.strtoupper(substr(end($nameParts),0,1)).'.':'');
      ?>
      <div class="dcard" data-bg="<?= $d['blood_group'] ?>" data-search="<?= strtolower(htmlspecialchars($d['name'].' '.($d['city']??''))) ?>">
        <div class="davatar" style="background:<?= $col['bg'] ?>;color:<?= $col['tc'] ?>"><?= htmlspecialchars($initials) ?></div>
        <div class="dname"><?= htmlspecialchars($displayName) ?></div>
        <?php if($d['city']): ?>
        <div class="dcity">
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          <?= htmlspecialchars($d['city']) ?>
        </div>
        <?php endif; ?>
        <div class="blood-pill"><?= $d['blood_group'] ?></div>
        <div class="ddonations"><strong><?= $d['total_donations'] ?></strong> donation<?= $d['total_donations']!=1?'s':'' ?></div>
        <span class="avail <?= $d['is_available']?'avail-yes':'avail-no' ?>"><?= $d['is_available']?'Available':'Donated recently' ?></span>
      </div>
      <?php endforeach; ?>
    </div>
    <p id="noRes" style="display:none;text-align:center;color:var(--text3);padding:40px;font-size:14px">No donors found matching your search.</p>
    <p style="text-align:center;color:var(--text3);margin-top:28px;font-size:13px">Only first name shown to protect donor privacy.</p>

    <?php else: ?>
    <div class="empty">
      <h3>No donors registered yet</h3>
      <p>Be the first donor on LifeFlow.<br>Register in 30 seconds and start saving lives today.</p>
      <a href="../register.php">Register as Donor →</a>
    </div>
    <?php endif; ?>
  </div>
</section>

<div class="cta-band">
  <h2>Ready to be a hero?</h2>
  <p>Join our donor community. Registration is free and takes 30 seconds.</p>
  <div class="cta-btns">
    <a href="../register.php" class="btn-w">Register as Donor</a>
    <a href="about.php" class="btn-t">Learn More</a>
  </div>
</div>

<footer>
  <div class="fb">
    <p>© 2025 LifeFlow Blood Bank. Made with <span>♥</span> to save lives.</p>
    <a href="home.php" style="color:var(--text3);font-size:13px;text-decoration:none">← Back to Home</a>
  </div>
</footer>

<script>
let activeBG = 'all';
function filterDonors(){
  const q = document.getElementById('searchInput').value.toLowerCase();
  let v = 0;
  document.querySelectorAll('.dcard').forEach(c=>{
    const matchSearch = c.dataset.search.includes(q);
    const matchBG = activeBG==='all' || c.dataset.bg===activeBG;
    const show = matchSearch && matchBG;
    c.style.display = show ? '' : 'none';
    if(show) v++;
  });
  document.getElementById('noRes').style.display = v===0 ? 'block' : 'none';
}
function filterBG(bg, btn){
  activeBG = bg;
  document.querySelectorAll('.bg-btn').forEach(b=>b.classList.remove('active'));
  btn.classList.add('active');
  filterDonors();
}
</script>
</body>
</html>
