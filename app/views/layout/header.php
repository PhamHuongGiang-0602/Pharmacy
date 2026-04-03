<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? 'Long Châu — Nhà thuốc online uy tín' ?></title>
  <meta name="description" content="Mua thuốc, vitamin, mỹ phẩm chính hãng. Giao nhanh 2 giờ. Tư vấn dược sĩ 24/7.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="public/css/main.css">
  <?php if (isset($extraCss)): foreach ($extraCss as $css): ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($css) ?>">
  <?php endforeach; endif; ?>
</head>
<body>

<!-- ===== TOP BAR ===== -->
<div class="topbar">
  <div class="container topbar-inner">
    <div class="topbar-left">
      <a href="/"><span>📞</span> 1800 599 921 (Miễn phí)</a>
      <a href="/consult"><span>💬</span> Tư vấn dược sĩ</a>
    </div>
    <div class="topbar-right">
      <a href="/blog">Góc sức khỏe</a>
      <a href="/stores">Hệ thống cửa hàng</a>
      <a href="/about">Về chúng tôi</a>
      <?php if (isset($_SESSION['admin'])): ?>
        <a href="/admin" class="topbar-admin">⚙️ Quản trị</a>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- ===== MAIN HEADER ===== -->
<header class="main-header" id="mainHeader">
  <div class="container header-inner">

    <!-- Logo -->
    <a href="/" class="logo">
      <div class="logo-icon">
        <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect width="36" height="36" rx="8" fill="#00904a"/>
          <path d="M18 8v20M8 18h20" stroke="#fff" stroke-width="4" stroke-linecap="round"/>
        </svg>
      </div>
      <div class="logo-text">
        <span class="logo-name">Long Châu</span>
        <span class="logo-tagline">Nhà thuốc tin cậy</span>
      </div>
    </a>

    <!-- Search Bar -->
    <form class="search-bar" action="/products" method="GET">
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

      <!-- Upload đơn thuốc -->
      <a href="/prescription/upload" class="header-action header-rx">
        <span class="action-icon">📋</span>
        <div class="action-text">
          <small>Upload</small>
          <strong>Đơn thuốc</strong>
        </div>
      </a>

      <!-- Tài khoản -->
      <div class="header-action header-account" id="accountDropdown">
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
            <a href="/account">Thông tin tài khoản</a>
            <a href="/account/orders">Đơn hàng của tôi</a>
            <a href="/account/prescriptions">Đơn thuốc đã lưu</a>
            <hr>
            <a href="/auth/logout" class="logout-link">Đăng xuất</a>
          <?php else: ?>
            <a href="/auth/login">Đăng nhập</a>
            <a href="/auth/register">Đăng ký</a>
          <?php endif; ?>
        </div>
      </div>

      <!-- Giỏ hàng -->
      <a href="/cart" class="header-action header-cart">
        <span class="action-icon cart-icon-wrap">
          🛒
          <span class="cart-count" id="cartCount">
            <?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>
          </span>
        </span>
        <div class="action-text">
          <small>Giỏ hàng</small>
          <strong id="cartTotal">
            <?php
              $total = 0;
              if (isset($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $item) $total += $item['price'] * $item['qty'];
              }
              echo $total > 0 ? number_format($total) . 'đ' : '0đ';
            ?>
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
              <a href="/products?category=thuoc-ke-don">Thuốc kê đơn</a>
              <a href="/products?category=thuoc-otc">Thuốc không kê đơn</a>
              <a href="/products?category=thiet-bi-y-te">Thiết bị y tế</a>
              <a href="/products?category=bang-gau">Băng gạc & Sơ cứu</a>
            </div>
            <div class="mega-col">
              <h4>Dinh dưỡng</h4>
              <a href="/products?category=vitamin">Vitamin & Khoáng chất</a>
              <a href="/products?category=omega3">Omega-3 & DHA</a>
              <a href="/products?category=probiotic">Probiotic</a>
              <a href="/products?category=protein">Protein & Thể thao</a>
            </div>
            <div class="mega-col">
              <h4>Làm đẹp & Chăm sóc</h4>
              <a href="/products?category=cham-soc-da">Chăm sóc da mặt</a>
              <a href="/products?category=chong-nang">Chống nắng</a>
              <a href="/products?category=cham-soc-toc">Chăm sóc tóc</a>
              <a href="/products?category=ve-sinh">Vệ sinh cá nhân</a>
            </div>
            <div class="mega-col">
              <h4>Mẹ & Bé</h4>
              <a href="/products?category=sua-bot">Sữa công thức</a>
              <a href="/products?category=tap-hoa-tre-em">Đồ dùng trẻ em</a>
              <a href="/products?category=san-pham-me-bau">Sản phẩm mẹ bầu</a>
              <a href="/products?category=vaccine">Vaccine & Tiêm chủng</a>
            </div>
          </div>
        </div>
      </div>

      <a href="/" class="nav-item <?= ($currentPage ?? '') === 'home' ? 'active' : '' ?>">Trang chủ</a>
      <a href="/products?sale=1" class="nav-item nav-sale">🔥 Khuyến mãi</a>
      <a href="/products?category=vitamin" class="nav-item">Vitamin</a>
      <a href="/products?category=cham-soc-da" class="nav-item">Chăm sóc da</a>
      <a href="/products?category=me-va-be" class="nav-item">Mẹ & Bé</a>
      <a href="/blog" class="nav-item">Góc sức khỏe</a>
      <a href="/stores" class="nav-item">Cửa hàng</a>

    </div>
  </nav>
</header>

<!-- Mobile overlay -->
<div class="overlay" id="overlay"></div>

<main id="pageMain">