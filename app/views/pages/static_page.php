<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container animate-fade-up" style="padding: 60px 20px; min-height: 50vh;">
    <h1 style="color: var(--primary); border-bottom: 2px solid var(--primary); display: inline-block; padding-bottom: 10px; margin-bottom: 30px;">
        <?= htmlspecialchars($pageName) ?>
    </h1>
    
    <div style="background: white; padding: 40px; border-radius: var(--radius-lg); box-shadow: 0 4px 15px rgba(0,0,0,0.05); line-height: 1.8; color: #444; font-size: 1.1rem;">
        <p>Chào mừng bạn đến với trang <strong><?= htmlspecialchars($pageName) ?></strong> của Nhà thuốc 1985.</p>
        <p>Nội dung chi tiết cho phần này đang được chúng tôi xây dựng và sẽ sớm ra mắt trong thời gian tới để phục vụ bạn tốt hơn.</p>
        <p>Cảm ơn bạn đã tin tưởng và đồng hành cùng hệ thống Nhà thuốc 1985!</p>
        
        <div style="margin-top: 30px;">
            <a href="<?= BASE_URL ?>" class="btn btn-premium" style="display: inline-flex; align-items: center; gap: 8px;">
                <span>←</span> Quay lại Trang Chủ
            </a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
