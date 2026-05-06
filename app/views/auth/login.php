<?php require_once 'app/views/layout/auth_header.php'; ?>

<form class="auth-form" action="<?= BASE_URL ?>auth/handleLogin" method="POST">
    <div class="input-group">
        <input type="text" name="credential" placeholder="Email hoặc Tên đăng nhập" maxlength="100" required>
    </div>
    
    <div class="input-group">
        <input type="password" name="password" placeholder="Mật khẩu" maxlength="100" required>
        <a href="<?= BASE_URL ?>auth/forgotPassword" class="forgot-pw-link">Quên mật khẩu?</a>
    </div>

    <button type="submit" class="btn-primary">Đăng nhập</button>
</form>

<div class="auth-links">
    Chưa có tài khoản? <a href="<?= BASE_URL ?>auth/register">Đăng ký ngay</a>
</div>

<?php require_once 'app/views/layout/auth_footer.php'; ?>
