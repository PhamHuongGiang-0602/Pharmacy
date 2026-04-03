<?php
// home.php - Trang chủ Long Châu
// Giả sử $featuredProducts, $categories được truyền từ controller
?>

<?php include __DIR__ . '/layout/header.php'; ?>

<!-- ===== HERO BANNER ===== -->
<section class="hero-banner">
  <div class="hero-slider" id="heroSlider">

    <div class="hero-slide active" style="--bg: url('../public/images/banner1.jpg')">
      <div class="hero-content">
        <span class="hero-tag">🌿 Sản phẩm mới</span>
        <h1>Chăm sóc sức khỏe<br><strong>toàn diện</strong> mỗi ngày</h1>
        <p>Hơn 10,000+ sản phẩm chính hãng từ các thương hiệu uy tín toàn cầu</p>
        <div class="hero-actions">
          <a href="/products" class="btn btn-primary">Mua ngay</a>
          <a href="/prescription" class="btn btn-outline">Tư vấn thuốc</a>
        </div>
      </div>
      <div class="hero-badge">
        <span class="badge-circle">
          <strong>100%</strong>
          <small>Chính hãng</small>
        </span>
      </div>
    </div>

    <div class="hero-slide" style="--bg: url('../public/images/banner2.jpg')">
      <div class="hero-content">
        <span class="hero-tag">💊 Ưu đãi hôm nay</span>
        <h1>Giảm đến <strong>40%</strong><br>vitamin & thực phẩm chức năng</h1>
        <p>Chương trình khuyến mãi giới hạn — chỉ trong tuần này</p>
        <div class="hero-actions">
          <a href="/products?category=vitamins" class="btn btn-primary">Xem ưu đãi</a>
        </div>
      </div>
    </div>

    <div class="hero-slide" style="--bg: url('../public/images/banner3.jpg')">
      <div class="hero-content">
        <span class="hero-tag">📋 Dịch vụ mới</span>
        <h1>Upload đơn thuốc —<br>nhận hàng <strong>tận nhà</strong></h1>
        <p>Giao nhanh 2 giờ trong nội thành TP.HCM và Hà Nội</p>
        <div class="hero-actions">
          <a href="/prescription/upload" class="btn btn-primary">Upload đơn thuốc</a>
        </div>
      </div>
    </div>

  </div>

  <!-- Slider dots -->
  <div class="slider-dots">
    <button class="dot active" onclick="goSlide(0)"></button>
    <button class="dot" onclick="goSlide(1)"></button>
    <button class="dot" onclick="goSlide(2)"></button>
  </div>

  <!-- Slider arrows -->
  <button class="slider-arrow prev" onclick="changeSlide(-1)">&#8249;</button>
  <button class="slider-arrow next" onclick="changeSlide(1)">&#8250;</button>
</section>

<!-- ===== TRUST BAR ===== -->
<section class="trust-bar">
  <div class="container">
    <div class="trust-grid">
      <div class="trust-item">
        <span class="trust-icon">🏥</span>
        <div>
          <strong>Cam kết chính hãng</strong>
          <p>Hoàn tiền 100% nếu hàng giả</p>
        </div>
      </div>
      <div class="trust-item">
        <span class="trust-icon">🚚</span>
        <div>
          <strong>Giao hàng nhanh 2 giờ</strong>
          <p>Nội thành TP.HCM & Hà Nội</p>
        </div>
      </div>
      <div class="trust-item">
        <span class="trust-icon">💬</span>
        <div>
          <strong>Tư vấn dược sĩ 24/7</strong>
          <p>Miễn phí, không cần đặt lịch</p>
        </div>
      </div>
      <div class="trust-item">
        <span class="trust-icon">🔄</span>
        <div>
          <strong>Đổi trả trong 30 ngày</strong>
          <p>Không cần lý do, dễ dàng</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===== DANH MỤC SẢN PHẨM ===== -->
