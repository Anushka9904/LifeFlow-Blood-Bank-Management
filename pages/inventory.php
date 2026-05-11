<?php
session_start();
require_once '../classes/Auth.php';
require_once '../classes/BloodInventory.php';
Auth::requireAdmin();
$user = Auth::currentUser();
$inv  = new BloodInventory();
$msg  = ''; $msgType = '';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $bg    = $_POST['blood_group'] ?? '';
    $units = (int)($_POST['units'] ?? 0);
    $action= $_POST['action'] ?? '';
    if ($units <= 0) { $msg='Units must be greater than 0.'; $msgType='error'; }
    elseif ($action==='add')    { $r=$inv->addUnits($bg,$units);    $msg=$r['message']; $msgType=$r['success']?'success':'error'; }
    elseif ($action==='deduct') { $r=$inv->deductUnits($bg,$units); $msg=$r['message']; $msgType=$r['success']?'success':'error'; }
    elseif ($action==='set')    { $r=$inv->setUnits($bg,$units);    $msg=$r['message']; $msgType=$r['success']?'success':'error'; }
}

$inventory = $inv->getAll();
$critical  = $inv->getCritical();
$total     = $inv->getTotalUnits();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Blood Inventory — LifeFlow</title>
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
      <div><h1 class="page-title">Blood Inventory</h1><p class="page-sub">Monitor and manage blood stock</p></div>
    </div>
    <div class="topbar-right"><div class="topbar-date">Total: <strong><?= $total ?> units</strong></div></div>
  </header>
  <div class="content">
    <?php if($msg): ?><div class="alert alert-<?= $msgType==='success'?'success':'error' ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if(!empty($critical)): ?>
    <div class="alert alert-error">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
      Critical stock: <?= implode(', ', array_column($critical,'blood_group')) ?> — needs immediate attention!
    </div>
    <?php endif; ?>
    <div class="inv-grid">
      <?php foreach($inventory as $item):
        $pct = min(100, ($item['units'] / max(1, $item['critical_level']*3)) * 100);
        $cls = $item['units'] <= $item['critical_level'] ? 'crit' : ($item['units'] <= $item['critical_level']*1.5 ? 'warn' : 'ok');
      ?>
      <div class="inv-card <?= $cls ?>">
        <div class="inv-card-top">
          <span class="inv-group"><?= $item['blood_group'] ?></span>
          <span class="inv-status-dot <?= $cls ?>"></span>
        </div>
        <div class="inv-units-big"><?= $item['units'] ?><span>units</span></div>
        <div class="inv-bar-wrap" style="margin:12px 0 4px"><div class="inv-bar <?= $cls ?>" style="width:<?= round($pct) ?>%"></div></div>
        <div class="inv-critical-label">Critical level: <?= $item['critical_level'] ?> units</div>
        <div class="inv-actions" style="margin-top:14px">
          <form method="POST" style="display:flex;gap:6px;align-items:center;flex-wrap:wrap">
            <input type="hidden" name="blood_group" value="<?= $item['blood_group'] ?>">
            <input type="number" name="units" min="1" max="999" value="1" style="width:60px;padding:6px 8px;font-size:13px">
            <button type="submit" name="action" value="add" class="btn-sm btn-green">+ Add</button>
            <button type="submit" name="action" value="deduct" class="btn-sm btn-red">− Use</button>
          </form>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
</body></html>
