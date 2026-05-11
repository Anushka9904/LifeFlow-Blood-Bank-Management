<?php
require_once __DIR__.'/../config/db.php';
$db = Database::getInstance();
$statDonors    = (int)$db->query("SELECT COUNT(*) FROM donors")->fetchColumn();
$statHospitals = (int)$db->query("SELECT COUNT(*) FROM users WHERE role='hospital'")->fetchColumn();
$statDonations = (int)$db->query("SELECT COUNT(*) FROM donations")->fetchColumn();
$statCamps     = (int)$db->query("SELECT COUNT(*) FROM donation_camps")->fetchColumn();
$statUnits     = (int)$db->query("SELECT COALESCE(SUM(units),0) FROM blood_inventory")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>LifeFlow — Blood Bank Management System</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --red:#c0392b;--red-bright:#e74c3c;--red-soft:rgba(192,57,43,0.08);--red-glow:rgba(192,57,43,0.2);
  --dark:#ffffff;--dark2:#f8f9fc;--dark3:#f1f4f9;--dark4:#e8edf5;
  --border:#e2e8f0;--teal:#059669;--amber:#d97706;
  --text:#1a202c;--text2:#4a5568;--text3:#a0aec0;
  --font-d:'Syne',sans-serif;--font-b:'DM Sans',sans-serif;
}
html{scroll-behavior:smooth}
body{font-family:var(--font-b);background:var(--dark2);color:var(--text);-webkit-font-smoothing:antialiased;overflow-x:hidden}

