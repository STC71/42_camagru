# 🧩 `app/views/partials/` — Header y footer

> **Navegación:** [views/](../README.md) · [I18n](../../core/README.md) · [css.md](../../../css.md)

## Componentes

| Archivo | Contenido |
|---------|-----------|
| `header.php` | Logo, badge de sesión (visitante / usuario), **banderas de idioma**, menú (Inicio, Galería, Editor, Perfil, Ajustes, Salir / Entrar, Registro) |
| `footer.php` | Leyenda del proyecto Outer Core (traducible; nombres propios sin traducir) |

## Por qué existen

Un solo sitio para navegación e i18n: cualquier cambio de menú o idioma se propaga a todas las páginas que incluyen estos partials.

## Relacionado

- Traducciones: `app/lang/*.php`  
- Estilos cabecera: clases `.header-bar`, `.lang-switcher` en `public/css/style.css`

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
