<?php
$current = basename($_SERVER['PHP_SELF']);
function navItem($href, $label, $icon, $current) {
    $active = (basename($href) === $current) ? 'active' : '';
    return "<a href='$href' class='nav-item $active'>$icon<span>$label</span></a>";
}
$u = Auth::currentUser();
?>
<aside class="sidebar" id="sidebar">
  <a href="/bloodbank/public/home.php" class="sidebar-logo" style="text-decoration:none">
    <span class="logo-drop"></span>
    <span class="logo-text">LifeFlow</span>
  </div>
  <nav class="sidebar-nav">
    <span class="nav-section">Main</span>
    <?= navItem('dashboard.php','Dashboard','<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>',$current) ?>
    <?= navItem('donors.php','Donors','<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>',$current) ?>
    <?= navItem('inventory.php','Blood Inventory','<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2L8 7H4l2 13h12L20 7h-4L12 2z"/></svg>',$current) ?>
    <?= navItem('requests.php','Blood Requests','<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>',$current) ?>
    <span class="nav-section">Manage</span>
    <?= navItem('camps.php','Donation Camps','<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>',$current) ?>
    <?= navItem('hospitals.php','Hospitals','<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M12 8v8M8 12h8"/></svg>',$current) ?>
    <?= navItem('certificates.php','Certificates','<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>',$current) ?>
    <span class="nav-section">Analytics</span>
    <?= navItem('reports.php','Reports','<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>',$current) ?>
  </nav>
  <div class="sidebar-footer">
    <div class="user-pill">
      <div class="user-avatar"><?= strtoupper(substr($u['name'],0,2)) ?></div>
      <div class="user-info">
        <span class="user-name"><?= htmlspecialchars($u['name']) ?></span>
        <span class="user-role"><?= ucfirst(htmlspecialchars($u['role'])) ?></span>
      </div>
    </div>
    <a href="/bloodbank/logout.php" class="logout-btn" title="Logout">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
    </a>
  </div>
</aside>
