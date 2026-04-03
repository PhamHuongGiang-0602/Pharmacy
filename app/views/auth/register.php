
<!DOCTYPE html>
<html lang="vi">
<head>
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/main.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/auth.css">
</head>
<body class="auth-page">
    <?php include 'app/views/layout/auth_header.php'; ?>

    <main class="auth-main">
        <?php if ($errors): ?>
            <div class="form-error show">
                <?php foreach ($errors as $error): ?>
                    <p>⚠️ <?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form class="auth-form" action="<?= BASE_URL ?>auth/handleRegister" method="POST">
            <input type="text" name="fullName" value="<?= htmlspecialchars($oldInput['fullName'] ?? '') ?>" required>
            </form>
    </main>

    <?php include 'app/views/layout/auth_footer.php'; ?>
    </body>
</html>