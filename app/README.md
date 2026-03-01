# 📦 Directorio `app/` - Aplicación Principal

Este directorio es el **corazón de Camagru**. Aquí vive toda la lógica de la aplicación organizada siguiendo el patrón MVC (Model-View-Controller).

## 🎯 ¿Qué es y para qué sirve?

**app/** contiene el código que hace funcionar la aplicación. Está separado del directorio público por seguridad: nadie puede acceder directamente a estos archivos desde el navegador.

**¿Por qué esta estructura?** Porque separa claramente las responsabilidades:
- Los **Modelos** hablan con la base de datos
- Las **Vistas** muestran información al usuario
- Los **Controladores** coordinan todo
- El **Core** proporciona herramientas compartidas

## 📋 Estructura y Propósito

```
app/
├── controllers/     # Coordinan las peticiones del usuario
├── models/          # Gestionan los datos (usuarios, fotos, likes, comentarios)
├── views/           # Muestran páginas HTML al usuario
└── core/            # Herramientas fundamentales (router, base de datos, email, etc.)
```

## 🔄 Cómo Funciona el Flujo MVC

**Ejemplo:** Usuario sube una foto con sticker

1. **Usuario** hace clic en "Capturar" en el editor
2. **Router** (core/) recibe la petición → `/editor/capture`
3. **EditorController** (controllers/) procesa la foto y el sticker
4. **Image Model** (models/) guarda la foto en la base de datos
5. **Vista** (views/) muestra confirmación de éxito
6. **Usuario** ve su foto en la galería

**¿Por qué esta separación?** Porque cada parte tiene una única responsabilidad:
- Si cambias cómo se ven las cosas → solo tocas **views/**
- Si cambias cómo se guardan datos → solo tocas **models/**
- Si cambias la lógica → solo tocas **controllers/**

## 📂 ¿Qué hay en cada carpeta?

### [controllers/](controllers/) - Los Coordinadores
**¿Qué hace?** Recibe las peticiones del usuario y coordina la respuesta.

**Contiene:**
- `AuthController.php` - Todo lo relacionado con login, registro y seguridad
- `EditorController.php` - Captura de fotos, stickers y generación de GIFs
- `GalleryController.php` - Mostrar fotos, dar likes, comentar
- `HomeController.php` - Página de bienvenida
- `UserController.php` - Perfil y configuración (en desarrollo)

**¿Por qué separado en archivos?** Para que cada controlador se encargue solo de su área.

### [models/](models/) - Los Gestores de Datos
**¿Qué hace?** Se comunica con la base de datos MySQL de forma segura.

**Contiene:**
- `User.php` - Crear usuarios, verificar emails, cambiar contraseñas
- `Image.php` - Guardar fotos, listar galería, eliminar imágenes
- `Like.php` - Dar/quitar likes, contar likes de cada foto
- `Comment.php` - Añadir comentarios, listar comentarios

**¿Por qué es importante?** Porque centraliza TODO el acceso a datos y previene inyecciones SQL.

### [views/](views/) - La Presentación
**¿Qué hace?** Genera el HTML que ve el usuario en su navegador.

**Contiene:**
- `auth/` - Formularios de login, registro, recuperar contraseña
- `editor/` - Interfaz del editor (webcam, stickers, captura)
- `gallery/` - Grid de fotos con likes y comentarios
- `home/` - Página principal
- `partials/` - Header y footer compartidos

**¿Por qué separado?** Para reutilizar componentes y mantener un diseño consistente.

### [core/](core/) - Las Herramientas Fundamentales
**¿Qué hace?** Proporciona funcionalidad básica que todos necesitan.

**Contiene:**
- `Router.php` - Convierte `/gallery/show/42` en código ejecutable
- `Database.php` - Conexión única y segura a MySQL
- `Controller.php` - Métodos comunes para todos los controladores
- `Mailer.php` - Envía emails de verificación y notificaciones
- `Validator.php` - Valida formularios y datos del usuario

**¿Por qué un "core"?** Para no repetir código. Todos los controladores heredan de `Controller`, todos los modelos usan `Database`, etc.

## 📺 Videos Educativos en Español

### Patrón MVC
- [¿Qué es el Patrón MVC? Explicación Simple](https://www.youtube.com/watch?v=ANQDl4hGJC0) - Por Código Facilito
- [MVC en PHP desde Cero - Tutorial Completo](https://www.youtube.com/watch?v=5s7Nn5dxC0E) - Por CodigoMasters
- [Arquitectura MVC - Conceptos y Ejemplos](https://www.youtube.com/watch?v=6nHkqkHdvLI) - Por Píldoras Informáticas

### PHP Orientado a Objetos
- [POO en PHP - Curso Completo](https://www.youtube.com/watch?v=Sb_bdOVN9HQ) - Por Código Facilito
- [Clases y Objetos en PHP](https://www.youtube.com/watch?v=1E5PqjZEFOw) - Por FaztCode
- [Herencia y Polimorfismo en PHP](https://www.youtube.com/watch?v=vY6_5E3Ky7Q) - Por EDteam

### Arquitectura de Software
- [Principios SOLID en PHP](https://www.youtube.com/watch?v=2O82eZ8K6yE) - Por MoureDev
- [Clean Code en PHP](https://www.youtube.com/watch?v=2PPzqTJNA-U) - Por Código Facilito
- [Patrones de Diseño en PHP](https://www.youtube.com/watch?v=cwfuydUHZ7o) - Por Píldoras Informáticas

### Desarrollo Web Profesional
- [Estructura de Proyectos PHP](https://www.youtube.com/watch?v=6Z7M1qMq3Fo) - Por HolaMundo
- [Buenas Prácticas en PHP](https://www.youtube.com/watch?v=dO3deLNHxCw) - Por MoureDev

## 🔗 Enlaces Relacionados

- [Documentación Principal](../README.md)
- [Controladores](controllers/README.md)
- [Modelos](models/README.md)
- [Vistas](views/README.md)
- [Core Framework](core/README.md)

---

[⬆ Volver al README principal](../README.md)
