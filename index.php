<?php
session_start();
require_once __DIR__.'/classes/Auth.php';
if (Auth::isLoggedIn()) {
    $user = Auth::currentUser();
    switch ($user['role']) {
        case 'admin':    header('Location: /bloodbank/pages/dashboard.php'); break;
        case 'hospital': header('Location: /bloodbank/pages/hospital_portal.php'); break;
        case 'donor':    header('Location: /bloodbank/pages/donor_portal.php'); break;
        default:         header('Location: /bloodbank/public/home.php');
    }
} else {
    header('Location: /bloodbank/public/home.php');
}
exit;