/* NAV */
nav{position:fixed;top:0;left:0;right:0;z-index:100;background:rgba(255,255,255,0.92);backdrop-filter:blur(12px);border-bottom:1px solid var(--border);padding:0 5%;box-shadow:0 1px 8px rgba(0,0,0,0.06)}
.nav-inner{max-width:1200px;margin:0 auto;height:68px;display:flex;align-items:center;justify-content:space-between}
.nav-logo{display:flex;align-items:center;gap:10px;text-decoration:none}
.nav-drop{width:22px;height:28px;background:var(--red);border-radius:50% 50% 50% 50%/40% 40% 60% 60%;transform:rotate(-10deg);box-shadow:0 0 16px var(--red-glow)}
.nav-name{font-family:var(--font-d);font-size:20px;font-weight:800;color:var(--text);letter-spacing:-.5px}
.nav-links{display:flex;align-items:center;gap:32px}
.nav-links a{color:var(--text2);text-decoration:none;font-size:14px;font-weight:500;transition:.2s}
.nav-links a:hover{color:var(--text)}
.nav-btns{display:flex;gap:12px}
.btn-outline{padding:9px 20px;border:1px solid var(--border);border-radius:8px;color:var(--text2);text-decoration:none;font-size:14px;font-weight:500;transition:.2s}
.btn-outline:hover{border-color:var(--red);color:var(--red)}
.btn-solid{padding:9px 20px;background:var(--red);border:none;border-radius:8px;color:#fff;text-decoration:none;font-size:14px;font-weight:500;transition:.2s;cursor:pointer}
.btn-solid:hover{background:var(--red-bright);box-shadow:0 4px 20px var(--red-glow)}

/* HERO */
.hero{min-height:100vh;display:flex;align-items:center;padding:120px 5% 80px;position:relative;overflow:hidden}
.hero::before{content:'';position:absolute;top:-200px;left:-200px;width:700px;height:700px;background:radial-gradient(circle,rgba(192,57,43,0.12) 0%,transparent 65%);pointer-events:none}
.hero::after{content:'';position:absolute;bottom:-100px;right:-100px;width:500px;height:500px;background:radial-gradient(circle,rgba(0,212,170,0.06) 0%,transparent 65%);pointer-events:none}
.hero-inner{max-width:1200px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:80px;align-items:center;position:relative;z-index:1}
.hero-eyebrow{display:inline-flex;align-items:center;gap:8px;padding:6px 14px;background:var(--red-soft);border:1px solid rgba(192,57,43,.2);border-radius:999px;font-size:12px;font-weight:600;color:var(--red-bright);letter-spacing:.5px;text-transform:uppercase;margin-bottom:24px}
.hero-eyebrow span{width:6px;height:6px;background:var(--red-bright);border-radius:50%;animation:blink 1.5s ease-in-out infinite}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.3}}
.hero h1{font-family:var(--font-d);font-size:clamp(42px,5vw,68px);font-weight:800;line-height:1.05;letter-spacing:-2.5px;margin-bottom:24px}
.hero h1 span{color:var(--red)}
.hero p{font-size:17px;color:var(--text2);line-height:1.75;margin-bottom:40px;max-width:480px}
.hero-btns{display:flex;gap:14px;flex-wrap:wrap}
.btn-hero{padding:14px 28px;border-radius:10px;font-size:15px;font-weight:600;text-decoration:none;transition:.2s;font-family:var(--font-b)}
.btn-hero-primary{background:var(--red);color:#fff;border:none}
.btn-hero-primary:hover{background:var(--red-bright);box-shadow:0 8px 32px var(--red-glow);transform:translateY(-1px)}
.btn-hero-secondary{background:transparent;color:var(--text);border:1px solid var(--border)}
.btn-hero-secondary:hover{border-color:var(--text2)}
.hero-stats{display:flex;gap:32px;margin-top:48px;padding-top:40px;border-top:1px solid var(--border)}
.hstat-num{font-family:var(--font-d);font-size:32px;font-weight:800;color:var(--text);display:block;line-height:1}
.hstat-label{font-size:13px;color:var(--text3);margin-top:4px;display:block}

/* Hero visual */
.hero-visual{position:relative}
.hero-card{background:var(--bg,#fff);border:1px solid var(--border);border-radius:20px;padding:28px;margin-bottom:16px;animation:float 4s ease-in-out infinite}
@keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}
.hero-card2{animation-delay:-.5s;animation-duration:5s}
.blood-type-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:20px}
.bt-pill{background:var(--dark4);border:1px solid var(--border);border-radius:10px;padding:12px 8px;text-align:center}
.bt-pill .type{font-family:var(--font-d);font-size:18px;font-weight:800;color:var(--red-bright)}
.bt-pill .units{font-size:11px;color:var(--text3);margin-top:2px}
.bt-pill.low .type{color:var(--amber)}
.bt-pill.ok .type{color:var(--teal)}
.card-label{font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:1px;color:var(--text3);margin-bottom:14px}
.req-item{display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border)}
.req-item:last-child{border-bottom:none}
.req-left{display:flex;align-items:center;gap:10px}
.req-avatar{width:32px;height:32px;border-radius:50%;background:var(--dark4);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:600;color:var(--teal)}
.req-name{font-size:13px;font-weight:500;color:var(--text)}
.req-sub{font-size:11px;color:var(--text3)}
.badge{display:inline-block;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:500}
.badge-red{background:var(--red-soft);color:var(--red-bright)}
.badge-teal{background:rgba(0,212,170,.1);color:var(--teal)}
.badge-amber{background:rgba(230,126,34,.1);color:var(--amber)}

/* SECTION */
section{padding:100px 5%}
.section-inner{max-width:1200px;margin:0 auto}
.section-tag{display:inline-block;padding:5px 14px;background:var(--red-soft);border:1px solid rgba(192,57,43,.2);border-radius:999px;font-size:11px;font-weight:600;color:var(--red-bright);letter-spacing:1px;text-transform:uppercase;margin-bottom:16px}
.section-title{font-family:var(--font-d);font-size:clamp(32px,4vw,48px);font-weight:800;letter-spacing:-1.5px;margin-bottom:16px;line-height:1.1}
.section-sub{font-size:16px;color:var(--text2);line-height:1.7;max-width:560px}

/* HOW IT WORKS */
.how-bg{background:var(--dark2);border-top:1px solid var(--border);border-bottom:1px solid var(--border)}
.steps-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:2px;margin-top:64px;background:var(--border);border:1px solid var(--border);border-radius:20px;overflow:hidden}
.step{background:var(--dark2);padding:40px 36px;position:relative}
.step-num{font-family:var(--font-d);font-size:64px;font-weight:800;color:var(--red-soft);line-height:1;margin-bottom:20px;color:rgba(192,57,43,.15)}
.step-icon{width:48px;height:48px;background:var(--red-soft);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:20px;color:var(--red-bright)}
.step h3{font-family:var(--font-d);font-size:18px;font-weight:700;margin-bottom:10px}
.step p{font-size:14px;color:var(--text2);line-height:1.7}

