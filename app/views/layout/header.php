<?php
/**
 * header.php — Layout component chung
 *
 * Biến PHP dùng trong file này (truyền từ controller hoặc session):
 *   $page_title      string  — tiêu đề trang (hiển thị trên <title>)
 *   $page_desc       string  — meta description
 *   $extra_css       array   — các file CSS bổ sung ['css/product.css']
 *   $_SESSION['user'] array  — thông tin user đang đăng nhập
 *   $cart_count      int     — số lượng sản phẩm trong giỏ (từ CartModel)
 *   $categories      array   — danh mục từ CategoryModel::getTree()
 */

// Giá trị mặc định nếu không truyền vào
$page_title = $page_title ?? 'LONG CHÂU — Nhà thuốc trực tuyến uy tín';
$page_desc = $page_desc ?? 'Mua thuốc, thực phẩm chức năng, mỹ phẩm dược chính hãng. Giao hàng nhanh toàn quốc. Được tư vấn bởi dược sĩ chuyên nghiệp.';
$extra_css = $extra_css ?? [];
$cart_count = $cart_count ?? 0;

// Lấy danh mục nếu chưa có (tránh lỗi khi dùng ở nhiều trang)
if (empty($categories)) {
    // $categories = CategoryModel::getTree(); // bật khi đã có model
    $categories = [
        [
            'id' => 1,
            'name' => 'Thuốc kê đơn',
            'slug' => 'thuoc-ke-don',
            'icon' => 'pill',
            'children' => [
                ['name' => 'Kháng sinh', 'slug' => 'khang-sinh'],
                ['name' => 'Tim mạch', 'slug' => 'tim-mach'],
                ['name' => 'Thần kinh', 'slug' => 'than-kinh'],
                ['name' => 'Tiểu đường', 'slug' => 'tieu-duong'],
                ['name' => 'Ung thư', 'slug' => 'ung-thu'],
                ['name' => 'Nội tiết', 'slug' => 'noi-tiet'],
            ]
        ],
        [
            'id' => 2,
            'name' => 'Thuốc không kê đơn',
            'slug' => 'thuoc-khong-ke-don',
            'icon' => 'capsule',
            'children' => [
                ['name' => 'Giảm đau hạ sốt', 'slug' => 'giam-dau-ha-sot'],
                ['name' => 'Tiêu hóa', 'slug' => 'tieu-hoa'],
                ['name' => 'Hô hấp', 'slug' => 'ho-hap'],
                ['name' => 'Dị ứng', 'slug' => 'di-ung'],
                ['name' => 'Mắt, tai, mũi', 'slug' => 'mat-tai-mui'],
                ['name' => 'Ngoài da', 'slug' => 'ngoai-da'],
            ]
        ],
        [
            'id' => 3,
            'name' => 'Thực phẩm chức năng',
            'slug' => 'thuc-pham-chuc-nang',
            'icon' => 'leaf',
            'children' => [
                ['name' => 'Vitamin & khoáng chất', 'slug' => 'vitamin-khoang-chat'],
                ['name' => 'Tăng đề kháng', 'slug' => 'tang-de-khang'],
                ['name' => 'Xương khớp', 'slug' => 'xuong-khop'],
                ['name' => 'Gan, mật', 'slug' => 'gan-mat'],
                ['name' => 'Hỗ trợ sinh lý', 'slug' => 'ho-tro-sinh-ly'],
                ['name' => 'Trẻ em', 'slug' => 'tre-em'],
            ]
        ],
        [
            'id' => 4,
            'name' => 'Mỹ phẩm dược',
            'slug' => 'my-pham-duoc',
            'icon' => 'sparkle',
            'children' => [
                ['name' => 'Chăm sóc da mặt', 'slug' => 'cham-soc-da-mat'],
                ['name' => 'Chống nắng', 'slug' => 'chong-nang'],
                ['name' => 'Tẩy trang', 'slug' => 'tay-trang'],
                ['name' => 'Chăm sóc tóc', 'slug' => 'cham-soc-toc'],
                ['name' => 'Vệ sinh cơ thể', 'slug' => 've-sinh-co-the'],
            ]
        ],
        [
            'id' => 5,
            'name' => 'Thiết bị y tế',
            'slug' => 'thiet-bi-y-te',
            'icon' => 'heart',
            'children' => [
                ['name' => 'Máy đo huyết áp', 'slug' => 'may-do-huyet-ap'],
                ['name' => 'Máy đo đường huyết', 'slug' => 'may-do-duong-huyet'],
                ['name' => 'Nhiệt kế', 'slug' => 'nhiet-ke'],
                ['name' => 'Khẩu trang', 'slug' => 'khau-trang'],
                ['name' => 'Băng bó, sơ cứu', 'slug' => 'bang-bo-so-cuu'],
            ]
        ],
    ];
}

