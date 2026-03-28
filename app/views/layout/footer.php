<?php
/**
 * footer.php — Layout component chung
 *
 * Đặt ở cuối mọi trang. Đóng thẻ <main> mở trong header.php,
 * render newsletter banner, footer grid, bottom bar,
 * nút back-to-top, và toàn bộ JavaScript.
 *
 * Biến tùy chọn:
 *   $extra_js  array  — các file JS bổ sung ['js/product.js']
 *   $hide_newsletter bool — ẩn newsletter (trang checkout)
 */

$extra_js         = $extra_js ?? [];
$hide_newsletter  = $hide_newsletter ?? false;
?>

</main><!-- /.site-main -->


<!-- ════════════════════════════════════
     NEWSLETTER BANNER
     ════════════════════════════════════ -->
<?php if (!$hide_newsletter): ?>
<section class="newsletter-banner" aria-label="Đăng ký nhận tin">
    <div class="container">
        <div class="newsletter-inner">
            <div class="newsletter-text">
                <h3>Nhận ưu đãi sức khỏe mỗi tuần</h3>
                <p>Cập nhật khuyến mãi độc quyền, tin tức sức khỏe & thông báo sản phẩm mới.</p>
            </div>
            <form class="newsletter-form" id="newsletterForm" novalidate>
                <input
                    type="email"
                    name="email"
                    class="newsletter-input"
                    placeholder="Nhập email của bạn..."
                    aria-label="Email đăng ký nhận tin"
                    required
                >
                <button type="submit" class="newsletter-submit">Đăng ký ngay</button>
            </form>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- ════════════════════════════════════
     MAIN FOOTER
     ════════════════════════════════════ -->
