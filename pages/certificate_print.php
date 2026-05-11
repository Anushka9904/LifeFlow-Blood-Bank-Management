<?php
session_start();
require_once '../classes/Auth.php';
require_once '../config/db.php';
Auth::requireLogin();
$db = Database::getInstance();
$id = (int)($_GET['id'] ?? 0);

$stmt = $db->prepare("
    SELECT c.id, c.certificate_no, c.issued_at, c.donor_id,
           d.blood_group, d.city, d.total_donations,
           u.name as donor_name, u.email as donor_email
    FROM certificates c
    JOIN donors d ON c.donor_id = d.id
    JOIN users u ON d.user_id = u.id
    WHERE c.id = ?
");
$stmt->execute([$id]);
$cert = $stmt->fetch();
if (!$cert) { die('<p style="font-family:sans-serif;padding:40px;color:red">Certificate not found.</p>'); }

$dstmt = $db->prepare("SELECT * FROM donations WHERE id=(SELECT donation_id FROM certificates WHERE id=?)");
$dstmt->execute([$id]);
$donation = $dstmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Certificate <?= htmlspecialchars($cert['certificate_no']) ?> — LifeFlow</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:#f0ebe4;min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:32px 20px}
.cert-wrap{width:100%;max-width:820px}
.cert{background:#fff;border:3px solid #8B0000;border-radius:6px;overflow:hidden;box-shadow:0 12px 48px rgba(0,0,0,0.15)}
.cert-header{background:#8B0000;padding:28px 48px;display:flex;align-items:center;justify-content:space-between}
.logo{display:flex;align-items:center;gap:12px}
.logo-drop{width:26px;height:34px;background:#fff;border-radius:50% 50% 50% 50%/40% 40% 60% 60%;transform:rotate(-10deg);opacity:.92}
.logo-name{font-family:'Syne',sans-serif;font-size:22px;font-weight:800;color:#fff;letter-spacing:-.3px}
.cert-no-box{text-align:right;color:rgba(255,255,255,.8)}
.cert-no-label{font-size:12px;letter-spacing:.5px}
.cert-no-val{font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:#fff;display:block;margin-top:2px}
.cert-body{padding:48px}
.cert-eyebrow{font-size:11px;font-weight:600;letter-spacing:3px;text-transform:uppercase;color:#8B0000;margin-bottom:10px}
.cert-title{font-family:'Syne',sans-serif;font-size:34px;font-weight:800;color:#1a1a1a;line-height:1.1;margin-bottom:28px}
.cert-para{font-size:15px;color:#555;line-height:1.85;margin-bottom:30px}
.cert-para strong{color:#1a1a1a;font-weight:600}
.cert-details{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;background:#fdf7f4;border:1px solid #f0e0d8;border-radius:10px;padding:24px;margin:28px 0}
.detail{text-align:center}
.detail-label{font-size:10px;text-transform:uppercase;letter-spacing:1.2px;color:#999;display:block;margin-bottom:6px}
.detail-val{font-family:'Syne',sans-serif;font-size:20px;font-weight:700;color:#1a1a1a}
.cert-footer{display:flex;align-items:flex-end;justify-content:space-between;margin-top:48px;padding-top:24px;border-top:1px solid #eee}
.sign-block{text-align:center}
.sign-line{width:150px;height:1px;background:#ccc;margin:0 auto 8px}
.sign-label{font-size:12px;color:#888;line-height:1.5}
.cert-seal{width:80px;height:80px;border-radius:50%;border:2.5px solid #8B0000;display:flex;align-items:center;justify-content:center;text-align:center;color:#8B0000;font-size:9px;font-weight:700;letter-spacing:.8px;line-height:1.4;text-transform:uppercase;padding:8px}
.actions{margin-top:24px;display:flex;gap:12px;justify-content:center}
.btn-print{padding:13px 36px;background:#8B0000;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:500;font-family:'DM Sans',sans-serif;cursor:pointer;transition:.2s}
.btn-print:hover{background:#a00}
.btn-back{padding:13px 24px;background:transparent;color:#8B0000;border:2px solid #8B0000;border-radius:8px;font-size:15px;font-weight:500;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none;transition:.2s}
.btn-back:hover{background:#8B0000;color:#fff}
@media print{
  body{background:#fff;padding:0}
  .cert{box-shadow:none}
  .actions{display:none}
}
</style>
</head>
<body>
<div class="cert-wrap">
  <div class="cert">
    <div class="cert-header">
      <div class="logo">
        <div class="logo-drop"></div>
        <span class="logo-name">LifeFlow</span>
      </div>
      <div class="cert-no-box">
        <span class="cert-no-label">Certificate of Donation</span>
        <span class="cert-no-val"><?= htmlspecialchars($cert['certificate_no']) ?></span>
      </div>
    </div>
    <div class="cert-body">
      <div class="cert-eyebrow">Blood Donation Certificate</div>
      <div class="cert-title">Certificate of<br>Appreciation</div>
      <div class="cert-para">
        This is to certify that <strong><?= htmlspecialchars($cert['donor_name']) ?></strong>
        has voluntarily donated blood and made a noble contribution towards saving human lives.
        This act of generosity is deeply appreciated by LifeFlow Blood Bank Management System
        and the many patients whose lives have been impacted by this selfless deed.
      </div>
      <div class="cert-details">
        <div class="detail">
          <span class="detail-label">Blood Group</span>
          <span class="detail-val"><?= htmlspecialchars($cert['blood_group']) ?></span>
        </div>
        <div class="detail">
          <span class="detail-label">Units Donated</span>
          <span class="detail-val"><?= $donation ? number_format((float)$donation['units'],1) : '1.0' ?></span>
        </div>
        <div class="detail">
          <span class="detail-label">Donation Date</span>
          <span class="detail-val" style="font-size:15px"><?= $donation ? date('d M Y',strtotime($donation['donated_at'])) : date('d M Y',strtotime($cert['issued_at'])) ?></span>
        </div>
        <?php if($cert['city']): ?>
        <div class="detail">
          <span class="detail-label">City</span>
          <span class="detail-val" style="font-size:16px"><?= htmlspecialchars($cert['city']) ?></span>
        </div>
        <?php endif; ?>
        <div class="detail">
          <span class="detail-label">Total Donations</span>
          <span class="detail-val"><?= $cert['total_donations'] ?></span>
        </div>
        <div class="detail">
          <span class="detail-label">Issued On</span>
          <span class="detail-val" style="font-size:15px"><?= date('d M Y',strtotime($cert['issued_at'])) ?></span>
        </div>
      </div>
      <div class="cert-footer">
        <div class="sign-block">
          <div class="sign-line"></div>
          <div class="sign-label">Authorized Signatory<br>LifeFlow Blood Bank</div>
        </div>
        <div class="cert-seal">LifeFlow<br>Blood<br>Bank<br>✦</div>
        <div class="sign-block">
          <div class="sign-line"></div>
          <div class="sign-label">Donor Signature<br><?= htmlspecialchars($cert['donor_name']) ?></div>
        </div>
      </div>
    </div>
  </div>
  <div class="actions">
    <button class="btn-print" onclick="window.print()">Print / Save as PDF</button>
    <a href="certificates.php" class="btn-back">← Back to Certificates</a>
  </div>
</div>
</body>
</html>
