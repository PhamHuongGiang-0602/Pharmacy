</main><!-- end #pageMain -->

<!-- ===== FOOTER ===== -->
<footer class="main-footer">
  <div class="footer-top">
    <div class="container footer-grid">

      <div class="footer-col footer-brand">
        <a href="/" class="footer-logo">
          <svg viewBox="0 0 36 36" fill="none" width="36" height="36">
            <rect width="36" height="36" rx="8" fill="#00904a"/>
            <path d="M18 8v20M8 18h20" stroke="#fff" stroke-width="4" stroke-linecap="round"/>
          </svg>
          <strong>Long Châu</strong>
        </a>
        <p>Nhà thuốc trực tuyến uy tín — 10,000+ sản phẩm chính hãng, giao hàng nhanh 2 giờ, tư vấn dược sĩ 24/7.</p>
        <div class="footer-cert">
          <span class="cert-badge">✓ Bộ Y tế cấp phép</span>
          <span class="cert-badge">✓ Đã đăng ký ĐKKD</span>
        </div>
        <div class="social-links">
          <a href="#" class="social-btn fb" aria-label="Facebook">f</a>
          <a href="#" class="social-btn yt" aria-label="YouTube">▶</a>
          <a href="#" class="social-btn zl" aria-label="Zalo">Z</a>
          <a href="#" class="social-btn ig" aria-label="Instagram">◎</a>
        </div>
      </div>

      <div class="footer-col">
        <h4>Về Long Châu</h4>
        <ul>
          <li><a href="/about">Giới thiệu</a></li>
          <li><a href="/careers">Tuyển dụng</a></li>
          <li><a href="/stores">Hệ thống cửa hàng</a></li>
          <li><a href="/franchise">Nhượng quyền</a></li>
          <li><a href="/news">Tin tức</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4>Hỗ trợ khách hàng</h4>
        <ul>
          <li><a href="/help/faq">Câu hỏi thường gặp</a></li>
          <li><a href="/help/shipping">Chính sách giao hàng</a></li>
          <li><a href="/help/return">Chính sách đổi trả</a></li>
          <li><a href="/help/payment">Phương thức thanh toán</a></li>
          <li><a href="/prescription">Hướng dẫn mua thuốc kê đơn</a></li>
          <li><a href="/consult">Tư vấn dược sĩ</a></li>
        </ul>
      </div>

      <div class="footer-col footer-contact">
        <h4>Liên hệ</h4>
        <div class="contact-item">
          <span>📞</span>
          <div>
            <strong>Hotline: 1800 599 921</strong>
            <small>Miễn phí — 8:00–22:00 mỗi ngày</small>
          </div>
        </div>
        <div class="contact-item">
          <span>✉️</span>
          <div>
            <a href="mailto:cskh@longchau.vn">cskh@longchau.vn</a>
          </div>
        </div>
        <div class="contact-item">
          <span>📍</span>
          <div>
            <span>123 Nguyễn Thị Minh Khai, Q.1, TP.HCM</span>
          </div>
        </div>
        <div class="footer-app">
          <p>Tải ứng dụng:</p>
          <div class="app-badges">
            <a href="#" class="app-badge">
              <span>🍎</span> App Store
            </a>
            <a href="#" class="app-badge">
              <span>▶</span> Google Play
            </a>
          </div>
        </div>
      </div>

    </div>
  </div>

  <div class="footer-bottom">
    <div class="container footer-bottom-inner">
      <p>© 2025 Long Châu. Giấy phép kinh doanh số: 0312345678 do Sở KH&ĐT TP.HCM cấp.</p>
      <div class="footer-legal">
        <a href="/privacy">Bảo mật thông tin</a>
        <a href="/terms">Điều khoản sử dụng</a>
        <a href="/cookie">Chính sách Cookie</a>
      </div>
    </div>
  </div>
</footer>

<!-- ===== BACK TO TOP ===== -->
<button class="back-to-top" id="backToTop" aria-label="Về đầu trang">↑</button>

<!-- ===== CHAT BUTTON ===== -->
<a href="/consult" class="chat-float" title="Chat với dược sĩ">
  <span class="chat-icon">💬</span>
  <span class="chat-label">Tư vấn</span>
</a>

<script src="/public/js/main.js"></script>
<?php if (isset($extraJs)): foreach ($extraJs as $js): ?>
  <script src="<?= htmlspecialchars($js) ?>"></script>
<?php endforeach; endif; ?>

<script>
// Search suggestions (debounce)
const searchInput = document.getElementById('globalSearch');
const suggestions = document.getElementById('searchSuggestions');
let searchTimer;
if (searchInput) {
  searchInput.addEventListener('input', function() {
    clearTimeout(searchTimer);
    const q = this.value.trim();
    if (q.length < 2) { suggestions.innerHTML = ''; suggestions.style.display = 'none'; return; }
    searchTimer = setTimeout(() => {
      fetch('/products/search-suggest?q=' + encodeURIComponent(q))
        .then(r => r.json())
        .then(items => {
          if (!items.length) { suggestions.style.display = 'none'; return; }
          suggestions.innerHTML = items.map(i =>
            `<a href="/product/${i.id}" class="suggestion-item">
              <span class="sug-name">${i.name}</span>
              <span class="sug-price">${i.price}</span>
            </a>`
          ).join('');
          suggestions.style.display = 'block';
        }).catch(() => {});
    }, 280);
  });
  document.addEventListener('click', e => {
    if (!searchInput.contains(e.target)) { suggestions.style.display = 'none'; }
  });
}

// Account dropdown
const accDrop = document.getElementById('accountDropdown');
if (accDrop) {
  accDrop.addEventListener('mouseenter', () => accDrop.classList.add('open'));
  accDrop.addEventListener('mouseleave', () => accDrop.classList.remove('open'));
}

// Sticky header
const header = document.getElementById('mainHeader');
window.addEventListener('scroll', () => {
  header.classList.toggle('sticky', window.scrollY > 80);
});

// Back to top
const btt = document.getElementById('backToTop');
window.addEventListener('scroll', () => btt.classList.toggle('visible', window.scrollY > 400));
btt.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

// Mega menu
const megaTrigger = document.getElementById('megaTrigger');
const megaMenu = document.getElementById('megaMenu');
if (megaTrigger) {
  megaTrigger.addEventListener('mouseenter', () => megaMenu.classList.add('open'));
  megaTrigger.addEventListener('mouseleave', () => megaMenu.classList.remove('open'));
}
</script>
</body>
</html>