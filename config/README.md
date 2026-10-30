# ⚙️ `config/` — Configuración de la aplicación

> **Navegación:** [README](../README.md) · [QUICKSTART](../QUICKSTART.md) · [app/core](../app/core/README.md) · [docker.md](../docker.md)

Ajustes de entorno y arranque de la BD **fuera** de `public/` (no se sirven por HTTP).

---

## Archivos

| Archivo | Rol |
|---------|-----|
| `Config.php` | Carga el `.env` de la raíz del proyecto y expone `Config::get('CLAVE', $default)` |
| `database.php` | Variables PDO (`$DB_DSN`, `$DB_USER`, `$DB_PASSWORD`) usadas por `Database` |
| `setup.php` | Inicialización / comprobaciones de esquema (según despliegue) |

El subject pide un **setup** que cree la base al instalar; en Docker el esquema se aplica sobre todo con [database/init.sql](../database/README.md) al crear el volumen MySQL.

---

## `.env` (raíz del repo)

No se versiona (`.gitignore`). Ejemplo de claves:

| Clave | Uso |
|-------|-----|
| `APP_URL` | Enlaces absolutos (emails, assets) |
| `DB_HOST` / `DB_NAME` / `DB_USER` / `DB_PASS` | MySQL (en compose el host suele ser `db`) |
| `MAIL_HOST` / `MAIL_PORT` / `MAIL_USER` / `MAIL_PASS` / `MAIL_FROM` | SMTP |

```bash
chmod 644 .env
docker-compose restart web
```

Detalle práctico: [QUICKSTART.md](../QUICKSTART.md).

---

## Cómo se carga

1. Front controller o bootstrap incluye `Config`.  
2. `Config` lee `/.env` (ruta relativa desde `config/`).  
3. `Database` hace `require` de `database.php` y abre PDO.

---

## Enlaces

- [DATABASE_INITIALIZATION.md](../DATABASE_INITIALIZATION.md)  
- [sql.md](../sql.md) · [php.md](../php.md)

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
