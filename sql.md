# 🗄️ SQL / MySQL en Camagru

[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://dev.mysql.com/doc/)
[![PDO](https://img.shields.io/badge/PDO-Prepared_Statements-777BB4?style=flat-square)](https://www.php.net/manual/en/book.pdo.php)
[![42](https://img.shields.io/badge/42-Málaga-00babc?style=flat-square&logo=42)](https://www.42malaga.com/)

> **Navegación:** [README](README.md) · [database/](database/README.md) · [models](app/models/README.md) · [MySQL docs](https://dev.mysql.com/doc/)

---

## 1. Motor

MySQL **8.0** en Docker. Acceso solo mediante **PDO** y sentencias preparadas (eval: no frameworks, sí SQL).

---

## 2. Esquema

Definido en `database/init.sql`:

- Unicidad de `username` / `email`  
- Tokens de verificación y reset  
- `likes` con UNIQUE (`image_id`, `user_id`)  
- CASCADE al borrar usuario o imagen  

Ver [DATABASE_INITIALIZATION.md](DATABASE_INITIALIZATION.md).

---

## 3. Ejemplo seguro

```sql
SELECT * FROM users WHERE email = ?;
-- params: [$email]
```

Nunca:

```sql
-- MAL
"SELECT * FROM users WHERE email = '$email'"
```

---

## 4. Enlaces

- [InnoDB](https://dev.mysql.com/doc/refman/8.0/en/innodb-storage-engine.html)  
- [Foreign keys](https://dev.mysql.com/doc/refman/8.0/en/create-table-foreign-keys.html)  
- [PDO MySQL](https://www.php.net/manual/en/ref.pdo-mysql.php)

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