<section class="section categories-section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Danh mục sản phẩm</h2>
      <a href="/products" class="see-all">Xem tất cả →</a>
    </div>
    <div class="categories-grid">

      <a href="/products?category=thuoc-ke-don" class="category-card cat-red">
        <div class="cat-icon">💊</div>
        <span>Thuốc kê đơn</span>
      </a>
      <a href="/products?category=thuoc-otc" class="category-card cat-orange">
        <div class="cat-icon">🧴</div>
        <span>Thuốc OTC</span>
      </a>
      <a href="/products?category=vitamin" class="category-card cat-yellow">
        <div class="cat-icon">🍊</div>
        <span>Vitamin & Khoáng chất</span>
      </a>
      <a href="/products?category=cham-soc-da" class="category-card cat-pink">
        <div class="cat-icon">✨</div>
        <span>Chăm sóc da</span>
      </a>
      <a href="/products?category=me-va-be" class="category-card cat-purple">
        <div class="cat-icon">👶</div>
        <span>Mẹ & Bé</span>
      </a>
      <a href="/products?category=thiet-bi-y-te" class="category-card cat-blue">
        <div class="cat-icon">🩺</div>
        <span>Thiết bị y tế</span>
      </a>
      <a href="/products?category=thuc-pham-chuc-nang" class="category-card cat-green">
        <div class="cat-icon">🌿</div>
        <span>Thực phẩm chức năng</span>
      </a>
      <a href="/products?category=cham-soc-toc" class="category-card cat-teal">
        <div class="cat-icon">💆</div>
        <span>Chăm sóc tóc</span>
      </a>

    </div>
  </div>
</section>

<!-- ===== BANNER UPLOAD ĐƠN THUỐC ===== -->
<section class="promo-banner-strip">
  <div class="container">
    <div class="promo-strip-grid">

      <div class="promo-strip-card promo-rx">
        <div class="promo-strip-text">
          <h3>Upload đơn thuốc</h3>
          <p>Dược sĩ tư vấn & giao thuốc tận nhà</p>
          <a href="/prescription/upload" class="btn btn-white-outline">Upload ngay</a>
        </div>
        <div class="promo-strip-img">📋</div>
      </div>

      <div class="promo-strip-card promo-consult">
        <div class="promo-strip-text">
          <h3>Tư vấn sức khỏe</h3>
          <p>Chat trực tiếp với dược sĩ chuyên nghiệp</p>
          <a href="/consult" class="btn btn-white-outline">Tư vấn miễn phí</a>
        </div>
        <div class="promo-strip-img">🩺</div>
      </div>

    </div>
  </div>
</section>