/* STATS BAND */
.stats-band{background:var(--red);padding:60px 5%}
.stats-band-inner{max-width:1200px;margin:0 auto;display:grid;grid-template-columns:repeat(4,1fr);gap:0;text-align:center}
.band-stat{padding:20px;border-right:1px solid rgba(255,255,255,.15)}
.band-stat:last-child{border-right:none}
.band-num{font-family:var(--font-d);font-size:48px;font-weight:800;color:#fff;line-height:1;display:block}
.band-label{font-size:14px;color:rgba(255,255,255,.7);margin-top:6px;display:block}

/* HOSPITALS */
.hospitals-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px;margin-top:56px}
.hospital-card{background:var(--bg,#fff);border:1px solid var(--border);border-radius:16px;padding:28px;transition:.2s;cursor:default}
.hospital-card:hover{border-color:#2e3d56;transform:translateY(-2px)}
.hosp-avatar{width:52px;height:52px;border-radius:12px;background:var(--red-soft);border:1px solid rgba(192,57,43,.2);display:flex;align-items:center;justify-content:center;font-family:var(--font-d);font-size:18px;font-weight:800;color:var(--red-bright);margin-bottom:16px}
.hosp-name{font-family:var(--font-d);font-size:16px;font-weight:700;margin-bottom:6px}
.hosp-city{font-size:13px;color:var(--text3);margin-bottom:16px;display:flex;align-items:center;gap:5px}
.hosp-stats{display:flex;gap:16px}
.hosp-stat span:first-child{font-family:var(--font-d);font-size:20px;font-weight:700;color:var(--text);display:block}
.hosp-stat span:last-child{font-size:11px;color:var(--text3)}

/* TESTIMONIALS */
.testi-bg{background:var(--dark2);border-top:1px solid var(--border);border-bottom:1px solid var(--border)}
.testi-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px;margin-top:56px}
.testi-card{background:var(--bg,#fff);border:1px solid var(--border);border-radius:16px;padding:28px}
.testi-stars{display:flex;gap:4px;margin-bottom:16px}
.testi-stars span{color:var(--amber);font-size:16px}
.testi-text{font-size:15px;color:var(--text2);line-height:1.75;margin-bottom:20px;font-style:italic}
.testi-text::before{content:'"';color:var(--red);font-size:24px;font-family:var(--font-d);font-style:normal;display:block;margin-bottom:4px}
.testi-author{display:flex;align-items:center;gap:12px}
.testi-avatar{width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;flex-shrink:0}
.testi-name{font-size:14px;font-weight:600;color:var(--text)}
.testi-role{font-size:12px;color:var(--text3)}

/* ABOUT */
.about-grid{display:grid;grid-template-columns:1fr 1fr;gap:80px;align-items:center;margin-top:0}
.about-img{background:var(--bg,#fff);border:1px solid var(--border);border-radius:20px;padding:40px;position:relative;overflow:hidden}
.about-img::before{content:'';position:absolute;top:-60px;right:-60px;width:200px;height:200px;background:radial-gradient(circle,var(--red-glow) 0%,transparent 70%)}
.about-feature{display:flex;gap:16px;margin-bottom:28px}
.about-feature:last-child{margin-bottom:0}
.feat-icon{width:44px;height:44px;background:var(--red-soft);border-radius:10px;display:flex;align-items:center;justify-content:center;color:var(--red-bright);flex-shrink:0}
.feat-title{font-size:15px;font-weight:600;margin-bottom:4px;color:var(--text)}
.feat-desc{font-size:13px;color:var(--text2);line-height:1.6}
.about-badges{display:flex;gap:10px;flex-wrap:wrap;margin-top:24px}
.about-badge{padding:8px 16px;background:var(--dark4);border:1px solid var(--border);border-radius:999px;font-size:13px;color:var(--text2)}

/* CTA */
.cta-section{background:linear-gradient(135deg,var(--dark2) 0%,var(--dark) 100%);border-top:1px solid var(--border);text-align:center;padding:120px 5%}
.cta-inner{max-width:640px;margin:0 auto}
.cta-inner h2{font-family:var(--font-d);font-size:clamp(36px,4vw,52px);font-weight:800;letter-spacing:-1.5px;margin-bottom:20px}
.cta-inner p{font-size:16px;color:var(--text2);line-height:1.7;margin-bottom:40px}
.cta-btns{display:flex;gap:14px;justify-content:center;flex-wrap:wrap}

/* FOOTER */
footer{background:var(--dark2);border-top:1px solid var(--border);padding:60px 5% 32px}
.footer-inner{max-width:1200px;margin:0 auto}
.footer-top{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:48px;margin-bottom:48px}
.footer-brand p{font-size:14px;color:var(--text2);line-height:1.7;margin-top:14px;max-width:280px}
.footer-col h4{font-family:var(--font-d);font-size:13px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;color:var(--text3);margin-bottom:16px}
.footer-col a{display:block;font-size:14px;color:var(--text2);text-decoration:none;margin-bottom:10px;transition:.2s}
.footer-col a:hover{color:var(--red-bright)}
.footer-bottom{border-top:1px solid var(--border);padding-top:24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
.footer-bottom p{font-size:13px;color:var(--text3)}
.footer-bottom span{color:var(--red-bright)}

/* RESPONSIVE */
@media(max-width:960px){.hero-inner{grid-template-columns:1fr}.hero-visual{display:none}.steps-grid{grid-template-columns:1fr}.hospitals-grid{grid-template-columns:1fr 1fr}.testi-grid{grid-template-columns:1fr 1fr}.about-grid{grid-template-columns:1fr}.stats-band-inner{grid-template-columns:repeat(2,1fr)}.footer-top{grid-template-columns:1fr 1fr}}
@media(max-width:600px){.hospitals-grid{grid-template-columns:1fr}.testi-grid{grid-template-columns:1fr}.stats-band-inner{grid-template-columns:1fr}.nav-links{display:none}.band-stat{border-right:none;border-bottom:1px solid rgba(255,255,255,.15)}.band-stat:last-child{border-bottom:none}}
</style>
</head>
<body>

<!-- NAV -->
<nav>
  <div class="nav-inner">
    <a href="home.php" class="nav-logo">
      <div class="nav-drop"></div>
      <span class="nav-name">LifeFlow</span>
    </a>
    <div class="nav-links">
      <a href="home.php">Home</a>
      <a href="about.php">About</a>
      <a href="hospitals.php">Hospitals</a>
      <a href="donors.php">Donors</a>
      <a href="home.php#contact">Contact</a>
    </div>
    <div class="nav-btns">
      <a href="../login.php" class="btn-outline">Login</a>
      <a href="../register.php" class="btn-solid">Register</a>
    </div>
  </div>
</nav>

<!-- HERO -->
<section class="hero">
  <div class="hero-inner">
    <div class="hero-content">
      <div class="hero-eyebrow"><span></span>India's Smart Blood Bank System</div>
      <h1>Saving Lives,<br>One Drop <span>at a Time.</span></h1>
      <p>LifeFlow connects blood donors, hospitals, and blood banks in real time — making blood available where it's needed most, faster than ever before.</p>
      <div class="hero-btns">
        <a href="../register.php" class="btn-hero btn-hero-primary">Become a Donor</a>
        <a href="about.php" class="btn-hero btn-hero-secondary">Learn More</a>
      </div>
      <div class="hero-stats">
        <div><span class="hstat-num"><?= $statDonors > 0 ? number_format($statDonors) : "0" ?></span><span class="hstat-label">Donors Registered</span></div>
        <div><span class="hstat-num"><?= $statHospitals > 0 ? $statHospitals : "0" ?></span><span class="hstat-label">Hospitals Enrolled</span></div>
        <div><span class="hstat-num"><?= $statDonations > 0 ? number_format($statDonations) : "0" ?></span><span class="hstat-label">Lives Saved</span></div>
      </div>
    </div>
    <div class="hero-visual">
      <div class="hero-card">
        <div class="card-label">Blood Inventory — Live</div>
        <div class="blood-type-grid">
          <div class="bt-pill ok"><div class="type">O+</div><div class="units">52 units</div></div>
          <div class="bt-pill ok"><div class="type">A+</div><div class="units">45 units</div></div>
          <div class="bt-pill ok"><div class="type">B+</div><div class="units">38 units</div></div>
          <div class="bt-pill low"><div class="type">AB-</div><div class="units">4 units</div></div>
          <div class="bt-pill low"><div class="type">B-</div><div class="units">8 units</div></div>
          <div class="bt-pill ok"><div class="type">O-</div><div class="units">10 units</div></div>
          <div class="bt-pill ok"><div class="type">A-</div><div class="units">12 units</div></div>
          <div class="bt-pill ok"><div class="type">AB+</div><div class="units">15 units</div></div>
        </div>
      </div>
      <div class="hero-card hero-card2">
        <div class="card-label">Recent Requests</div>
        <div class="req-item">
          <div class="req-left"><div class="req-avatar">CH</div><div><div class="req-name">City Hospital</div><div class="req-sub">Needs O+ — 2 units</div></div></div>
          <span class="badge badge-red">Critical</span>
        </div>
        <div class="req-item">
          <div class="req-left"><div class="req-avatar">AH</div><div><div class="req-name">Apollo Hospital</div><div class="req-sub">Needs A+ — 1 unit</div></div></div>
          <span class="badge badge-amber">Urgent</span>
        </div>
        <div class="req-item">
          <div class="req-left"><div class="req-avatar">MH</div><div><div class="req-name">Max Healthcare</div><div class="req-sub">Needs B+ — 3 units</div></div></div>
          <span class="badge badge-teal">Normal</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="how-bg">
  <div class="section-inner">
    <div class="section-tag">How it works</div>
    <h2 class="section-title">Simple. Fast. Life-saving.</h2>
    <p class="section-sub">Three easy steps connect donors to patients in need — in real time, with full transparency.</p>
    <div class="steps-grid">
      <div class="step">
        <div class="step-num">01</div>
        <div class="step-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
        <h3>Register as a Donor</h3>
        <p>Sign up for free, provide your blood group and contact details. The admin verifies and activates your donor profile within 24 hours.</p>
      </div>
      <div class="step">
        <div class="step-num">02</div>
        <div class="step-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
        <h3>Hospital Requests Blood</h3>
        <p>Registered hospitals submit blood requests instantly. The system checks live inventory and notifies matching donors automatically.</p>
      </div>
      <div class="step">
        <div class="step-num">03</div>
        <div class="step-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg></div>
        <h3>Donate & Get Certified</h3>
        <p>Donate at a camp or hospital. Get a downloadable certificate instantly. Track your donation history and impact in your portal.</p>
      </div>
    </div>
  </div>
</section>

<!-- STATS BAND -->
<div class="stats-band">
  <div class="stats-band-inner">
    <div class="band-stat"><span class="band-num"><?= number_format($statDonors) ?></span><span class="band-label">Registered Donors</span></div>
    <div class="band-stat"><span class="band-num"><?= $statHospitals ?></span><span class="band-label">Hospitals Enrolled</span></div>
    <div class="band-stat"><span class="band-num"><?= number_format($statDonations) ?></span><span class="band-label">Lives Saved</span></div>
    <div class="band-stat"><span class="band-num"><?= $statCamps ?></span><span class="band-label">Camps Organized</span></div>
  </div>
</div>

<!-- HOSPITALS -->
<section id="hospitals">
  <div class="section-inner">
    <div class="section-tag">Our Network</div>
    <h2 class="section-title">Enrolled Hospitals</h2>
    <p class="section-sub">Leading hospitals and blood banks across India trust LifeFlow to manage their blood supply chain efficiently.</p>
    <div class="hospitals-grid">
      <?php
      $hospitals = [
        ['name'=>'City General Hospital','city'=>'Mumbai','initials'=>'CG','requests'=>142,'fulfilled'=>138],
        ['name'=>'Apollo Multi-Specialty','city'=>'Delhi','initials'=>'AP','requests'=>98,'fulfilled'=>95],
        ['name'=>'Max Healthcare Center','city'=>'Bangalore','initials'=>'MH','requests'=>76,'fulfilled'=>74],
        ['name'=>'Fortis Memorial Hospital','city'=>'Chennai','initials'=>'FM','requests'=>115,'fulfilled'=>110],
        ['name'=>'AIIMS Blood Bank','city'=>'Hyderabad','initials'=>'AI','requests'=>203,'fulfilled'=>198],
        ['name'=>'Lilavati Hospital','city'=>'Pune','initials'=>'LH','requests'=>67,'fulfilled'=>65],
        ['name'=>'Kokilaben Hospital','city'=>'Ahmedabad','initials'=>'KH','requests'=>89,'fulfilled'=>87],
        ['name'=>'Manipal Hospitals','city'=>'Kolkata','initials'=>'MN','requests'=>54,'fulfilled'=>52],
        ['name'=>'Narayana Health City','city'=>'Jaipur','initials'=>'NH','requests'=>121,'fulfilled'=>118],
      ];
      foreach($hospitals as $h): ?>
      <div class="hospital-card">
        <div class="hosp-avatar"><?= $h['initials'] ?></div>
        <div class="hosp-name"><?= $h['name'] ?></div>
        <div class="hosp-city"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg><?= $h['city'] ?></div>
        <div class="hosp-stats">
          <div class="hosp-stat"><span><?= $h['requests'] ?></span><span>Requests</span></div>
          <div class="hosp-stat"><span style="color:var(--teal)"><?= $h['fulfilled'] ?></span><span>Fulfilled</span></div>
          <div class="hosp-stat"><span style="color:var(--amber)"><?= round(($h['fulfilled']/$h['requests'])*100) ?>%</span><span>Rate</span></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div style="text-align:center;margin-top:40px">
      <a href="hospitals.php" class="btn-hero btn-hero-secondary" style="display:inline-flex;align-items:center;gap:8px">View All Hospitals →</a>
    </div>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="testi-bg">
  <div class="section-inner">
    <div class="section-tag">Stories</div>
    <h2 class="section-title">Happy Donors &amp; Receivers</h2>
    <p class="section-sub">Real stories from people whose lives have been touched by LifeFlow.</p>
    <div class="testi-grid">
      <div class="testi-card">
        <div class="testi-stars"><span>★</span><span>★</span><span>★</span><span>★</span><span>★</span></div>
        <div class="testi-text">I donated blood three times through LifeFlow. The process is incredibly smooth — I got my certificate the same day. Knowing my blood saved someone's life is the best feeling in the world.</div>
        <div class="testi-author">
          <div class="testi-avatar" style="background:rgba(0,212,170,.1);color:var(--teal)">RK</div>
          <div><div class="testi-name">Rahul Kulkarni</div><div class="testi-role">Blood Donor · Mumbai</div></div>
        </div>
      </div>
      <div class="testi-card">
        <div class="testi-stars"><span>★</span><span>★</span><span>★</span><span>★</span><span>★</span></div>
        <div class="testi-text">My daughter needed AB- urgently after surgery. LifeFlow found a matching donor within 2 hours. I don't have words to describe the gratitude I feel. This system literally saved her life.</div>
        <div class="testi-author">
          <div class="testi-avatar" style="background:rgba(192,57,43,.1);color:var(--red-bright)">SP</div>
          <div><div class="testi-name">Sunita Patel</div><div class="testi-role">Patient's Family · Delhi</div></div>
        </div>
      </div>
      <div class="testi-card">
        <div class="testi-stars"><span>★</span><span>★</span><span>★</span><span>★</span><span>★</span></div>
        <div class="testi-text">As a hospital administrator, LifeFlow transformed how we manage blood supply. Real-time inventory, instant requests, and SMS alerts — it's exactly what modern healthcare needs.</div>
        <div class="testi-author">
          <div class="testi-avatar" style="background:rgba(52,152,219,.1);color:#3498db">DM</div>
          <div><div class="testi-name">Dr. Deepak Mehta</div><div class="testi-role">Hospital Admin · Bangalore</div></div>
        </div>
      </div>
      <div class="testi-card">
        <div class="testi-stars"><span>★</span><span>★</span><span>★</span><span>★</span><span>★</span></div>
        <div class="testi-text">I was skeptical at first but the donation camp near my area was perfectly organized. From registration to the actual donation took 25 minutes. Super professional. Will donate again!</div>
        <div class="testi-author">
          <div class="testi-avatar" style="background:rgba(230,126,34,.1);color:var(--amber)">AJ</div>
          <div><div class="testi-name">Anjali Joshi</div><div class="testi-role">First-time Donor · Pune</div></div>
        </div>
      </div>
      <div class="testi-card">
        <div class="testi-stars"><span>★</span><span>★</span><span>★</span><span>★</span><span>★</span></div>
        <div class="testi-text">After my accident, I received 4 units of O+ blood. The hospital told me it was sourced through LifeFlow in under an hour. I owe my life to the donors and this incredible system.</div>
        <div class="testi-author">
          <div class="testi-avatar" style="background:rgba(0,212,170,.1);color:var(--teal)">VS</div>
          <div><div class="testi-name">Vikram Singh</div><div class="testi-role">Accident Survivor · Chennai</div></div>
        </div>
      </div>
      <div class="testi-card">
        <div class="testi-stars"><span>★</span><span>★</span><span>★</span><span>★</span><span>★</span></div>
        <div class="testi-text">I've been donating every 3 months for 2 years. LifeFlow sends me reminders, tracks my history, and gives me a certificate each time. It makes giving back so easy and rewarding.</div>
        <div class="testi-author">
          <div class="testi-avatar" style="background:rgba(192,57,43,.1);color:var(--red-bright)">NR</div>
          <div><div class="testi-name">Neha Rao</div><div class="testi-role">Regular Donor · Hyderabad</div></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section">
  <div class="cta-inner">
    <div class="section-tag" style="margin-bottom:24px">Join Us Today</div>
    <h2>Ready to save a life?</h2>
    <p>Register as a donor today and become part of a community that saves thousands of lives every year. It takes 30 seconds to sign up.</p>
    <div class="cta-btns">
      <a href="../register.php" class="btn-hero btn-hero-primary">Register as Donor</a>
      <a href="../login.php" class="btn-hero btn-hero-secondary">Sign In</a>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="footer-inner">
    <div class="footer-top">
      <div class="footer-brand">
        <a href="home.php" class="nav-logo" style="margin-bottom:0"><div class="nav-drop"></div><span class="nav-name">LifeFlow</span></a>
        <p>India's most trusted blood bank management system, connecting donors and hospitals in real time to save lives.</p>
        <div class="about-badges" style="margin-top:20px">
          <span class="about-badge">🩸 48 Hospitals</span>
          <span class="about-badge">❤️ 1,200+ Donors</span>
          <span class="about-badge">✅ 3,800+ Saved</span>
        </div>
      </div>
      <div class="footer-col">
        <h4>Navigate</h4>
        <a href="home.php">Home</a>
        <a href="about.php">About Us</a>
        <a href="hospitals.php">Hospitals</a>
        <a href="donors.php">Donors</a>
      </div>
      <div class="footer-col">
        <h4>Portal</h4>
        <a href="../login.php">Login</a>
        <a href="../register.php">Register</a>
        <a href="../register.php">Donate Blood</a>
        <a href="../register.php">Hospital Signup</a>
      </div>
      <div class="footer-col" id="contact">
        <h4>Contact</h4>
        <a href="#">support@lifeflow.in</a>
        <a href="#">+91 98765 43210</a>
        <a href="#">Mumbai, Maharashtra</a>
        <a href="#">24/7 Emergency Line</a>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© 2025 LifeFlow Blood Bank. Made with <span>♥</span> to save lives.</p>
      <p style="color:var(--text3);font-size:12px">Built with PHP · MySQL · JavaScript</p>
    </div>
  </div>
</footer>

</body>
</html>
