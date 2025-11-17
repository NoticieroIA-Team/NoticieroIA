<h1>Login</h1>

<?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post" action="<?= BASE_URL ?>auth/login">
    <div class="input-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>
    </div>

    <div class="input-group">
        <label for="password">Contraseña</label>
        <input type="password" name="password" id="password" required>
    </div>

    <button class="btn btn-primary" type="submit">Entrar</button>
</form>
