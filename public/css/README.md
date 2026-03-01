# 🎨 Directorio `css/` - Estilos

## 🎯 ¿Qué es y para qué sirve?

Aquí está el CSS que hace que la aplicación se vea bien: colores, tamaños, posiciones, animaciones, etc.

**¿Por qué existe?** Para separar el diseño visual del contenido HTML. Así puedes cambiar colores o diseño sin tocar las vistas.

**¿Para qué sirve?**
- Define cómo se VE la aplicación (colores, tipografías, espaciados)
- Hace el diseño **responsive** (se adapta a móvil, tablet, desktop)
- Añade animaciones y transiciones suaves
- Organiza el layout (grid para galería, flexbox para navegación)

## 📋 Contenido

**`style.css`** - Hoja de estilos principal con todo el CSS del proyecto

**¿Qué contiene?**
- **Reset:** Elimina estilos por defecto del navegador
- **Variables:** Colores y tamaños reutilizables
- **Layout:** Estructura general (header, main, footer)
- **Componentes:** Botones, cards, formularios, modals
- **Responsive:** Media queries para adaptar a diferentes pantallas
- **Utilities:** Clases de ayuda (text-center, mt-1, hidden)

### Variables CSS (Custom Properties)
```css
:root {
    --primary-color: #00babc;
    --secondary-color: #777BB4;
    --danger-color: #dc3545;
    --success-color: #28a745;
    --text-color: #333;
    --bg-color: #f8f9fa;
}

.btn-primary {
    background-color: var(--primary-color);
}
```

### Flexbox y Grid
```css
/* Flex para navegación */
nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Grid para galería */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}
```

## 🛠️ Metodología CSS

### BEM (Block Element Modifier)
```css
/* Block */
.card { }

/* Element */
.card__header { }
.card__body { }
.card__footer { }

/* Modifier */
.card--featured { }
.card--dark { }
```

### Organización del Código
```css
/* === RESET === */
* { margin: 0; padding: 0; box-sizing: border-box; }

/* === VARIABLES === */
:root { --primary: #00babc; }

/* === LAYOUT === */
body { }
header { }
main { }
footer { }

/* === COMPONENTS === */
.btn { }
.card { }
.modal { }

/* === UTILITIES === */
.text-center { }
.mt-1 { }
.hidden { }

/* === RESPONSIVE === */
@media (max-width: 768px) { }
```

## 🎨 Componentes Principales

### Botones
```css
.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.btn-primary { background: var(--primary-color); }
.btn-danger { background: var(--danger-color); }
```

### Cards
```css
.card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.3s ease;
}

.card:hover {
    transform: scale(1.05);
}
```

### Formularios
```css
.form-group {
    margin-bottom: 1rem;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.form-control {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(0, 186, 188, 0.1);
}
```

## 🚀 Optimización CSS

### 1. Minificación
```bash
# Usando cssnano
npx cssnano style.css style.min.css

# Usando clean-css
npx clean-css -o style.min.css style.css
```

### 2. Critical CSS
```html
<!-- Inline critical CSS -->
<style>
    /* Above-the-fold styles */
    header, nav, .hero { ... }
</style>

<!-- Lazy load non-critical -->
<link rel="preload" href="style.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
```

### 3. Remove Unused CSS
```bash
# Usando PurgeCSS
npx purgecss --css style.css --content *.php --output purged.css
```

## 📺 Videos Educativos en Español

### CSS Fundamentals
- [CSS desde CERO - Curso Completo](https://www.youtube.com/watch?v=OWKXEJN67FE) - Por MoureDev
- [CSS Grid Layout - Tutorial](https://www.youtube.com/watch?v=QBOUSrMqlSQ) - Por FalconMasters
- [Flexbox en 15 Minutos](https://www.youtube.com/watch?v=JX2FTvE4yCI) - Por MoureDev

### CSS Avanzado
- [CSS Variables - Custom Properties](https://www.youtube.com/watch?v=oZPR_78wCnY) - Por MiduDev
- [Metodología BEM en CSS](https://www.youtube.com/watch?v=YaAkV--25fg) - Por FalconMasters
- [Animaciones CSS](https://www.youtube.com/watch?v=SgmNxE9lWcY) - Por DesignCourse

### Responsive Design
- [Responsive Design desde Cero](https://www.youtube.com/watch?v=2KL-z9A56SQ) - Por FaztCode
- [Mobile First Design](https://www.youtube.com/watch?v=WZLhHBtkIz0) - Por EDteam
- [Media Queries Explicadas](https://www.youtube.com/watch?v=yU7jJ3NbPdA) - Por FalconMasters

### Performance
- [Optimización de CSS](https://www.youtube.com/watch?v=Mv8wQ-DzG4s) - Por Google Developers
- [Critical CSS](https://www.youtube.com/watch?v=RWTRtWUssNo) - Por Web.dev

## 🔗 Enlaces Relacionados

- [Volver a public/](../README.md)
- [JavaScript](../js/README.md)
- [Vistas HTML](../../app/views/README.md)

---

[⬆ Volver al README principal](../../README.md)
