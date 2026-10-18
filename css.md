# 🎨 CSS en Camagru

[![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/CSS)
[![Responsive](https://img.shields.io/badge/Responsive-Design-1572B6?style=flat-square)](https://developer.mozilla.org/en-US/docs/Learn/CSS/CSS_layout/Responsive_Design)
[![42](https://img.shields.io/badge/42-Málaga-00babc?style=flat-square&logo=42)](https://www.42malaga.com/)

---

## 1. ¿Qué es CSS y por qué es importante?

**CSS** (Cascading Style Sheets) es el lenguaje que define **cómo se ve** una página web: colores, tamaños, disposición de los elementos, animaciones, etc.

En Camagru lo usamos para:

- Dar una apariencia limpia y moderna a la aplicación
- Hacer que se vea bien en móvil, tablet y escritorio (responsive)
- Resaltar botones, formularios y la zona del editor
- Dar feedback visual (hover, botones deshabilitados, etc.)

> **Analogía cotidiana:**  
> Si HTML es la estructura de una casa (paredes, puertas, ventanas), **CSS es la decoración**: el color de las paredes, los muebles y la iluminación.

Documentación oficial: [MDN – CSS](https://developer.mozilla.org/en-US/docs/Web/CSS)

---

## 2. Enfoque del proyecto

| Decisión | Motivo |
|----------|--------|
| **CSS puro** (sin Bootstrap, Tailwind, Sass…) | Cumplir el subject de 42 y aprender los fundamentos |
| Archivos en `/public/css/` | Separación clara de responsabilidades |
| Mobile-first / responsive | El subject exige buen comportamiento en móvil |

---

## 3. Características CSS que utilizamos

| Característica | Uso en Camagru | Documentación |
|----------------|----------------|---------------|
| **Flexbox** | Barras de navegación, formularios, tarjetas de la galería | [MDN Flexbox](https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_flexible_box_layout) |
| **CSS Grid** | Distribución del editor (webcam + stickers + miniaturas) | [MDN Grid](https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_grid_layout) |
| **Media Queries** | Adaptar el layout a pantallas pequeñas | [MDN Media Queries](https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_media_queries) |
| **Custom Properties** | Colores y espaciados consistentes | [MDN Custom Properties](https://developer.mozilla.org/en-US/docs/Web/CSS/Using_CSS_custom_properties) |
| **Transitions** | Suavizar cambios de color y tamaño en botones | [MDN Transitions](https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_transitions) |
| **Position + z-index** | Superponer el sticker sobre el vídeo en el editor | [MDN Position](https://developer.mozilla.org/en-US/docs/Web/CSS/position) |

---

## 4. Requisitos del subject cumplidos

- Diseño usable y limpio
- Funciona correctamente en **móvil**
- Compatible con Firefox ≥ 41 y Chrome ≥ 46
- No depende de frameworks externos

---

## 5. Ejemplos reales del proyecto

### 5.1 Variables CSS (tema consistente)

```css
:root {
    --color-primary: #00babc;   /* Color 42 */
    --color-dark: #1a1a2e;
    --color-light: #f5f5f5;
    --radius: 8px;
    --shadow: 0 2px 8px rgba(0,0,0,0.15);
}
```

### 5.2 Layout del editor con Grid

```css
.editor-layout {
    display: grid;
    grid-template-columns: 1fr 280px;
    gap: 1.5rem;
}

@media (max-width: 768px) {
    .editor-layout {
        grid-template-columns: 1fr; /* una sola columna en móvil */
    }
}
```

### 5.3 Botón deshabilitado (sin sticker seleccionado)

```css
.btn-capture:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background-color: #999;
}
```

### 5.4 Tarjeta de la galería con Flexbox

```css
.gallery-card {
    display: flex;
    flex-direction: column;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
}

.gallery-card img {
    width: 100%;
    height: auto;
    object-fit: cover;
}
```

### 5.5 Media query para móvil

```css
@media (max-width: 600px) {
    .navbar {
        flex-direction: column;
        gap: 0.5rem;
    }

    .gallery-grid {
        grid-template-columns: 1fr;
    }
}
```

---

## 6. Buenas prácticas aplicadas

- Variables CSS para no repetir colores y medidas
- Preferimos Flexbox y Grid frente a floats antiguos
- Mobile-first: primero pensamos en pantalla pequeña y luego ampliamos
- Evitamos `!important` siempre que es posible
- Clases con nombres descriptivos

---

## 7. Recursos oficiales recomendados

| Recurso | Enlace |
|---------|--------|
| CSS Reference (MDN) | https://developer.mozilla.org/en-US/docs/Web/CSS/Reference |
| Flexbox | https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_flexible_box_layout |
| Grid | https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_grid_layout |
| Responsive Design | https://developer.mozilla.org/en-US/docs/Learn/CSS/CSS_layout/Responsive_Design |
| Media Queries | https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_media_queries |
| Custom Properties | https://developer.mozilla.org/en-US/docs/Web/CSS/Using_CSS_custom_properties |

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
