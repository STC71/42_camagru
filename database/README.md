# 🗄️ `database/` — Esquema MySQL

> **Navegación:** [README](../README.md) · [sql.md](../sql.md) · [DATABASE_INITIALIZATION.md](../DATABASE_INITIALIZATION.md) · [models](../app/models/README.md)

---

## Contenido

| Archivo | Rol |
|---------|-----|
| `init.sql` | Crea BD `camagru` y tablas `users`, `images`, `likes`, `comments` |
| `Dockerfile` | Imagen MySQL 8 que monta `init.sql` en el entrypoint |

En `docker-compose`, el servicio `db` construye este contexto. **Solo se ejecuta init en el primer arranque** del volumen `db_data`. Para regenerar desde cero:

```bash
docker-compose down -v   # ¡borra datos del volumen!
docker-compose up --build -d
```

---

## Tablas (resumen)

| Tabla | Idea |
|-------|------|
| `users` | Credenciales, tokens verify/reset, `notify_comments` |
| `images` | `user_id` + `filename` → fichero en `public/uploads/images/` |
| `likes` | Par único (`image_id`, `user_id`) |
| `comments` | Texto + autor + imagen |

Claves foráneas con **`ON DELETE CASCADE`** (borrar usuario limpia likes/comments/images en BD). Los PNG/GIF en disco se borran desde PHP si se pide explícitamente (Ajustes / delete de foto).

---

## Enlaces

- [app/models/](../app/models/README.md)  
- [COMPLIANCE.md](../COMPLIANCE.md) (PDO EXCEPTION)

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
