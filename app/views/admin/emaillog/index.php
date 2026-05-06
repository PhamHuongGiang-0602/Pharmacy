<?php include __DIR__ . '/../../layout/header.php'; ?>

<style>
.email-log-card {
    background: var(--glass-bg, #fff);
    border: 1px solid var(--border, rgba(0,0,0,0.1));
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}
.table-wrapper {
    overflow-x: auto;
}
.email-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.95rem;
}
.email-table th {
    text-align: left;
    padding: 12px 15px;
    background: #f8fafc;
    color: #475569;
    font-weight: 600;
    border-bottom: 2px solid #e2e8f0;
}
.email-table td {
    padding: 12px 15px;
    border-bottom: 1px solid #e2e8f0;
    vertical-align: middle;
}
.badge {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}
.badge-sent { background: #e0e7ff; color: #4338ca; }
.badge-opened { background: #dcfce7; color: #15803d; }
.badge-failed { background: #fee2e2; color: #b91c1c; }
.stat-box {
    background: #f8fafc;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid var(--blue);
}
</style>

<div class="container section animate-fade-up">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 class="section-title" style="margin: 0;">📧 Theo dõi Email Tự động</h1>
        <a href="<?= BASE_URL ?>admin/dashboard" class="btn btn-secondary">Quay lại Dashboard</a>
    </div>

    <!-- Stats -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">
        <div class="stat-box" style="border-left-color: #4338ca;">
            <div style="color: #64748b; font-size: 0.9rem;">Tổng email đã gửi</div>
            <div style="font-size: 1.5rem; font-weight: bold; color: #1e293b;"><?= number_format($stats['total']) ?></div>
        </div>
        <div class="stat-box" style="border-left-color: #15803d;">
            <div style="color: #64748b; font-size: 0.9rem;">Email đã được mở</div>
            <div style="font-size: 1.5rem; font-weight: bold; color: #1e293b;"><?= number_format($stats['opened']) ?></div>
        </div>
        <div class="stat-box" style="border-left-color: #b91c1c;">
            <div style="color: #64748b; font-size: 0.9rem;">Gửi thất bại</div>
            <div style="font-size: 1.5rem; font-weight: bold; color: #1e293b;"><?= number_format($stats['failed']) ?></div>
        </div>
    </div>

    <div class="email-log-card">
        <div class="table-wrapper">
            <table class="email-table">
                <thead>
                    <tr>
                        <th>ID Tracking</th>
                        <th>Loại Email</th>
                        <th>Người nhận</th>
                        <th>Tiêu đề (Subject)</th>
                        <th>Trạng thái</th>
                        <th>Thời gian gửi</th>
                        <th>Thời gian mở</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: #64748b; padding: 30px;">Chưa có dữ liệu email nào được gửi đi.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td style="font-family: monospace; color: #64748b; font-size: 0.85rem;">
                                <?= htmlspecialchars(substr($log['tracking_id'], 0, 15)) ?>...
                            </td>
                            <td>
                                <?php 
                                    $type = explode('\\', $log['email_type']);
                                    echo htmlspecialchars(end($type));
                                ?>
                            </td>
                            <td style="font-weight: 500;"><?= htmlspecialchars($log['recipient']) ?></td>
                            <td style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($log['subject']) ?>">
                                <?= htmlspecialchars($log['subject']) ?>
                            </td>
                            <td>
                                <?php if ($log['status'] === 'opened'): ?>
                                    <span class="badge badge-opened">Đã mở</span>
                                <?php elseif ($log['status'] === 'sent'): ?>
                                    <span class="badge badge-sent">Đã gửi</span>
                                <?php else: ?>
                                    <span class="badge badge-failed">Thất bại</span>
                                <?php endif; ?>
                            </td>
                            <td style="color: #64748b; font-size: 0.9rem;">
                                <?= date('d/m/Y H:i:s', strtotime($log['sent_at'])) ?>
                            </td>
                            <td style="color: #64748b; font-size: 0.9rem;">
                                <?= $log['opened_at'] ? date('d/m/Y H:i:s', strtotime($log['opened_at'])) : '-' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../layout/footer.php'; ?>
