<h1>Registro</h1>

<?php if (!empty($errores)): ?>
    <div class="error">
        <ul style="margin:0; padding-left:1.25rem;">
            <?php foreach ($errores as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="<?= BASE_URL ?>auth/register">
    <div class="input-group">
        <label for="nombre">Nombre</label>
        <input
            type="text"
            name="nombre"
            id="nombre"
            required
            value="<?= htmlspecialchars($nombre ?? '') ?>"
        >
    </div>

    <div class="input-group">
        <label for="email">Email</label>
        <input
            type="email"
            name="email"
            id="email"
            required
            value="<?= htmlspecialchars($email ?? '') ?>"
        >
    </div>

    <div class="input-group">
        <label for="password">Contraseña</label>
        <input type="password" name="password" id="password" required>
    </div>

    <div class="input-group">
        <label for="confirm_password">Repite la contraseña</label>
        <input type="password" name="confirm_password" id="confirm_password" required>
    </div>

    <button class="btn btn-primary" type="submit">Registrarme</button>
</form>
