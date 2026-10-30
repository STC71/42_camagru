# 📤 `public/uploads/` — Contenido generado

> **Navegación:** [public/](../README.md) · [Image model](../../app/models/README.md) · [QUICKSTART](../../QUICKSTART.md)

## Estructura

```
uploads/
├── images/     # PNG/GIF finales (filename en tabla images)
└── temp/       # Uso puntual si el pipeline lo necesita
```

## Permisos

El proceso del contenedor (`www-data`) debe poder escribir:

```bash
chmod -R 777 public/uploads
# o
docker-compose exec web chmod -R 777 /var/www/html/public/uploads
```

En compose a menudo se monta solo este volumen para persistir fotos entre rebuilds.

## Seguridad

- No ejecutar uploads como PHP.  
- Validar MIME/tamaño en `EditorController`.  
- Borrado de fichero al borrar imagen o cuenta (si se elige).

---

<p align="center">
  <b>Hecho con ❤️ por sternero en 42 Málaga — Octubre 2026</b>
</p>
