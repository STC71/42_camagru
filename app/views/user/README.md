# 👤 `app/views/user/` — Perfil y ajustes

> **Navegación:** [views/](../README.md) · [UserController](../../controllers/README.md) · [QUICKSTART](../../../QUICKSTART.md)

## Estado actual (implementado)

| Vista | Función |
|-------|---------|
| `profile.php` | Datos del usuario + grid de **mis fotos** |
| `settings.php` | Editar perfil, cambiar password, preferencia de email de comentarios, **eliminar cuenta** (checkbox borrar fotos + confirmar password) |

> Documentación antigua podía marcar estas pantallas como “en desarrollo”; el código actual las sirve vía `UserController`.

## Seguridad

- Solo sesión autenticada.  
- CSRF en todos los POST.  
- Baja de cuenta: verifica contraseña; opcionalmente `Image::deleteAllByUser` antes del `DELETE` del usuario (CASCADE en BD).

## Enlaces

- [models/User](../../models/README.md) · [models/Image](../../models/README.md)  
- [partials](../partials/README.md)

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
