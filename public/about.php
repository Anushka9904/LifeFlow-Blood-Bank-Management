<?php
require_once '../config/db.php';
$db = Database::getInstance();
$statDonors    = (int)$db->query("SELECT COUNT(*) FROM donors")->fetchColumn();
$statHospitals = (int)$db->query("SELECT COUNT(*) FROM users WHERE role='hospital'")->fetchColumn();
$statDonations = (int)$db->query("SELECT COUNT(*) FROM donations")->fetchColumn();
$statCamps     = (int)$db->query("SELECT COUNT(*) FROM donation_camps")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>About Us — LifeFlow</title>
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
body{font-family:var(--font-b);background:var(--dark2);color:var(--text);-webkit-font-smoothing:antialiased}
nav{position:fixed;top:0;left:0;right:0;z-index:100;background:rgba(255,255,255,0.92);backdrop-filter:blur(12px);border-bottom:1px solid var(--border);padding:0 5%;box-shadow:0 1px 8px rgba(0,0,0,0.06)}
.nav-inner{max-width:1200px;margin:0 auto;height:68px;display:flex;align-items:center;justify-content:space-between}
.nav-logo{display:flex;align-items:center;gap:10px;text-decoration:none}
.nav-drop{width:22px;height:28px;background:var(--red);border-radius:50% 50% 50% 50%/40% 40% 60% 60%;transform:rotate(-10deg)}
.nav-name{font-family:var(--font-d);font-size:20px;font-weight:800;color:var(--text);letter-spacing:-.5px}
.nav-links{display:flex;align-items:center;gap:32px}
.nav-links a{color:var(--text2);text-decoration:none;font-size:14px;font-weight:500;transition:.2s}
.nav-links a:hover,.nav-links a.active{color:var(--red)}
.nav-btns{display:flex;gap:12px}
.btn-outline{padding:9px 20px;border:1px solid var(--border);border-radius:8px;color:var(--text2);text-decoration:none;font-size:14px;font-weight:500;transition:.2s}
.btn-outline:hover{border-color:var(--red);color:var(--red)}
.btn-solid{padding:9px 20px;background:var(--red);border:none;border-radius:8px;color:#fff;text-decoration:none;font-size:14px;font-weight:500;transition:.2s}
.btn-solid:hover{background:var(--red-bright)}
section{padding:80px 5%}
.section-inner{max-width:1200px;margin:0 auto}
.section-tag{display:inline-block;padding:5px 14px;background:var(--red-soft);border:1px solid rgba(192,57,43,.2);border-radius:999px;font-size:11px;font-weight:600;color:var(--red-bright);letter-spacing:1px;text-transform:uppercase;margin-bottom:16px}
.section-title{font-family:var(--font-d);font-size:clamp(32px,4vw,48px);font-weight:800;letter-spacing:-1.5px;margin-bottom:16px;line-height:1.1;color:var(--text)}
.section-sub{font-size:16px;color:var(--text2);line-height:1.7;max-width:560px}
.page-hero{padding:160px 5% 80px;background:var(--dark2);border-bottom:1px solid var(--border);text-align:center}
.page-hero h1{font-family:var(--font-d);font-size:clamp(42px,5vw,64px);font-weight:800;letter-spacing:-2px;margin-bottom:20px;color:var(--text)}
.page-hero h1 span{color:var(--red)}
.page-hero p{font-size:18px;color:var(--text2);max-width:560px;margin:0 auto;line-height:1.7}
.mission-grid{display:grid;grid-template-columns:1fr 1fr;gap:80px;align-items:center;margin-top:60px}
.mission-text h2{font-family:var(--font-d);font-size:36px;font-weight:800;letter-spacing:-1px;margin-bottom:16px;color:var(--text)}
.mission-text p{font-size:15px;color:var(--text2);line-height:1.8;margin-bottom:16px}
.mission-visual{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.mission-card{background:#fff;border:1px solid var(--border);border-radius:16px;padding:24px;text-align:center;box-shadow:0 1px 3px rgba(0,0,0,0.06)}
.mission-card .icon{font-size:32px;margin-bottom:12px;display:block}
.mission-card h3{font-family:var(--font-d);font-size:28px;font-weight:800;margin-bottom:4px;color:var(--red)}
.mission-card p{font-size:12px;color:var(--text3)}
.values-bg{background:var(--dark2);border-top:1px solid var(--border);border-bottom:1px solid var(--border)}
.values-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-top:56px}
.value-card{background:#fff;border:1px solid var(--border);border-radius:16px;padding:32px;box-shadow:0 1px 3px rgba(0,0,0,0.06)}
.value-icon{width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:20px}
.value-card h3{font-family:var(--font-d);font-size:18px;font-weight:700;margin-bottom:10px;color:var(--text)}
.value-card p{font-size:14px;color:var(--text2);line-height:1.7}
.team-grid{
  display:grid;
  grid-template-columns:repeat(4,1fr);
  gap:20px;
  margin-top:56px
}
.team-card{background:#fff;border:1px solid var(--border);border-radius:16px;padding:28px;text-align:center;box-shadow:0 1px 3px rgba(0,0,0,0.06);transition:.2s}
.team-card:hover{box-shadow:0 4px 16px rgba(0,0,0,0.08);border-color:#cbd5e1}
.team-avatar{width:72px;height:72px;border-radius:50%;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;font-family:var(--font-d);font-size:24px;font-weight:800}
.team-name{font-family:var(--font-d);font-size:16px;font-weight:700;margin-bottom:4px;color:var(--text)}
.team-role{font-size:13px;color:var(--text3);margin-bottom:12px}
.team-bio{font-size:13px;color:var(--text2);line-height:1.6}
.team-grid{
  display:grid;
  grid-template-columns:repeat(4,1fr);
  gap:20px;
  margin-top:56px
}
.tech-card{background:#fff;border:1px solid var(--border);border-radius:12px;padding:24px;text-align:center;transition:.2s;box-shadow:0 1px 3px rgba(0,0,0,0.06)}
.tech-card:hover{border-color:var(--red);box-shadow:0 4px 16px rgba(0,0,0,0.08)}
.tech-icon{font-size:36px;margin-bottom:12px;display:block}
.tech-name{font-family:var(--font-d);font-size:15px;font-weight:700;margin-bottom:4px;color:var(--text)}
.tech-desc{font-size:12px;color:var(--text3)}
.cta-section{background:var(--red);text-align:center;padding:100px 5%}
.cta-inner{max-width:600px;margin:0 auto}
.cta-inner h2{font-family:var(--font-d);font-size:clamp(32px,4vw,48px);font-weight:800;letter-spacing:-1.5px;margin-bottom:20px;color:#fff}
.cta-inner p{font-size:16px;color:rgba(255,255,255,.85);line-height:1.7;margin-bottom:40px}
.cta-btns{display:flex;gap:14px;justify-content:center;flex-wrap:wrap}
.btn-hero{padding:14px 28px;border-radius:10px;font-size:15px;font-weight:600;text-decoration:none;transition:.2s}
.btn-hero-primary{background:#fff;color:var(--red)}
.btn-hero-primary:hover{background:rgba(255,255,255,.9)}
.btn-hero-secondary{background:transparent;color:#fff;border:2px solid rgba(255,255,255,.4)}
.btn-hero-secondary:hover{border-color:#fff}
footer{background:#fff;border-top:1px solid var(--border);padding:32px 5%}
.footer-bottom{max-width:1200px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
.footer-bottom p{font-size:13px;color:var(--text3)}
.footer-bottom span{color:var(--red-bright)}
@media(max-width:900px){.mission-grid{grid-template-columns:1fr}.values-grid{grid-template-columns:1fr 1fr}.team-grid{grid-template-columns:1fr 1fr!important}.tech-grid{grid-template-columns:1fr 1fr}.nav-links{display:none}}
@media(max-width:600px){.values-grid{grid-template-columns:1fr}.team-grid{grid-template-columns:1fr!important}.tech-grid{grid-template-columns:1fr 1fr}.mission-visual{grid-template-columns:1fr 1fr}}
</style>
</head>
<body>

<nav>
  <div class="nav-inner">
    <a href="home.php" class="nav-logo"><div class="nav-drop"></div><span class="nav-name">LifeFlow</span></a>
    <div class="nav-links">
      <a href="home.php">Home</a>
      <a href="about.php" class="active">About</a>
      <a href="hospitals.php">Hospitals</a>
      <a href="donors.php">Donors</a>
    </div>
    <div class="nav-btns">
      <a href="../login.php" class="btn-outline">Login</a>
      <a href="../register.php" class="btn-solid">Register</a>
    </div>
  </div>
</nav>

<div class="page-hero">
  <div class="section-tag">Our Story</div>
  <h1>We exist to <span>save lives.</span></h1>
  <p>LifeFlow was built with one mission — make blood available to anyone who needs it, anywhere in India, at any time.</p>
</div>

<section>
  <div class="section-inner">
    <div class="mission-grid">
      <div class="mission-text">
        <div class="section-tag">Our Mission</div>
        <h2>Bridging the gap between donors and patients</h2>
        <p>Every 2 seconds, someone in India needs blood. Yet blood banks often face shortages while willing donors go unnotified. LifeFlow was created to solve this disconnect.</p>
        <p>We built a real-time system that connects donors, hospitals, and blood banks — making the entire process transparent, fast, and efficient. From registration to donation to certification, everything happens in one place.</p>
        <p style="color:var(--teal);font-weight:500">Our goal: Zero blood shortage across India by 2030.</p>
      </div>
      <div class="mission-visual">
        <div class="mission-card"><span class="icon">🩸</span><h3><?= $statDonors ?></h3><p>Registered Donors</p></div>
        <div class="mission-card"><span class="icon">🏥</span><h3><?= $statHospitals ?></h3><p>Hospitals Enrolled</p></div>
        <div class="mission-card"><span class="icon">❤️</span><h3><?= $statDonations ?></h3><p>Lives Saved</p></div>
        <div class="mission-card"><span class="icon">🏕️</span><h3><?= $statCamps ?></h3><p>Camps Organized</p></div>
      </div>
    </div>
  </div>
</section>

<section class="values-bg">
  <div class="section-inner">
    <div class="section-tag">Our Values</div>
    <h2 class="section-title">What drives us</h2>
    <div class="values-grid">
      <div class="value-card">
        <div class="value-icon" style="background:var(--red-soft);color:var(--red-bright)"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></div>
        <h3>Life First</h3>
        <p>Every decision we make is guided by one question — does this save more lives? Speed, accuracy, and reliability are non-negotiable when lives are on the line.</p>
      </div>
      <div class="value-card">
        <div class="value-icon" style="background:rgba(5,150,105,.1);color:var(--teal)"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
        <h3>Trust & Safety</h3>
        <p>Donor data is protected. Hospital requests are verified. Every unit of blood is tracked from collection to delivery with full transparency and accountability.</p>
      </div>
      <div class="value-card">
        <div class="value-icon" style="background:rgba(37,99,235,.1);color:#2563eb"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></div>
        <h3>Accessibility</h3>
        <p>Blood should never be unavailable because of location or logistics. We work to make blood accessible across urban and rural areas alike.</p>
      </div>
    </div>
  </div>
</section>

<section>
  <div class="section-inner">
    <div class="section-tag">The Team</div>
    <h2 class="section-title">Built by students, for humanity</h2>
    <p class="section-sub">LifeFlow was developed as an academic project with real-world impact in mind.</p>
    <div class="team-grid">
      <div class="team-card">
        <div class="team-avatar" style="background:var(--red-soft);color:var(--red-bright)">AS</div>
        <div class="team-name">Anushka Abhijit Shinde</div>
        <div class="team-role">Full-Stack Developer</div>
        <div class="team-bio">Led the end-to-end development of LifeFlow — from PHP backend and MySQL database design to frontend UI, API integrations, and overall system architecture.</div>
      </div>
      <div class="team-card">
        <div class="team-avatar" style="background:rgba(5,150,105,.1);color:var(--teal)">PP</div>
        <div class="team-name">Payal Vikas Patil</div>
        <div class="team-role">Software Developer</div>
        <div class="team-bio">Contributed to module development, testing, and implementation of core features including donor management and blood request workflows.</div>
      </div>
      <div class="team-card">
        <div class="team-avatar" style="background:rgba(37,99,235,.1);color:#2563eb">VB</div>
        <div class="team-name">Vaishnavi Santosh Borse</div>
        <div class="team-role">Software Developer</div>
        <div class="team-bio">Worked on system functionality, database operations, and frontend implementation across multiple modules of the application.</div>
      </div>
      <div class="team-card">
  <div class="team-avatar" style="background:rgba(168,85,247,.1);color:#a855f7">PP</div>
  <div class="team-name">Pinak Pradip Pawar</div>
  <div class="team-role">Software Developer</div>
  <div class="team-bio">Assisted in data preparation, setup.sql verification, testing of registration flow, and report formatting.</div>
</div>
    </div>
  </div>
</section>

<section style="background:var(--dark2);border-top:1px solid var(--border);border-bottom:1px solid var(--border)">
  <div class="section-inner">
    <div class="section-tag">Tech Stack</div>
    <h2 class="section-title">Built with modern technology</h2>
 <div class="tech-grid">

  <div class="tech-card">
    <span class="tech-icon">🐘</span>
    <div class="tech-name">PHP</div>
    <div class="tech-desc">Backend development</div>
  </div>

  <div class="tech-card">
    <span class="tech-icon">🗄️</span>
    <div class="tech-name">MySQL</div>
    <div class="tech-desc">Database management</div>
  </div>

  <div class="tech-card">
    <span class="tech-icon">🌐</span>
    <div class="tech-name">HTML5</div>
    <div class="tech-desc">Page structure</div>
  </div>

  <div class="tech-card">
    <span class="tech-icon">🎨</span>
    <div class="tech-name">CSS3</div>
    <div class="tech-desc">UI design & responsiveness</div>
  </div>

  <div class="tech-card">
    <span class="tech-icon">⚡</span>
    <div class="tech-name">JavaScript</div>
    <div class="tech-desc">Frontend interactivity</div>
  </div>

  <div class="tech-card">
    <span class="tech-icon">📊</span>
    <div class="tech-name">Chart.js</div>
    <div class="tech-desc">Reports & visualization</div>
  </div>

  <div class="tech-card">
    <span class="tech-icon">🔤</span>
    <div class="tech-name">Google Fonts</div>
    <div class="tech-desc">Typography</div>
  </div>

  <div class="tech-card">
    <span class="tech-icon">🖥️</span>
    <div class="tech-name">Apache (XAMPP)</div>
    <div class="tech-desc">Local server</div>
  </div>

</div>
</section>

<div class="cta-section">
  <div class="cta-inner">
    <h2>Be part of the solution.</h2>
    <p>Join donors and hospitals already using LifeFlow. Registration is free and takes less than a minute.</p>
    <div class="cta-btns">
      <a href="../register.php" class="btn-hero btn-hero-primary">Register Now</a>
      <a href="home.php" class="btn-hero btn-hero-secondary">Back to Home</a>
    </div>
  </div>
</div>

<footer>
  <div class="footer-bottom">
    <p>© 2025 LifeFlow Blood Bank. Made with <span>♥</span> to save lives.</p>
    <p>Built with PHP · MySQL · JavaScript</p>
  </div>
</footer>
</body>
</html>