# 🔐 Vistas de Autenticación

## 🎯 ¿Qué son y para qué sirven?

Las páginas de **login, registro y recuperación de contraseña**.

**¿Por qué existen?** Para gestionar quién puede entrar a la aplicación y cómo recuperar el acceso si olvidas tu contraseña.

## 📋 Las 4 Páginas

| Página | ¿Qué hace? | URL |
|--------|------------|-----|
| `login.php` | Formulario para entrar con email y contraseña | `/login` |
| `register.php` | Formulario para crear cuenta nueva | `/register` |
| `forgot-password.php` | Solicitar recuperación de contraseña | `/forgot-password` |
| `reset-password.php` | Cambiar contraseña con token del email | `/reset-password?token=...` |

## ✨ ¿Qué tiene cada una?

### login.php - Iniciar Sesión
- Formulario con email y contraseña
- Botón "¿Olvidaste tu contraseña?"
- Link para registrarse si no tienes cuenta
- Validación: email válido, campos requeridos

### register.php
- ✅ Formulario de registro completo
- ✅ Validación en tiempo real
- ✅ Indicador de fortaleza de contraseña
- ✅ Verificación de disponibilidad de username/email
- ✅ Mensaje de confirmación
- ✅ Protección CSRF

### forgot-password.php
- ✅ Formulario simple con email
- ✅ Instrucciones claras
- ✅ Mensaje de confirmación
- ✅ Rate limiting visual

### reset-password.php
- ✅ Formulario de nueva contraseña
- ✅ Confirmación de contraseña
- ✅ Validación de token
- ✅ Indicador de fortaleza

## 🛡️ Seguridad

### Protección CSRF
```php
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
```

### Validación de Input
```php
<input type="email" name="email" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
<input type="password" name="password" required minlength="8">
```

### Escape de Datos
```php
<span class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
```

## 📺 Videos Educativos en Español

### Autenticación y Seguridad
- [Sistema de Login en PHP](https://www.youtube.com/watch?v=1bbpKF33b9s) - Por Fazt
- [Autenticación Segura en PHP](https://www.youtube.com/watch?v=BXfqJa7o3Gk) - Por HolaMundo
- [Recuperación de Contraseña](https://www.youtube.com/watch?v=Z7aYKM-qmZA) - Por Código Facilito

### Validación de Formularios
- [Validación HTML5](https://www.youtube.com/watch?v=ua0Z3nHoJDY) - Por Píldoras Informáticas
- [Validación con JavaScript](https://www.youtube.com/watch?v=NdF_vQjrWYg) - Por FalconMasters
- [RegExp para Validación](https://www.youtube.com/watch?v=wfogZfIS03U) - Por MiduDev

---

[⬆ Volver a views/](../README.md) | [⬆ README principal](../../../README.md)
