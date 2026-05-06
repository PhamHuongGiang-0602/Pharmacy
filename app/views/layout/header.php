<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? 'Nhà thuốc 1985 — Thuốc tốt từ tâm' ?></title>
  <meta name="description" content="Mua thuốc, vitamin, mỹ phẩm chính hãng. Giao nhanh 2 giờ. Tư vấn dược sĩ 24/7.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>public/css/main.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>public/css/premium.css">

  <?php if (isset($extraCss)): foreach ($extraCss as $css): ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($css) ?>">
  <?php endforeach; endif; ?>
</head>
<body>

<!-- ===== TOP BAR ===== -->
<div class="topbar">
  <div class="container topbar-inner">
    <div class="topbar-left">
      <a href="<?= BASE_URL ?>"><span>📞</span> 1800 599 921 (Miễn phí)</a>
      <a href="<?= BASE_URL ?>consult"><span>💬</span> Tư vấn dược sĩ</a>
    </div>
    <div class="topbar-right">
      <a href="<?= BASE_URL ?>blog">Góc sức khỏe</a>
      <a href="<?= BASE_URL ?>stores">Hệ thống cửa hàng</a>
      <a href="<?= BASE_URL ?>about">Về chúng tôi</a>
      <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>
        <a href="<?= BASE_URL ?>admin" class="topbar-admin">⚙️ Quản trị Admin</a>
        <a href="<?= BASE_URL ?>admin/settings" class="topbar-admin" style="margin-left: 15px;">🔧 Cấu hình</a>
      <?php elseif (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2): ?>
        <a href="<?= BASE_URL ?>doctor/dashboard" class="topbar-admin">💊 Trang Dược sĩ</a>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- ===== MAIN HEADER ===== -->