<footer class="site-footer" role="contentinfo">
    <div class="container">
        <div class="footer-grid">

            <!-- CỘT 1: Thương hiệu -->
            <div class="footer-brand">
                <div class="footer-logo">
                    <div class="logo-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                            <path d="M2 17l10 5 10-5"/>
                            <path d="M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <div class="logo-text">
                        <div class="logo-name">Pharma<span>Viet</span></div>
                        <div class="logo-tagline">Nhà thuốc uy tín</div>
                    </div>
                </div>

                <p class="footer-about">
                    PharmaViet cam kết cung cấp thuốc và sản phẩm chăm sóc sức khỏe chính hãng,
                    được tư vấn bởi đội ngũ dược sĩ chuyên nghiệp. Giao hàng nhanh,
                    giá minh bạch, chất lượng đảm bảo.
                </p>

                <!-- Chứng nhận -->
                <div class="footer-cert">
                    <div class="footer-cert-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <polyline points="9 12 11 14 15 10"/>
                        </svg>
                    </div>
                    <div class="footer-cert-text">
                        <strong>Được cấp phép Bộ Y tế</strong>
                        Số GPHĐ: 01234/BYT-GDP &bull; GPKD: 0123456789
                    </div>
                </div>

                <!-- Mạng xã hội -->
                <div class="footer-socials" aria-label="Mạng xã hội">
                    <a href="https://facebook.com" class="social-btn" aria-label="Facebook" rel="noopener noreferrer" target="_blank">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
                        </svg>
                    </a>
                    <a href="https://zalo.me" class="social-btn" aria-label="Zalo" target="_blank" rel="noopener noreferrer">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V9h2v7zm4 0h-2V9h2v7z"/>
                        </svg>
                    </a>
                    <a href="https://youtube.com" class="social-btn" aria-label="YouTube" target="_blank" rel="noopener noreferrer">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M22.54 6.42a2.78 2.78 0 00-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 00-1.95 1.96A29 29 0 001 12a29 29 0 00.46 5.58 2.78 2.78 0 001.95 1.96C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 001.95-1.96A29 29 0 0023 12a29 29 0 00-.46-5.58zM9.75 15.02V8.98L15.5 12l-5.75 3.02z"/>
                        </svg>
                    </a>
                    <a href="https://tiktok.com" class="social-btn" aria-label="TikTok" target="_blank" rel="noopener noreferrer">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.75a8.19 8.19 0 004.79 1.53V6.84a4.85 4.85 0 01-1.02-.15z"/>
                        </svg>
                    </a>
                </div>

                <!-- Phương thức thanh toán -->
                <div class="footer-badges" style="margin-top:18px">
                    <div class="footer-badges-title">Phương thức thanh toán</div>
                    <div class="footer-badge-row">
                        <div class="payment-badge">COD</div>
                        <div class="payment-badge">Momo</div>
                        <div class="payment-badge">VNPay</div>
                        <div class="payment-badge">ATM</div>
                        <div class="payment-badge">Visa</div>
                        <div class="payment-badge">Banking</div>
                    </div>
                </div>
            </div>

            <!-- CỘT 2: Sản phẩm -->
            <div class="footer-col">
                <h3 class="footer-col-title">Danh mục sản phẩm</h3>
                <nav class="footer-links" aria-label="Danh mục sản phẩm">
                    <a href="/danh-muc/thuoc-ke-don"       class="footer-link">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        Thuốc kê đơn
                    </a>
                    <a href="/danh-muc/thuoc-khong-ke-don" class="footer-link">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        Thuốc không kê đơn
                    </a>
                    <a href="/danh-muc/thuc-pham-chuc-nang" class="footer-link">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        Thực phẩm chức năng
                    </a>
                    <a href="/danh-muc/my-pham-duoc"       class="footer-link">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        Mỹ phẩm dược
                    </a>
                    <a href="/danh-muc/thiet-bi-y-te"       class="footer-link">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        Thiết bị y tế
                    </a>
                    <a href="/khuyen-mai"                   class="footer-link">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        Sản phẩm khuyến mãi
                    </a>
                </nav>
            </div>

            <!-- CỘT 3: Hỗ trợ -->
            <div class="footer-col">
                <h3 class="footer-col-title">Hỗ trợ khách hàng</h3>
                <nav class="footer-links" aria-label="Hỗ trợ">
                    <a href="/huong-dan-mua-hang" class="footer-link">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        Hướng dẫn mua hàng
                    </a>
                    <a href="/chinh-sach-giao-hang" class="footer-link">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        Chính sách giao hàng
                    </a>
                    <a href="/chinh-sach-doi-tra" class="footer-link">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        Chính sách đổi trả
                    </a>
                    <a href="/chinh-sach-bao-mat" class="footer-link">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        Chính sách bảo mật
                    </a>
                    <a href="/gio-hang-thuoc" class="footer-link">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        Gửi đơn thuốc online
                    </a>
                    <a href="/cau-hoi-thuong-gap" class="footer-link">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        Câu hỏi thường gặp
                    </a>
                </nav>
            </div>

            <!-- CỘT 4: Liên hệ -->
            <div class="footer-col">
                <h3 class="footer-col-title">Liên hệ</h3>
                <div class="footer-contact">
                    <div class="footer-contact-item">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 10.8 19.79 19.79 0 010 2.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14z"/>
                        </svg>
                        <div>
                            Hotline (miễn phí 24/7)
                            <a href="tel:18001234" class="footer-hotline">1800 1234</a>
                        </div>
                    </div>

                    <div class="footer-contact-item">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                        <a href="mailto:cskh@pharmaviet.vn">cskh@pharmaviet.vn</a>
                    </div>

                    <div class="footer-contact-item">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>
                        </svg>
                        <span>123 Đường Nguyễn Hữu Thọ, Quận 7, TP. Hồ Chí Minh</span>
                    </div>

                    <div class="footer-contact-item">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <span>Thứ 2 – CN: 7:30 – 22:00</span>
                    </div>
                </div>

                <!-- App download -->
                <div class="footer-badges" style="margin-top:18px">
                    <div class="footer-badges-title">Tải ứng dụng</div>
                    <div class="footer-badge-row">
                        <a href="#" class="payment-badge" style="gap:5px;text-decoration:none">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
                            App Store
                        </a>
                        <a href="#" class="payment-badge" style="gap:5px;text-decoration:none">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M3 20.5v-17c0-.83.94-1.3 1.6-.8l14 8.5c.6.36.6 1.24 0 1.6l-14 8.5c-.66.5-1.6.03-1.6-.8z"/></svg>
                            Google Play
                        </a>
                    </div>
                </div>
            </div>

        </div><!-- /.footer-grid -->
    </div><!-- /.container -->

    <!-- Bottom Bar -->
    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom-inner">
                <p class="footer-copy">
                    &copy; <?= date('Y') ?> <a href="/">PharmaViet</a>.
                    Mọi quyền được bảo lưu. Thuộc Công ty CP Dược phẩm PharmaViet.
                </p>

                <div class="footer-bottom-links">
                    <a href="/dieu-khoan-su-dung" class="footer-bottom-link">Điều khoản sử dụng</a>
                    <div class="footer-bottom-sep"></div>
                    <a href="/chinh-sach-bao-mat"  class="footer-bottom-link">Bảo mật</a>
                    <div class="footer-bottom-sep"></div>
                    <a href="/sitemap.xml"           class="footer-bottom-link">Sitemap</a>
                </div>
            </div>
        </div>
    </div>