<!-- ===== SẢN PHẨM NỔI BẬT ===== -->
<section class="section products-section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Sản phẩm nổi bật</h2>
      <div class="product-tabs">
        <button class="tab-btn active" data-tab="hot">🔥 Bán chạy</button>
        <button class="tab-btn" data-tab="sale">💸 Khuyến mãi</button>
        <button class="tab-btn" data-tab="new">🆕 Mới nhất</button>
      </div>
    </div>

    <div class="products-grid" id="productsGrid">
      <?php
      // Dữ liệu mẫu khi chưa có controller truyền vào
      $sampleProducts = [
        ['id'=>1,'name'=>'Vitamin C 1000mg Blackmores','price'=>285000,'old_price'=>320000,'img'=>'vitc.jpg','badge'=>'Bán chạy','rating'=>4.8,'reviews'=>1240,'brand'=>'Blackmores'],
        ['id'=>2,'name'=>'Omega-3 Fish Oil Nature\'s Way','price'=>399000,'old_price'=>450000,'img'=>'omega3.jpg','badge'=>'Sale 20%','rating'=>4.7,'reviews'=>980,'brand'=>'Nature\'s Way'],
        ['id'=>3,'name'=>'Siro Ho Prospan 100ml','price'=>125000,'old_price'=>null,'img'=>'prospan.jpg','badge'=>'','rating'=>4.9,'reviews'=>2100,'brand'=>'Engelhard'],
        ['id'=>4,'name'=>'Kem dưỡng ẩm Eucerin 50ml','price'=>320000,'old_price'=>380000,'img'=>'eucerin.jpg','badge'=>'Hot','rating'=>4.6,'reviews'=>750,'brand'=>'Eucerin'],
        ['id'=>5,'name'=>'Canxi D3 Calcium Sandoz','price'=>168000,'old_price'=>null,'img'=>'ca-d3.jpg','badge'=>'Mới','rating'=>4.5,'reviews'=>430,'brand'=>'Sandoz'],
        ['id'=>6,'name'=>'Kẽm Zinc Gluconate OPV','price'=>95000,'old_price'=>120000,'img'=>'zinc.jpg','badge'=>'Sale 21%','rating'=>4.7,'reviews'=>620,'brand'=>'OPV'],
        ['id'=>7,'name'=>'Panadol Extra 500mg (24 viên)','price'=>48000,'old_price'=>null,'img'=>'panadol.jpg','badge'=>'','rating'=>4.8,'reviews'=>3200,'brand'=>'GSK'],
        ['id'=>8,'name'=>'Kem chống nắng La Roche SPF50+','price'=>560000,'old_price'=>650000,'img'=>'larocheposay.jpg','badge'=>'Hot','rating'=>4.9,'reviews'=>1850,'brand'=>'La Roche-Posay'],
      ];
      $products = $featuredProducts ?? $sampleProducts;
      foreach ($products as $p): 
        $discount = $p['old_price'] ? round((1 - $p['price']/$p['old_price'])*100) : 0;
      ?>
      <div class="product-card" data-id="<?= $p['id'] ?>">
        <?php if (!empty($p['badge'])): ?>
          <span class="product-badge <?= str_contains($p['badge'],'Sale')||str_contains($p['badge'],'%') ? 'badge-sale' : (str_contains($p['badge'],'Mới') ? 'badge-new' : 'badge-hot') ?>"><?= htmlspecialchars($p['badge']) ?></span>
        <?php endif; ?>
        <?php if ($discount > 0): ?>
          <span class="product-discount">-<?= $discount ?>%</span>
        <?php endif; ?>

        <div class="product-img-wrap">
          <img src="/public/images/products/<?= htmlspecialchars($p['img']) ?>"
               alt="<?= htmlspecialchars($p['name']) ?>"
               onerror="this.src='/public/images/placeholder.png'">
          <div class="product-actions-hover">
            <button class="btn-quick-add" onclick="addToCart(<?= $p['id'] ?>)">
              🛒 Thêm vào giỏ
            </button>
            <a href="/product/<?= $p['id'] ?>" class="btn-view">Xem chi tiết</a>
          </div>
        </div>

        <div class="product-info">
          <span class="product-brand"><?= htmlspecialchars($p['brand']) ?></span>
          <h3 class="product-name">
            <a href="/product/<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></a>
          </h3>
          <div class="product-rating">
            <span class="stars"><?= str_repeat('★', floor($p['rating'])) ?><?= $p['rating'] - floor($p['rating']) >= 0.5 ? '½' : '' ?></span>
            <span class="review-count">(<?= number_format($p['reviews']) ?>)</span>
          </div>
          <div class="product-price-row">
            <span class="price-current"><?= number_format($p['price']) ?>đ</span>
            <?php if ($p['old_price']): ?>
              <span class="price-old"><?= number_format($p['old_price']) ?>đ</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="load-more-wrap">
      <a href="/products" class="btn btn-load-more">Xem thêm sản phẩm</a>
    </div>
  </div>
</section>

<!-- ===== THƯƠNG HIỆU ===== -->
<section class="section brands-section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Thương hiệu uy tín</h2>
    </div>
    <div class="brands-track-wrap">
      <div class="brands-track" id="brandsTrack">
        <?php
        $brands = ['Blackmores','Omega','Eucerin','La Roche-Posay','GSK','Sanofi','Pfizer','Bayer','Abbott','Novartis','Nature\'s Way','Sandoz'];
        foreach ($brands as $brand):
        ?>
        <div class="brand-logo">
          <span><?= htmlspecialchars($brand) ?></span>
        </div>
        <?php endforeach; ?>
        <!-- Duplicate for infinite scroll -->
        <?php foreach ($brands as $brand): ?>
        <div class="brand-logo">
          <span><?= htmlspecialchars($brand) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- ===== BÀI VIẾT SỨC KHỎE ===== -->
