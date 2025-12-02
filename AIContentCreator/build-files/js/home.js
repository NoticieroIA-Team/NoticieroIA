// Script para la página home
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('generoForm');
    const mensaje = document.getElementById('mensaje');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const data = {
            tema: formData.get('tema'),
            descripcion: formData.get('descripcion'),
            frecuencia: formData.get('frecuencia'),
            cantidad: parseInt(formData.get('cantidad')),
            idioma: formData.get('idioma'),
            fuentes: ['BBC News', 'Reuters'] // Por defecto
        };

        try {
            const response = await fetch('/api/generos', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                mensaje.textContent = 'Género guardado correctamente';
                mensaje.className = 'success';
                form.reset();
            } else {
                mensaje.textContent = 'Error: ' + (result.error || 'Error al guardar');
                mensaje.className = 'error';
            }
        } catch (error) {
            mensaje.textContent = 'Error de conexión: ' + error.message;
            mensaje.className = 'error';
        }
    });
});