</footer>


<!-- ════════════════════════════════════
     NÚT BACK TO TOP
     ════════════════════════════════════ -->
<a href="#" class="back-to-top" id="backToTop" aria-label="Lên đầu trang" title="Lên đầu trang">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="18 15 12 9 6 15"/>
    </svg>
</a>


<!-- ════════════════════════════════════
     JAVASCRIPT
     ════════════════════════════════════ -->
<script>
(function() {
'use strict';

/* ── Helpers ── */
const $ = id => document.getElementById(id);
const on = (el, ev, fn) => el && el.addEventListener(ev, fn);

/* ── Elements ── */
const header     = $('siteHeader');
const catBtn     = $('catBtn');
const megaMenu   = $('megaMenu');
const overlay    = $('overlay');
const mobileBtn  = $('mobileMenuBtn');
const drawer     = $('mobileDrawer');
const drawerClose= $('drawerClose');
const backToTop  = $('backToTop');
const searchInput= $('searchInput');
const suggestions= $('searchSuggestions');
const suggList   = $('suggestionList');
const newsletter = $('newsletterForm');

/* ── Header scroll shadow ── */
on(window, 'scroll', () => {
    if (!header) return;
    header.classList.toggle('scrolled', window.scrollY > 10);
    if (backToTop) backToTop.classList.toggle('show', window.scrollY > 300);
});

/* ── Back to top ── */
on(backToTop, 'click', e => {
    e.preventDefault();
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

/* ── Mega Menu toggle ── */
let megaOpen = false;

function openMega() {
    megaOpen = true;
    megaMenu?.classList.add('open');
    overlay?.classList.add('show');
    catBtn?.setAttribute('aria-expanded', 'true');
}
function closeMega() {
    megaOpen = false;
    megaMenu?.classList.remove('open');
    overlay?.classList.remove('show');
    catBtn?.setAttribute('aria-expanded', 'false');
}

on(catBtn, 'click', e => {
    e.stopPropagation();
    megaOpen ? closeMega() : openMega();
});

/* ── Mega category hover (hiện sub-panel) ── */
document.querySelectorAll('.mega-cat-item').forEach(item => {
    on(item, 'mouseenter', () => {
        document.querySelectorAll('.mega-cat-item').forEach(i => i.classList.remove('active'));
        document.querySelectorAll('.mega-sub-panel').forEach(p => p.classList.remove('active'));
        item.classList.add('active');
        const target = item.dataset.target;
        if (target) document.getElementById(target)?.classList.add('active');
    });
    on(item, 'keydown', e => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            item.dispatchEvent(new Event('mouseenter'));
        }
    });
});

/* ── Close mega on outside click ── */
on(overlay, 'click', () => {
    closeMega();
    closeDrawer();
});
on(document, 'keydown', e => {
    if (e.key === 'Escape') { closeMega(); closeDrawer(); }
});

/* ── Mobile Drawer ── */
function openDrawer() {
    drawer?.classList.add('open');
    overlay?.classList.add('show');
    drawer?.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
}
function closeDrawer() {
    drawer?.classList.remove('open');
    if (!megaOpen) overlay?.classList.remove('show');
    drawer?.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
}

on(mobileBtn,   'click', openDrawer);
on(drawerClose, 'click', closeDrawer);

/* ── Show mobile hamburger on small screens ── */
function checkMobile() {
    if (mobileBtn) {
        mobileBtn.style.display = window.innerWidth <= 1024 ? 'flex' : 'none';
    }
}
checkMobile();
on(window, 'resize', checkMobile);

/* ── Search suggestions ── */
const popularSearches = [
    'Paracetamol 500mg', 'Vitamin C', 'Omega 3', 'Amoxicillin',
    'Ibuprofen', 'Cao xương khớp', 'Probiotics', 'Mask 3M'
];
let searchTimeout;

on(searchInput, 'input', function() {
    clearTimeout(searchTimeout);
    const val = this.value.trim();

    if (val.length < 2) {
        suggestions?.classList.remove('show');
        return;
    }

    searchTimeout = setTimeout(() => {
        const filtered = popularSearches.filter(s =>
            s.toLowerCase().includes(val.toLowerCase())
        );

        if (filtered.length === 0) {
            suggestions?.classList.remove('show');
            return;
        }

        if (suggList) {
            suggList.innerHTML = filtered.map(s => {
                const highlighted = s.replace(
                    new RegExp(`(${val})`, 'gi'),
                    '<mark>$1</mark>'
                );
                return `<div class="suggestion-item" onclick="document.querySelector('.search-form').submit()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    ${highlighted}
                </div>`;
            }).join('');
        }
        suggestions?.classList.add('show');
    }, 200);
});

on(searchInput, 'focus', function() {
    if (this.value.trim().length >= 2) suggestions?.classList.add('show');
});

on(document, 'click', e => {
    if (!e.target.closest('.search-wrap')) {
        suggestions?.classList.remove('show');
    }
});

/* ── Newsletter form ── */
on(newsletter, 'submit', function(e) {
    e.preventDefault();
    const emailInput = this.querySelector('input[type="email"]');
    const email = emailInput?.value.trim();

    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        showToast('Vui lòng nhập email hợp lệ', 'error');
        return;
    }

    // TODO: gọi AJAX đến /api/newsletter
    showToast('Đăng ký thành công! Cảm ơn bạn.', 'success');
    if (emailInput) emailInput.value = '';
});

/* ── Toast notification ── */
window.showToast = function(msg, type = 'success', duration = 3500) {
    const container = $('toastContainer');
    if (!container) return;

    const icons = {
        success: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>',
        error:   '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
        warning: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
    };

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `${icons[type] || icons.success} <span>${msg}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(20px)';
        toast.style.transition = 'all .3s ease';
        setTimeout(() => toast.remove(), 300);
    }, duration);
};

/* ── Update cart badge via AJAX (gọi khi add to cart) ── */
window.updateCartBadge = function(count) {
    const btn   = $('cartBtn');
    const badge = btn?.querySelector('.badge');

    if (count > 0) {
        if (badge) {
            badge.textContent = count > 99 ? '99+' : count;
        } else if (btn) {
            const b = document.createElement('span');
            b.className = 'badge badge-accent';
            b.textContent = count > 99 ? '99+' : count;
            btn.prepend(b);
        }
    } else {
        badge?.remove();
    }
};

})();
</script>

<!-- Page-specific JS -->
<?php foreach ($extra_js as $js): ?>
    <script src="/public/js/<?= htmlspecialchars($js) ?>"></script>
<?php endforeach; ?>

</body>
</html>