<section class="section blog-section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Góc sức khỏe</h2>
      <a href="/blog" class="see-all">Xem tất cả →</a>
    </div>
    <div class="blog-grid">

      <article class="blog-card blog-featured">
        <div class="blog-img" style="background:linear-gradient(135deg,#e8f5e9,#c8e6c9)">
          <span style="font-size:4rem">🩺</span>
        </div>
        <div class="blog-content">
          <span class="blog-tag">Sức khỏe tổng quát</span>
          <h3><a href="/blog/1">10 thói quen buổi sáng giúp tăng cường miễn dịch cả ngày</a></h3>
          <p>Khởi đầu ngày mới với những thói quen đơn giản nhưng hiệu quả, được các chuyên gia y tế khuyến nghị...</p>
          <div class="blog-meta">
            <span>👨‍⚕️ Dược sĩ Nguyễn An</span>
            <span>15 thg 3, 2025</span>
          </div>
        </div>
      </article>

      <div class="blog-list">
        <?php
        $blogPosts = [
          ['tag'=>'Vitamin','title'=>'Bổ sung Vitamin D đúng cách — liều lượng và thời điểm vàng','date'=>'12 thg 3'],
          ['tag'=>'Dinh dưỡng','title'=>'Thực phẩm giàu Omega-3 tốt nhất cho não bộ và tim mạch','date'=>'10 thg 3'],
          ['tag'=>'Mẹ & Bé','title'=>'Lịch tiêm chủng 2025 — những mũi vaccine quan trọng cho trẻ','date'=>'8 thg 3'],
        ];
        foreach ($blogPosts as $i => $post):
        ?>
        <article class="blog-list-item">
          <div class="blog-list-num"><?= str_pad($i+1, 2, '0', STR_PAD_LEFT) ?></div>
          <div>
            <span class="blog-tag"><?= htmlspecialchars($post['tag']) ?></span>
            <h4><a href="/blog/<?= $i+2 ?>"><?= htmlspecialchars($post['title']) ?></a></h4>
            <span class="blog-date"><?= $post['date'] ?></span>
          </div>
        </article>
        <?php endforeach; ?>
      </div>

    </div>
  </div>
</section>

<!-- ===== TOAST NOTIFICATION ===== -->
<div class="toast" id="cartToast">
  <span class="toast-icon">✅</span>
  <span id="toastMsg">Đã thêm vào giỏ hàng!</span>
</div>

<!-- ===== INLINE JS ===== -->
<script>
// Hero Slider
let currentSlide = 0;
const slides = document.querySelectorAll('.hero-slide');
const dots = document.querySelectorAll('.dot');

function showSlide(n) {
  slides[currentSlide].classList.remove('active');
  dots[currentSlide].classList.remove('active');
  currentSlide = (n + slides.length) % slides.length;
  slides[currentSlide].classList.add('active');
  dots[currentSlide].classList.add('active');
}
function changeSlide(dir) { showSlide(currentSlide + dir); }
function goSlide(n) { showSlide(n); }

// Auto-advance
setInterval(() => changeSlide(1), 5000);

// Product Tabs
document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    // Gọi AJAX nếu cần: fetch('/products?tab=' + this.dataset.tab)
  });
});

// Add to Cart
function addToCart(productId) {
  fetch('/cart/add', {
    method: 'POST',
    headers: {'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'},
    body: JSON.stringify({ product_id: productId, quantity: 1 })
  })
  .then(r => r.json())
  .then(data => {
    showToast(data.message || 'Đã thêm vào giỏ hàng!');
    // Cập nhật số lượng giỏ hàng trên header
    const badge = document.querySelector('.cart-count');
    if (badge && data.cart_count) badge.textContent = data.cart_count;
  })
  .catch(() => showToast('Đã thêm vào giỏ hàng!'));
}

function showToast(msg) {
  const t = document.getElementById('cartToast');
  document.getElementById('toastMsg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3000);
}
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>