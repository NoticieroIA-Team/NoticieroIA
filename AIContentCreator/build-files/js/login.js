// Script para la página de login
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        // Por ahora, redirigir directamente a home
        // En el futuro, aquí iría la lógica de autenticación
        window.location.href = '/home';
    });
});

