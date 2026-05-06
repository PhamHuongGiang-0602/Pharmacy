<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container section animate-fade-up">
    <!-- Breadcrumb -->
    <div style="margin-bottom: 20px; font-size: 0.9rem; color: #666;">
        <a href="<?= BASE_URL ?>" style="color: var(--primary); text-decoration: none;">Trang chủ</a> &raquo;
        <a href="<?= BASE_URL ?>products" style="color: var(--primary); text-decoration: none;">Sản phẩm</a> &raquo;
        <?= htmlspecialchars($product['product_name']) ?>
    </div>

    <div class="glass-card" style="padding: 30px; display: grid; grid-template-columns: 1fr 1.5fr; gap: 40px;">
        <!-- Hình ảnh sản phẩm -->
        <div style="background: #fff; padding: 20px; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; overflow: hidden; border: 1px solid var(--border);">
            <?php
                $rawImage = trim((string)($product['image_url'] ?? ''));
                $isRemoteImage = preg_match('#^https?://#i', $rawImage) === 1;
                $localImageFile = __DIR__ . '/../../../public/img/products/' . $rawImage;
                if ($isRemoteImage) {
                    $imageSrc = $rawImage;
                } elseif ($rawImage !== '' && file_exists($localImageFile)) {
                    $imageSrc = BASE_URL . 'public/img/products/' . rawurlencode($rawImage);
                } else {
                    $imageSrc = 'https://picsum.photos/seed/pharmacy-detail-' . (int)($product['product_id'] ?? 0) . '/800/800';
                }
            ?>
            <img src="<?= $imageSrc ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" style="max-width: 100%; max-height: 400px; object-fit: contain;" onerror="this.onerror=null;this.src='<?= BASE_URL ?>public/img/placeholder.png';">
        </div>
        
        <!-- Thông tin chi tiết -->
        <div>
            <h1 style="font-size: 1.8rem; margin-bottom: 15px; color: var(--text-color);"><?= htmlspecialchars($product['product_name']) ?></h1>
            
            <div style="margin-bottom: 20px;">
                <span style="font-size: 1.8rem; font-weight: bold; color: var(--primary);">
                    <?= number_format($product['current_price'] ?? $product['price']) ?>đ
                </span>
                <?php if (!empty($product['discount_percent'])): ?>
                    <span style="text-decoration: line-through; color: #999; margin-left: 15px; font-size: 1.2rem;">
                        <?= number_format($product['price']) ?>đ
                    </span>
                    <span style="background: #e65100; color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 0.9rem; margin-left: 10px;">
                        -<?= $product['discount_percent'] ?>%
                    </span>
                <?php endif; ?>
            </div>
            
            <div style="margin-bottom: 30px; line-height: 1.6; color: #555;">
                <p><?= nl2br(htmlspecialchars($product['description'] ?? 'Đang cập nhật mô tả...')) ?></p>
            </div>
            
            <div style="display: flex; gap: 15px; margin-bottom: 30px;">
                <div style="display: flex; border: 1px solid var(--border); border-radius: var(--radius-sm); overflow: hidden; width: 120px;">
                    <button type="button" onclick="const q=document.getElementById('detailQty'); if(q.value>1) q.value--;" style="width: 40px; background: #f5f5f5; border: none; font-size: 1.2rem; cursor: pointer;">-</button>
                    <input type="number" id="detailQty" value="1" min="1" style="width: 40px; text-align: center; border: none; border-left: 1px solid var(--border); border-right: 1px solid var(--border); font-weight: bold;">
                    <button type="button" onclick="const q=document.getElementById('detailQty'); q.value++;" style="width: 40px; background: #f5f5f5; border: none; font-size: 1.2rem; cursor: pointer;">+</button>
                </div>
                <button onclick="addToCart(<?= $product['product_id'] ?>, document.getElementById('detailQty').value)" class="btn btn-premium" style="flex: 1; font-size: 1.1rem;">🛒 Thêm vào giỏ hàng</button>
            </div>
            
            <hr style="border: 0; border-top: 1px solid var(--border); margin: 20px 0;">
            
            <ul style="list-style: none; padding: 0; line-height: 2; font-size: 0.95rem; color: #666;">
                <li><strong style="color: #333;">Thương hiệu:</strong> <?= htmlspecialchars($product['brand'] ?? 'Đang cập nhật') ?></li>
                <li><strong style="color: #333;">Nhà cung cấp:</strong> <?= htmlspecialchars($product['manufacturer'] ?? 'Đang cập nhật') ?></li>
                <li><strong style="color: #333;">Trạng thái:</strong> <?= ($product['stock_quantity'] ?? 0) > 0 ? '<span style="color: var(--green);">Còn hàng</span>' : '<span style="color: var(--red);">Hết hàng</span>' ?></li>
            </ul>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
