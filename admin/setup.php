<?php
/**
 * CREADOR DEL PRIMER USUARIO — ejecutar UNA sola vez.
 * Solo funciona si la tabla `usuario` está vacía.
 * Cuando termines: BORRA este archivo.
 */
require_once __DIR__ . '/config/init.php';

$yaHay = (int) db()->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
$msg = '';
$ok  = false;

if ($yaHay > 0) {
    $msg = 'Ya existe al menos un usuario. Por seguridad, borra este archivo (admin/setup.php).';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombres = trim($_POST['nombres'] ?? '');
    $apePat  = trim($_POST['apellido'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $pass    = (string) ($_POST['password'] ?? '');

    if ($nombres === '' || $email === '' || strlen($pass) < 6) {
        $msg = 'Nombres, correo y contraseña (mínimo 6 caracteres) son obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = 'El correo no es válido.';
    } else {
        $stmt = db()->prepare(
            'INSERT INTO usuarios
               (nombres, ap_paterno, email, password_hash, rol, created_at)
             VALUES (?, ?, ?, ?, ?, NOW())'
        );
        $stmt->execute([
            $nombres,
            $apePat ?: '-',
            $email,
            password_hash($pass, PASSWORD_DEFAULT),
            'admin',
        ]);
        $ok  = true;
        $msg = 'Usuario creado. Ahora BORRA admin/setup.php y entra por admin/login.php';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear primer usuario</title>
    <link rel="stylesheet" href="<?= e(base_url('dist/css/adminlte.min.css')) ?>">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo"><b>DDP</b> · Primer usuario</div>
    <div class="card">
        <div class="card-body">
            <?php if ($msg): ?>
                <div class="alert <?= $ok ? 'alert-success' : 'alert-warning' ?>"><?= e($msg) ?></div>
            <?php endif; ?>

            <?php if (!$ok && $yaHay === 0): ?>
            <form method="post">
                <div class="form-group">
                    <label>Nombres</label>
                    <input class="form-control" name="nombres" value="<?= e($_POST['nombres'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Apellido</label>
                    <input class="form-control" name="apellido" value="<?= e($_POST['apellido'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Correo (con este ingresas)</label>
                    <input type="email" class="form-control" name="email" value="<?= e($_POST['email'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Contraseña (mín. 6)</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <button class="btn btn-primary btn-block">Crear usuario</button>
            </form>
            <?php endif; ?>

            <a class="btn btn-link btn-block mt-2" href="<?= e(base_url('login.php')) ?>">Ir al login</a>
        </div>
    </div>
</div>
</body>
</html>
