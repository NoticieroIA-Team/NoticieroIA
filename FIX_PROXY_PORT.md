# 🔧 Fix: Cambiar Puerto del Proxy de 80 a 3000

## ⚠️ Problema Detectado

En la sección **Domains**, el proxy está configurado así:

```
https://digital-digital-noticieroia.owolqd.easypanel... → http://digital_digital_noticieroia:80/
```

El puerto **80** es incorrecto. Debe ser **3000**.

## ✅ Solución

### Paso 1: Editar el Dominio

1. En la sección **Domains**, encuentra la entrada del dominio
2. Haz clic en el **ícono de lápiz** (✏️) a la derecha del dominio
3. Se abrirá un formulario de edición

### Paso 2: Cambiar el Puerto

En el formulario de edición, busca:
- **Target Port** o **Port**
- **Internal Port**
- **Backend Port**

Cambia el valor de **80** a **3000**

### Paso 3: Guardar y Deploy

1. Guarda los cambios
2. Haz clic en el botón verde **"Deploy"**
3. Espera a que termine el despliegue

### Paso 4: Verificar

Después del deploy, la configuración debería verse así:

```
https://digital-digital-noticieroia.owolqd.easypanel... → http://digital_digital_noticieroia:3000/
```

## ✅ Verificación Final

1. Accede a: `https://digital-digital-noticieroia.owolqd.easypanel.host/`
   - Debería mostrar la página de login

2. Prueba el endpoint: `https://digital-digital-noticieroia.owolqd.easypanel.host/test`
   - Debería devolver JSON con información del servidor

3. Revisa los logs en EasyPanel
   - Debería mostrar: `✅ Servidor corriendo en http://0.0.0.0:3000`

## 📝 Nota

El puerto **80** es el puerto HTTP estándar, pero EasyPanel lo maneja internamente. Tu aplicación Node.js debe correr en el puerto **3000** y el proxy debe redirigir desde el dominio público al puerto 3000 del contenedor.

