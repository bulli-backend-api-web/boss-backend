<?php
// includes/header.php
$user = auth_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle ?? SITE_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/admin.css">
</head>
<body>

<div class="layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <span class="brand-icon"><i class="fa-solid fa-bolt"></i></span>
            <span class="brand-name"><?= SITE_NAME ?></span>
        </div>
        <nav class="sidebar-nav">
            <a href="<?= SITE_URL ?>/pages/dashboard.php" class="nav-item <?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>">
                <i class="fa-solid fa-chart-pie"></i> Dashboard
            </a>
            <a href="<?= SITE_URL ?>/pages/products.php" class="nav-item <?= ($activePage ?? '') === 'products' ? 'active' : '' ?>">
                <i class="fa-solid fa-box"></i> Products
            </a>
            <a href="<?= SITE_URL ?>/pages/api_tokens.php" class="nav-item <?= ($activePage ?? '') === 'api_tokens' ? 'active' : '' ?>">
                <i class="fa-solid fa-key"></i> API Tokens
            </a>
            <a href="<?= SITE_URL ?>/pages/users.php" class="nav-item <?= ($activePage ?? '') === 'users' ? 'active' : '' ?>">
                <i class="fa-solid fa-users"></i> Users
            </a>
        </nav>
        <div class="sidebar-footer">
            <span><?= h($user['username'] ?? '') ?></span>
            <a href="<?= SITE_URL ?>/logout.php" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main">
        <div class="topbar">
            <h1 class="page-title"><?= h($pageTitle ?? 'Dashboard') ?></h1>
        </div>
        <div class="content">
