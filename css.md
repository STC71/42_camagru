# 🎨 CSS en Camagru

[![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)](https://developer.mozilla.org/es/docs/Web/CSS)
[![42](https://img.shields.io/badge/42-Málaga-00babc?style=flat-square&logo=42)](https://www.42malaga.com/)

> **Navegación:** [README](README.md) · [public/css](public/css/README.md) · [MDN CSS](https://developer.mozilla.org/es/docs/Web/CSS)

Hoja principal: `public/css/style.css`.

---

## 1. Variables y botones

```css
:root {
    --primary-color: /* color marca */;
    --danger-color: /* borrados / zona peligrosa */;
}

.btn {
    /* base común */
}
.btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
```

---

## 2. Cabecera e idiomas

```css
.header-bar {
    display: flex;
    align-items: center;
    justify-content: center; /* contenido centrado */
    flex-wrap: wrap;
    gap: /* espacio banderas / nav */;
}

.lang-switcher a {
    /* solo banderas, sin texto del idioma */
}
```

---

## 3. Zona de upload compacta

```css
.upload-area {
    max-width: 480px;
    margin: 0 auto;
    padding: 1.25rem 1.5rem;
    min-height: 120px;
    border: 2px dashed var(--light);
    position: relative;
}

.upload-file-input {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
    z-index: 5;
}
```

---

## 4. Features home (2×2)

```css
.features-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}
@media (max-width: 768px) {
    .features-grid {
        grid-template-columns: 1fr;
    }
}
```

---

## 5. Enlaces MDN

- [Flexbox](https://developer.mozilla.org/es/docs/Web/CSS/CSS_flexible_box_layout)  
- [Grid](https://developer.mozilla.org/es/docs/Web/CSS/CSS_grid_layout)  
- [Responsive](https://developer.mozilla.org/es/docs/Learn/CSS/CSS_layout/Responsive_Design)

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
