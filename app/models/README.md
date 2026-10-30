# 🗄️ `app/models/` — Acceso a datos

> **Navegación:** [app/](../README.md) · [sql.md](../../sql.md) · [database/](../../database/README.md) · [core/Database](../core/README.md)

Cada modelo encapsula SQL de una tabla (o consultas relacionadas) usando el wrapper PDO.

---

## Modelos

| Clase | Tabla | Operaciones principales |
|-------|-------|-------------------------|
| `User.php` | `users` | Registro, verify, login fields, reset token, perfil, notificaciones, `deleteById` |
| `Image.php` | `images` | create, listados, por usuario, delete (fila + fichero), `deleteAllByUser` |
| `Like.php` | `likes` | toggle / conteos |
| `Comment.php` | `comments` | crear, listar por imagen |

El esquema con **FK y `ON DELETE CASCADE`** está en [database/init.sql](../../database/README.md) y [DATABASE_INITIALIZATION.md](../../DATABASE_INITIALIZATION.md).

---

## Patrón de consulta

```php
// Dentro del modelo
$stmt = $this->db->query(
    'SELECT * FROM users WHERE email = ?',
    [$email]
);
return $stmt ? $stmt->fetch() : false;
```

No se concatena input de usuario en el SQL (eval: SQLi).

---

## Ficheros en disco

`Image::delete` / `deleteAllByUser` intentan `unlink` bajo `public/uploads/images/`. La BD puede limpiar filas por CASCADE al borrar el usuario; los archivos hay que borrarlos en código si no se quieren huérfanos.

---

## Enlaces

- [controllers/](../controllers/README.md)  
- [COMPLIANCE.md](../../COMPLIANCE.md) (password_hash, prepared statements)

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
