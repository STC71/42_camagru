# 🧩 Partials - Componentes Reutilizables

## 🎯 ¿Qué son y para qué sirven?

Son **fragmentos de HTML compartidos** que se usan en todas las páginas: el header y el footer.

**¿Por qué existen?** Para no copiar y pegar el mismo código 20 veces. Si cambias el menú, lo cambias una vez aquí y se actualiza en todas las páginas.

## 📋 Los 2 Componentes

| Archivo | ¿Qué contiene? | ¿Dónde se usa? |
|---------|----------------|----------------|
| `header.php` | Cabecera, menú de navegación, mensajes flash | Todas las páginas |
| `footer.php` | Pie de página, copyright, scripts JavaScript | Todas las páginas |

## 🔧 ¿Cómo funcionan?

**Cada vista los incluye al principio y al final:**

```php
<?php require_once 'app/views/partials/header.php'; ?>
<!-- Contenido específico de la página -->
<?php require_once 'app/views/partials/footer.php'; ?>
```

**Ventajas:**
- ✅ Cambias el menú una vez y se actualiza en TODAS las páginas
- ✅ Consistencia visual garantizada
- ✅ Menos código duplicado
- ✅ Más fácil de mantener

## 🔧 Uso

### header.php
**Contiene:**
- `<!DOCTYPE html>` y apertura de `<html>`
- `<head>` con meta tags, title, CSS
- Barra de navegación
- Menú diferente para usuarios autenticados/invitados
- Mensajes flash (success, error, info)

**Uso:**
```php
<?php require_once 'app/views/partials/header.php'; ?>
<!-- Contenido de la página -->
```

### footer.php
**Contiene:**
- Información de copyright
- Enlaces a redes sociales
- Scripts JavaScript
- Cierre de `</body>` y `</html>`

**Uso:**
```php
<!-- Contenido de la página -->
<?php require_once 'app/views/partials/footer.php'; ?>
```

## 📺 Videos Educativos en Español

### Templates y Components
- [Include y Require en PHP](https://www.youtube.com/watch?v=NUvj4Fzg5j4) - Por HolaMundo
- [Componentes Reutilizables](https://www.youtube.com/watch?v=v0cpNQ0F7Nw) - Por Código Facilito
- [Layout System en PHP](https://www.youtube.com/watch?v=1_aSL6CcAxs) - Por FaztCode

---

[⬆ Volver a views/](../README.md) | [⬆ README principal](../../../README.md)
