# DDP Noticias — Diálogo y Desarrollo Perú

Réplica funcional de [dialogoydesarrollo.com.pe](https://www.dialogoydesarrollo.com.pe) con panel de administración propio, hecha en **PHP + MySQL** y desplegada en **Hostinger** desde GitHub.

**URL pública:** https://orangered-bison-369479.hostingersite.com
**Panel de administración:** https://orangered-bison-369479.hostingersite.com/admin/login.php

## Funcionalidades

- Sitio público dinámico: Reportajes (con paginación), Noticias, Boletín NTEP, Podcast y Especiales.
- Reproductor de podcast en ventana emergente (Spotify / YouTube).
- Imágenes de los reportajes ampliables con clic y con carga diferida (`loading="lazy"`).
- Archivo de reportajes por mes/año.
- Panel de administración (AdminLTE 3.2): CRUD de contenido, biblioteca de medios, apariencia, usuarios y roles (admin / autor).

## Tecnologías

PHP 8.2 · MySQL/MariaDB (PDO con consultas preparadas) · Apache (`.htaccess`) · Bootstrap 4 · AdminLTE 3.2

## Estructura

```
├── index.php, reportajes.php, articulo.php, boletines.php, podcast.php, especiales.php
├── inc/                     Funciones del sitio público
├── assets/                  CSS, JS e imágenes
├── admin/                   Panel de administración
│   └── config/
│       ├── conexion.example.php   Plantilla de configuración (SÍ se versiona)
│       └── conexion.php           Credenciales reales (NO se versiona, ver .gitignore)
├── database/schema.sql      Estructura de la base de datos (sin datos)
└── .htaccess                Redirecciones de las URLs antiguas (.html)
```

## Instalación local (XAMPP)

1. Clonar en `C:\xampp\htdocs\reportaje`.
2. En phpMyAdmin crear la base `revista_digital` (cotejamiento `utf8mb4_unicode_ci`) e importar `database/schema.sql`.
3. Copiar `admin/config/conexion.example.php` a `admin/config/conexion.php` y completar usuario, contraseña y códigos.
4. Cambiar `RewriteBase` en `.htaccess` a `/reportaje/` si se sirve desde subcarpeta.
5. Abrir `http://localhost/reportaje/admin/setup.php` para crear el primer usuario y **borrar ese archivo después**.

## Despliegue en Hostinger

1. **Base de datos:** hPanel → Bases de datos → crear base y usuario MySQL; abrir phpMyAdmin e importar `database/schema.sql` (o el `.sql` con datos exportado desde local).
2. **Código:** hPanel → Avanzado → Git → conectar `https://github.com/JosuheM/reportaje.git`, rama `main`, directorio `public_html`, con implementación automática activada (cada `git push` se despliega solo).
3. **Credenciales:** como `conexion.php` no está en Git, se crea a mano en el servidor (Administrador de archivos → `admin/config/conexion.php`) con los datos de la base de Hostinger.
4. **Verificación:** abrir la URL pública y entrar al panel.

## Seguridad y buenas prácticas

- Contraseñas y códigos maestros fuera del repositorio (`.gitignore` + plantilla `conexion.example.php`).
- Contraseñas de usuarios con `password_hash` (bcrypt); consultas con PDO preparadas; salida escapada con `htmlspecialchars`.
- Subidas de imágenes validadas por tipo MIME real y con nombre generado.
- Scripts de uso único (migraciones, importadores) eliminados del repositorio.
- Historial de Git limpio y commits atómicos con mensajes descriptivos.