$user = $_SESSION['user'] ?? null;
$current_url = $_SERVER['REQUEST_URI'] ?? '/';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($page_desc) ?>">
    <meta property="og:title" content="<?= htmlspecialchars($page_title) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($page_desc) ?>">
    <title><?= htmlspecialchars($page_title) ?></title>

    <!-- Preconnect fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/main.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/footer.css">
    <!-- Page-specific CSS -->
    <?php foreach ($extra_css as $css): ?>
        <link rel="stylesheet" href="/public/css/<?= htmlspecialchars($css) ?>">
    <?php endforeach; ?>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/public/img/favicon.svg">
</head>

<body>

    <!-- ════════════════════════════════════
     TOPBAR — thông tin nhanh & điều hướng phụ
     ════════════════════════════════════ -->
    <div class="topbar">
        <div class="container topbar-inner">

            <div class="topbar-left">
                <a href="tel:18001234" class="topbar-item">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 10.8 19.79 19.79 0 01.0 2.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14z" />
                    </svg>
                    <span>Hotline: <strong style="color:#fff">1800 1234</strong></span>
                </a>

                <a href="/gio-hang-thuoc" class="topbar-item">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Gửi đơn thuốc
                </a>

                <span class="topbar-item">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                    Giao hàng nhanh 2h nội thành
                </span>
            </div>

            <div class="topbar-right">
                <?php if ($user): ?>
                    <a href="/tai-khoan" class="topbar-link">Xin chào,
                        <strong><?= htmlspecialchars(explode(' ', $user['full_name'])[0]) ?></strong></a>
                    <div class="topbar-divider"></div>
                    <a href="/dang-xuat" class="topbar-link">Đăng xuất</a>
                <?php else: ?>
                    <a href="/dang-nhap" class="topbar-link">Đăng nhập</a>
                    <div class="topbar-divider"></div>
                    <a href="/dang-ky" class="topbar-link">Đăng ký</a>
                <?php endif; ?>

                <?php if ($user && in_array($user['role'], ['admin', 'pharmacist', 'warehouse'])): ?>
                    <div class="topbar-divider"></div>
                    <a href="/admin" class="topbar-link" style="color:var(--primary)">
                        Quản trị &rarr;
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>


    <!-- ════════════════════════════════════
     MAIN HEADER
     ════════════════════════════════════ -->
    <header class="site-header" id="siteHeader">
        <div class="container header-inner">

            <!-- Hamburger (mobile) -->
            <button class="action-btn" id="mobileMenuBtn" aria-label="Mở menu" style="display:none">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="6" x2="21" y2="6" />
                    <line x1="3" y1="12" x2="21" y2="12" />
                    <line x1="3" y1="18" x2="21" y2="18" />
                </svg>
            </button>

            <!-- Logo -->
            <a href="/" class="site-logo" aria-label="PharmaViet - Trang chủ">
                <div class="logo-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L2 7l10 5 10-5-10-5z" />
                        <path d="M2 17l10 5 10-5" />
                        <path d="M2 12l10 5 10-5" />
                    </svg>
                </div>
                <div class="logo-text">
                    <div class="logo-name">Pharma<span>Viet</span></div>
                    <div class="logo-tagline">Nhà thuốc uy tín</div>
                </div>
            </a>

            <!-- Nút danh mục (desktop) -->
            <button class="cat-btn" id="catBtn" aria-expanded="false" aria-controls="megaMenu">
                <span class="hamburger">
                    <span></span><span></span><span></span>
                </span>
                <span>Danh mục</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9" />
                </svg>
            </button>

            <!-- Search Bar -->
            <div class="search-wrap">
                <form class="search-form" action="/tim-kiem" method="GET" role="search" autocomplete="off">
                    <input type="search" name="q" class="search-input" id="searchInput"
                        placeholder="Tìm thuốc, thực phẩm chức năng, thương hiệu..." aria-label="Tìm kiếm sản phẩm"
                        value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                    <button type="submit" class="search-btn" aria-label="Tìm kiếm">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                    </button>
                </form>

                <!-- Gợi ý tìm kiếm (JS điều khiển) -->
                <div class="search-suggestions" id="searchSuggestions" role="listbox" aria-label="Gợi ý tìm kiếm">
                    <div class="suggestion-label">Tìm kiếm phổ biến</div>
                    <div id="suggestionList"></div>
                </div>
            </div>

            <!-- Desktop Nav -->
            <nav class="site-nav" aria-label="Điều hướng chính">
                <a href="/khuyen-mai"
                    class="nav-link <?= str_starts_with($current_url, '/khuyen-mai') ? 'active' : '' ?>">Khuyến mãi</a>
                <a href="/tin-tuc"
                    class="nav-link <?= str_starts_with($current_url, '/tin-tuc') ? 'active' : '' ?>">Tin sức
                    khỏe</a>
                <a href="/he-thong-nha-thuoc" class="nav-link">Nhà thuốc</a>
            </nav>

            <!-- Action Buttons -->
            <div class="header-actions">
                <!-- Tài khoản -->
                <?php if ($user): ?>
                    <a href="/tai-khoan" class="action-btn" title="Tài khoản của tôi">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        <span class="action-label"><?= htmlspecialchars(explode(' ', $user['full_name'])[0]) ?></span>
                    </a>
                <?php else: ?>
                    <a href="/dang-nhap" class="action-btn" title="Đăng nhập">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        <span class="action-label">Đăng nhập</span>
                    </a>
                <?php endif; ?>

                <!-- Yêu thích -->
                <a href="/yeu-thich" class="action-btn" title="Danh sách yêu thích">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" />
                    </svg>
                    <span class="action-label">Yêu thích</span>
                </a>

                <!-- Đơn hàng -->
                <a href="/don-hang" class="action-btn" title="Đơn hàng của tôi">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="16" y1="13" x2="8" y2="13" />
                        <line x1="16" y1="17" x2="8" y2="17" />
                        <polyline points="10 9 9 9 8 9" />
                    </svg>
                    <span class="action-label">Đơn hàng</span>
                </a>

                <!-- Giỏ hàng -->
                <a href="/gio-hang" class="action-btn" title="Giỏ hàng" id="cartBtn">
                    <?php if ($cart_count > 0): ?>
                        <span class="badge badge-accent"><?= $cart_count > 99 ? '99+' : $cart_count ?></span>
                    <?php endif; ?>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1" />
                        <circle cx="20" cy="21" r="1" />
                        <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6" />
                    </svg>
                    <span class="action-label">Giỏ hàng</span>
                </a>
            </div>

        </div>
    </header>


    <!-- ════════════════════════════════════
     MEGA MENU — danh mục sản phẩm
     ════════════════════════════════════ -->
    <div class="mega-menu" id="megaMenu" role="navigation" aria-label="Danh mục sản phẩm">
        <div class="container">
            <div class="mega-inner">

                <!-- Danh sách danh mục cha (trái) -->
                <div class="mega-cats" role="list">
                    <?php foreach ($categories as $i => $cat): ?>
                        <div class="mega-cat-item <?= $i === 0 ? 'active' : '' ?>" data-target="subpanel-<?= $cat['id'] ?>"
                            role="listitem" tabindex="0" aria-expanded="<?= $i === 0 ? 'true' : 'false' ?>">
                            <div class="mega-cat-icon">
                                <?php
                                $icons = [
                                    'pill' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.5 20H4a2 2 0 01-2-2V5c0-1.1.9-2 2-2h3.93a2 2 0 011.66.9l.82 1.2a2 2 0 001.66.9H20a2 2 0 012 2v2.5"/><circle cx="17" cy="17" r="5"/><path d="M13.9 20.1L20.1 13.9"/></svg>',
                                    'capsule' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="8" width="20" height="8" rx="4"/><line x1="12" y1="8" x2="12" y2="16"/></svg>',
                                    'leaf' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 014 13c0-5 4-9 7-11 3 2 7 6 7 11a7 7 0 01-7 7z"/><path d="M11 20v-9"/></svg>',
                                    'sparkle' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.88 5.76H20l-4.94 3.59 1.88 5.76L12 14.52l-4.94 3.59 1.88-5.76L4 8.76h6.12z"/></svg>',
                                    'heart' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>',
                                ];
                                echo $icons[$cat['icon']] ?? $icons['pill'];
                                ?>
                            </div>
                            <span><?= htmlspecialchars($cat['name']) ?></span>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                style="margin-left:auto;opacity:.4">
                                <polyline points="9 18 15 12 9 6" />
                            </svg>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Sub-panels (phải) -->
                <div style="position:relative">
                    <?php foreach ($categories as $i => $cat): ?>
                        <div class="mega-sub-panel <?= $i === 0 ? 'active' : '' ?>" id="subpanel-<?= $cat['id'] ?>">
                            <div class="mega-subcats">
                                <div class="mega-sub-title">
                                    <?= htmlspecialchars($cat['name']) ?>
                                    &mdash; Tất cả danh mục
                                </div>

                                <!-- Link xem tất cả -->
                                <a href="/danh-muc/<?= htmlspecialchars($cat['slug']) ?>" class="mega-sub-item"
                                    style="margin-bottom:8px;font-weight:600;color:var(--primary)">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="7" height="7" />
                                        <rect x="14" y="3" width="7" height="7" />
                                        <rect x="3" y="14" width="7" height="7" />
                                        <rect x="14" y="14" width="7" height="7" />
                                    </svg>
                                    Xem tất cả <?= htmlspecialchars($cat['name']) ?>
                                </a>

                                <!-- Danh mục con -->
                                <div class="mega-sub-grid">
                                    <?php foreach ($cat['children'] as $sub): ?>
                                        <a href="/danh-muc/<?= htmlspecialchars($sub['slug']) ?>" class="mega-sub-item">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="9 18 15 12 9 6" />
                                            </svg>
                                            <?= htmlspecialchars($sub['name']) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>
    </div>


    <!-- ════════════════════════════════════
     MOBILE DRAWER MENU
     ════════════════════════════════════ -->
    <aside class="mobile-drawer" id="mobileDrawer" aria-label="Menu di động" aria-hidden="true">
        <div class="drawer-header">
            <a href="/" class="site-logo">
                <div class="logo-icon" style="width:30px;height:30px">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L2 7l10 5 10-5-10-5z" />
                        <path d="M2 17l10 5 10-5" />
                        <path d="M2 12l10 5 10-5" />
                    </svg>
                </div>
                <div class="logo-text">
                    <div class="logo-name" style="font-size:1rem">Pharma<span>Viet</span></div>
                </div>
            </a>
            <button class="drawer-close" id="drawerClose" aria-label="Đóng menu">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>
        </div>

        <!-- User info -->
        <div class="drawer-user">
            <?php if ($user): ?>
                <div class="drawer-user-info">
                    <strong><?= htmlspecialchars($user['full_name']) ?></strong>
                    <?= htmlspecialchars($user['email']) ?>
                </div>
            <?php else: ?>
                <div style="display:flex;gap:8px">
                    <a href="/dang-nhap" class="btn btn-primary"
                        style="flex:1;justify-content:center;font-size:.82rem;padding:8px 16px">Đăng nhập</a>
                    <a href="/dang-ky" class="btn btn-outline"
                        style="flex:1;justify-content:center;font-size:.82rem;padding:8px 16px">Đăng ký</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Navigation -->
        <nav class="drawer-nav">
            <div class="drawer-section-title">Mua sắm</div>
            <?php foreach ($categories as $cat): ?>
                <a href="/danh-muc/<?= htmlspecialchars($cat['slug']) ?>" class="drawer-nav-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6" />
                    </svg>
                    <?= htmlspecialchars($cat['name']) ?>
                </a>
            <?php endforeach; ?>

            <div class="drawer-divider"></div>
            <div class="drawer-section-title">Dịch vụ</div>

            <a href="/gio-hang-thuoc" class="drawer-nav-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Gửi đơn thuốc
            </a>
            <a href="/tin-tuc" class="drawer-nav-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path
                        d="M4 22h16a2 2 0 002-2V4a2 2 0 00-2-2H8a2 2 0 00-2 2v16a2 2 0 01-2 2zm0 0a2 2 0 01-2-2v-9c0-1.1.9-2 2-2h2" />
                </svg>
                Tin sức khỏe
            </a>
            <a href="/he-thong-nha-thuoc" class="drawer-nav-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" />
                    <circle cx="12" cy="10" r="3" />
                </svg>
                Hệ thống nhà thuốc
            </a>

            <?php if ($user): ?>
                <div class="drawer-divider"></div>
                <div class="drawer-section-title">Tài khoản</div>
                <a href="/tai-khoan" class="drawer-nav-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                    Thông tin cá nhân
                </a>
                <a href="/don-hang" class="drawer-nav-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                    </svg>
                    Đơn hàng của tôi
                </a>
                <a href="/dang-xuat" class="drawer-nav-item" style="color:#ef4444">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" />
                        <polyline points="16 17 21 12 16 7" />
                        <line x1="21" y1="12" x2="9" y2="12" />
                    </svg>
                    Đăng xuất
                </a>
            <?php endif; ?>
        </nav>
    </aside>

    <!-- Overlay -->
    <div class="overlay" id="overlay"></div>

    <!-- Toast container -->
    <div class="toast-container" id="toastContainer" aria-live="polite"></div>

    <!-- ════════════════════════════════════
     Bắt đầu nội dung trang
     ════════════════════════════════════ -->
    <main class="site-main" id="mainContent">
    </main>

</body>