<header class="main-header" id="mainHeader">
  <div class="container header-inner">

    <!-- Logo -->
    <a href="<?= BASE_URL ?>" class="logo">
      <div class="logo-icon">
        <img style="width: 70px; height: 70px;" src="<?= BASE_URL ?>public/img/logo.jpg" alt="Logo">
      </div>
      <div class="logo-text">
        <span class="logo-name">Nhà thuốc 1985</span>
        <span class="logo-tagline">Thuốc tốt từ tâm</span>
      </div>
    </a>

    <!-- Search Bar -->
    <form class="search-bar" action="<?= BASE_URL ?>products" method="GET">
      <div class="search-wrap">
        <input
          type="text"
          name="q"
          placeholder="Tìm thuốc, vitamin, mỹ phẩm..."
          class="search-input"
          autocomplete="off"
          id="globalSearch"
          value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
        >
        <div class="search-suggestions" id="searchSuggestions"></div>
      </div>
      <button type="submit" class="search-btn">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        Tìm kiếm
      </button>
    </form>

    <!-- Header Right -->
    <div class="header-right">

      <!-- Tài khoản -->
      <div class="header-action header-account" id="accountDropdown" style="border-left: 1px solid var(--border); padding-left: 20px;">
        <span class="action-icon">👤</span>
        <div class="action-text">
          <?php if (isset($_SESSION['user'])): ?>
            <small>Xin chào</small>
            <strong><?= htmlspecialchars(explode(' ', $_SESSION['user']['name'])[0]) ?></strong>
          <?php else: ?>
            <small>Đăng nhập</small>
            <strong>Tài khoản</strong>
          <?php endif; ?>
        </div>
        <div class="dropdown-menu account-menu">
          <?php if (isset($_SESSION['user'])): ?>
            <a href="<?= BASE_URL ?>account">Thông tin tài khoản</a>
            <a href="<?= BASE_URL ?>account/orders">Đơn hàng của tôi</a>
            <?php if (in_array($_SESSION['role_id'], [1, 2, 3])): ?>
                <hr>
                <a href="<?= BASE_URL ?>doctor/dashboard" style="color: var(--green); font-weight: bold;">👨‍⚕️ Trang chuyên môn</a>
            <?php endif; ?>
            <hr>
            <a href="<?= BASE_URL ?>auth/logout" class="logout-link">Đăng xuất</a>
          <?php else: ?>
            <a href="<?= BASE_URL ?>auth/login">Đăng nhập</a>
            <a href="<?= BASE_URL ?>auth/register">Đăng ký</a>
          <?php endif; ?>
        </div>
      </div>

      <!-- Giỏ hàng -->
      <a href="<?= BASE_URL ?>cart" class="header-action header-cart">
        <span class="action-icon cart-icon-wrap">
          🛒
          <span class="cart-count" id="cartCount">
            <?php
              $cartItemCount = 0;
              $cartTotalAmount = 0;
              if (isset($_SESSION['user_id'])) {
                  require_once __DIR__ . '/../../Models/CartModel.php';
                  $cartModelHeader = new CartModel();
                  $cartId = $cartModelHeader->getCartByUserId($_SESSION['user_id']);
                  if ($cartId) {
                      $headerCartItems = $cartModelHeader->getCartItems($cartId);
                      $cartItemCount = count($headerCartItems);
                      foreach ($headerCartItems as $item) {
                          $cartTotalAmount += $item['current_price'] * $item['quantity'];
                      }
                  } else {
                      // Session user stale
                      unset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['full_name'], $_SESSION['role_id'], $_SESSION['user']);
                  }
              } else if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                  require_once __DIR__ . '/../../Models/ProductModel.php';
                  $productModelHeader = new ProductModel();
                  $cartItemCount = count($_SESSION['cart']);
                  foreach ($_SESSION['cart'] as $pid => $qty) {
                      $p = $productModelHeader->getProductById($pid);
                      if ($p) {
                          $cartTotalAmount += $p['current_price'] * $qty;
                      }
                  }
              }
              echo $cartItemCount;
            ?>
          </span>
        </span>
        <div class="action-text">
          <small>Giỏ hàng</small>
          <strong id="cartTotal">
            <?= $cartTotalAmount > 0 ? number_format($cartTotalAmount) . 'đ' : '0đ' ?>
          </strong>
        </div>
      </a>

    </div>
  </div>

  <!-- ===== NAV MENU ===== -->
  <nav class="main-nav" id="mainNav">
    <div class="container nav-inner">

      <!-- Mega menu trigger -->
      <div class="nav-item nav-mega-trigger" id="megaTrigger">
        <span class="nav-icon">☰</span> Danh mục
        <div class="mega-menu" id="megaMenu">
          <div class="mega-grid">
            <div class="mega-col">
              <h4>Thuốc & Y tế</h4>
              <a href="<?= BASE_URL ?>products?category=thuoc-ke-don">Thuốc kê đơn</a>
              <a href="<?= BASE_URL ?>products?category=thuoc-otc">Thuốc không kê đơn</a>
              <a href="<?= BASE_URL ?>products?category=5">Thiết bị y tế</a>
              <a href="<?= BASE_URL ?>products?category=5">Băng gạc & Sơ cứu</a>
            </div>
            <div class="mega-col">
              <h4>Dinh dưỡng</h4>
              <a href="<?= BASE_URL ?>products?category=8">Vitamin & Khoáng chất</a>
              <a href="<?= BASE_URL ?>products?category=3">Omega-3 & DHA</a>
              <a href="<?= BASE_URL ?>products?category=3">Probiotic</a>
              <a href="<?= BASE_URL ?>products?category=3">Protein & Thể thao</a>
            </div>
            <div class="mega-col">
              <h4>Làm đẹp & Chăm sóc</h4>
              <a href="<?= BASE_URL ?>products?category=4">Chăm sóc da mặt</a>
              <a href="<?= BASE_URL ?>products?category=4">Chống nắng</a>
              <a href="<?= BASE_URL ?>products?category=4">Chăm sóc tóc</a>
              <a href="<?= BASE_URL ?>products?category=4">Vệ sinh cá nhân</a>
            </div>
            <div class="mega-col">
              <h4>Mẹ & Bé</h4>
              <a href="<?= BASE_URL ?>products?category=3">Sữa công thức</a>
              <a href="<?= BASE_URL ?>products?category=3">Đồ dùng trẻ em</a>
              <a href="<?= BASE_URL ?>products?category=3">Sản phẩm mẹ bầu</a>
              <a href="<?= BASE_URL ?>products?category=5">Vaccine & Tiêm chủng</a>
            </div>
          </div>
        </div>
      </div>

      <a href="<?= BASE_URL ?>" class="nav-item <?= ($currentPage ?? '') === 'home' ? 'active' : '' ?>">Trang chủ</a>
      <a href="<?= BASE_URL ?>products?sale=1" class="nav-item nav-sale">🔥 Khuyến mãi</a>
      <a href="<?= BASE_URL ?>products?category=8" class="nav-item">Vitamin</a>
      <a href="<?= BASE_URL ?>products?category=4" class="nav-item">Chăm sóc da</a>
      <a href="<?= BASE_URL ?>products?category=3" class="nav-item">Mẹ & Bé</a>
      <a href="<?= BASE_URL ?>blog" class="nav-item">Góc sức khỏe</a>
      <a href="<?= BASE_URL ?>stores" class="nav-item">Cửa hàng</a>

    </div>
  </nav>

  <!-- ===== ADMIN SUB-HEADER (Visible only to Admin/Pharmacist) ===== -->
  <!-- ===== ADMIN/PROFESSIONAL BAR ===== -->
  <?php if (isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [1, 2, 3])): ?>
    <div style="background: #1e293b; color: #f8fafc; padding: 10px 0; border-bottom: 2px solid var(--green);">
      <div class="container" style="display: flex; gap: 20px; align-items: center; flex-wrap: nowrap; overflow-x: auto;">
        <span style="display: flex; align-items: center; gap: 8px; font-weight: 800; color: var(--green); font-size: 0.8rem; letter-spacing: 0.05em; text-transform: uppercase; white-space: nowrap;">
           <span style="font-size: 1.1rem;">🛠️</span> Bảng điều khiển
        </span>
        
        <div style="height: 20px; width: 1px; background: rgba(255,255,255,0.1);"></div>
        
        <a href="<?= BASE_URL ?>doctor/dashboard" class="admin-link">📋 Duyệt đơn thuốc</a>
        
        <?php if ($_SESSION['role_id'] == 1): // Admin only tools ?>
            <a href="<?= BASE_URL ?>admin/dashboard" class="admin-link">📊 Thống kê</a>
            <a href="<?= BASE_URL ?>admin/product" class="admin-link">💊 Sản phẩm</a>
            <a href="<?= BASE_URL ?>admin/inventory" class="admin-link">📦 Kho hàng</a>
            <a href="<?= BASE_URL ?>admin/order" class="admin-link">🧾 Đơn hàng</a>
            <a href="<?= BASE_URL ?>admin/users" class="admin-link">👤 Người dùng</a>
            <a href="<?= BASE_URL ?>admin/settings" class="admin-link">⚙️ Cấu hình</a>
            <a href="<?= BASE_URL ?>admin/chatbot" class="admin-link">🤖 Trợ lý AI</a>
            <a href="<?= BASE_URL ?>admin/emaillog" class="admin-link">📧 Gửi mail</a>
        <?php endif; ?>
      </div>
    </div>
    <style>
      .admin-link {
          text-decoration: none;
          color: #cbd5e1;
          font-size: 0.85rem;
          font-weight: 600;
          white-space: nowrap;
          transition: all 0.2s;
          display: flex;
          align-items: center;
          gap: 6px;
          padding: 5px 10px;
          border-radius: 8px;
      }
      .admin-link:hover {
          color: #fff;
          background: rgba(255,255,255,0.05);
      }
    </style>
  <?php endif; ?>
</header>

<!-- Mobile overlay -->
<div class="overlay" id="overlay"></div>

<main id="pageMain">