<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container section animate-fade-up">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 class="section-title" style="margin-bottom: 0;">Danh mục sản phẩm</h1>
        <div style="display: flex; gap: 15px;">
            <select onchange="window.location.href='<?= BASE_URL ?>products?category=<?= htmlspecialchars($_GET['category'] ?? '') ?>&q=<?= htmlspecialchars($_GET['q'] ?? '') ?>&sort=' + this.value" style="padding: 10px; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                <option value="">Sắp xếp mặc định</option>
                <option value="price_asc" <?= (isset($_GET['sort']) && $_GET['sort'] === 'price_asc') ? 'selected' : '' ?>>Giá tăng dần</option>
                <option value="price_desc" <?= (isset($_GET['sort']) && $_GET['sort'] === 'price_desc') ? 'selected' : '' ?>>Giá giảm dần</option>
            </select>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: 250px 1fr; gap: 30px;">
        <!-- Sidebar Lọc -->
        <div class="glass-card" style="padding: 20px; height: fit-content;">
            <h3 style="margin-bottom: 15px; font-size: 1.1rem;">Danh mục</h3>
            <ul style="list-style: none; padding-left: 0; line-height: 2;">
                <li><a href="<?= BASE_URL ?>products" style="color: var(--text-color); text-decoration: none; font-weight: <?= empty($_GET['category']) ? 'bold' : 'normal' ?>;">Tất cả sản phẩm</a></li>
                <li>
                    <a href="<?= BASE_URL ?>products?category=8" style="color: var(--text-color); text-decoration: none; font-weight: <?= (isset($_GET['category']) && $_GET['category'] == '8') ? 'bold' : 'normal' ?>;">
                        Vitamin
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>products?category=4" style="color: var(--text-color); text-decoration: none; font-weight: <?= (isset($_GET['category']) && $_GET['category'] == '4') ? 'bold' : 'normal' ?>;">
                        Chăm sóc da
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>products?category=3" style="color: var(--text-color); text-decoration: none; font-weight: <?= (isset($_GET['category']) && $_GET['category'] == '3') ? 'bold' : 'normal' ?>;">
                        Mẹ & Bé
                    </a>
                </li>
                <?php if (isset($categories)): foreach ($categories as $cat): ?>
                    <?php
                        // Tránh lặp lại các danh mục quick-link đã hiển thị ở trên.
                        if (in_array((int)$cat['category_id'], [3, 4, 8], true)) {
                            continue;
                        }
                    ?>
                    <li>
                        <a href="<?= BASE_URL ?>products?category=<?= $cat['category_id'] ?>" style="color: var(--text-color); text-decoration: none; font-weight: <?= (isset($_GET['category']) && $_GET['category'] == $cat['category_id']) ? 'bold' : 'normal' ?>;">
                            <?= htmlspecialchars($cat['category_name']) ?>
                        </a>
                    </li>
                <?php endforeach; endif; ?>
            </ul>
        </div>
        
        <!-- Lưới sản phẩm -->
        <div>
            <?php if (isset($pagination)): ?>
                <div style="margin-bottom: 15px; color: var(--text-muted);">
                    Tổng <strong><?= (int)$pagination['total_products'] ?></strong> sản phẩm,
                    trang <strong><?= (int)$pagination['current_page'] ?></strong>/<?= (int)$pagination['total_pages'] ?>.
                </div>
            <?php endif; ?>

            <?php if (empty($products)): ?>
                <div class="glass-card" style="padding: 50px; text-align: center;">
                    <p>Không tìm thấy sản phẩm nào phù hợp.</p>
                </div>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px;">
                    <?php foreach ($products as $p): ?>
                        <div class="glass-card" style="padding: 15px; text-align: center; display: flex; flex-direction: column; justify-content: space-between;">
                            <a href="<?= BASE_URL ?>product/<?= $p['product_id'] ?>" style="text-decoration: none; color: inherit;">
                                <div style="height: 180px; display: flex; align-items: center; justify-content: center; overflow: hidden; border-radius: var(--radius-sm); margin-bottom: 15px; background: #fff;">
                                    <?php
                                        $rawImage = trim((string)($p['image_url'] ?? ''));
                                        $isRemoteImage = preg_match('#^https?://#i', $rawImage) === 1;
                                        $localImageFile = __DIR__ . '/../../../public/img/products/' . $rawImage;
                                        if ($isRemoteImage) {
                                            $imageSrc = $rawImage;
                                        } elseif ($rawImage !== '' && file_exists($localImageFile)) {
                                            $imageSrc = BASE_URL . 'public/img/products/' . rawurlencode($rawImage);
                                        } else {
                                            $imageSrc = 'https://picsum.photos/seed/pharmacy-product-' . (int)($p['product_id'] ?? 0) . '/600/600';
                                        }
                                    ?>
                                    <img src="<?= $imageSrc ?>" alt="<?= htmlspecialchars($p['product_name']) ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;" onerror="this.onerror=null;this.src='<?= BASE_URL ?>public/img/placeholder.png';">
                                </div>
                                <h4 style="margin-bottom: 10px; font-size: 1rem; line-height: 1.4; height: 2.8em; overflow: hidden;"><?= htmlspecialchars($p['product_name']) ?></h4>
                                <div style="color: var(--primary); font-weight: bold; font-size: 1.1rem; margin-bottom: 15px;">
                                    <?= number_format($p['current_price'] ?? $p['price']) ?>đ
                                    <?php if (!empty($p['discount_percent'])): ?>
                                        <small style="text-decoration: line-through; color: #999; margin-left: 5px; font-weight: normal;"><?= number_format($p['price']) ?>đ</small>
                                    <?php endif; ?>
                                </div>
                            </a>
                            <button onclick="addToCart(<?= $p['product_id'] ?>)" class="btn btn-premium" style="width: 100%; padding: 8px;">Thêm vào giỏ</button>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($pagination) && $pagination['total_pages'] > 1): ?>
                    <?php
                        $currentPage = (int)$pagination['current_page'];
                        $totalPages = (int)$pagination['total_pages'];
                        $baseQuery = $_GET;
                    ?>
                    <div style="margin-top: 25px; display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                        <?php if ($currentPage > 1): ?>
                            <?php $baseQuery['page'] = $currentPage - 1; ?>
                            <a class="btn btn-outline" href="<?= BASE_URL ?>products?<?= htmlspecialchars(http_build_query($baseQuery)) ?>">← Trang trước</a>
                        <?php endif; ?>

                        <?php
                            $start = max(1, $currentPage - 2);
                            $end = min($totalPages, $currentPage + 2);
                            for ($i = $start; $i <= $end; $i++):
                                $baseQuery['page'] = $i;
                        ?>
                            <a
                                href="<?= BASE_URL ?>products?<?= htmlspecialchars(http_build_query($baseQuery)) ?>"
                                class="btn <?= $i === $currentPage ? 'btn-premium' : 'btn-outline' ?>"
                            >
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <?php $baseQuery['page'] = $currentPage + 1; ?>
                            <a class="btn btn-outline" href="<?= BASE_URL ?>products?<?= htmlspecialchars(http_build_query($baseQuery)) ?>">Trang sau →</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
