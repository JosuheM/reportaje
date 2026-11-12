# Despliegue en Azure (sin costo)

Arquitectura: **Azure App Service (Linux, PHP 8.2, plan F1 gratis)** + **Azure Database for MySQL Flexible Server** (prueba gratuita de 12 meses), desplegado desde GitHub con `.github/workflows/azure-deploy.yml`.

> Azure no tiene MySQL gratis "para siempre". La cuenta gratuita de Azure (o Azure for Students) incluye MySQL Flexible B1ms gratis 12 meses. Pasado ese plazo, o si se elige otra opción, **se cobra**. Antes de empezar, crea una alerta de presupuesto en *Cost Management + Billing → Budgets* (por ejemplo 1 USD).

## 1. Exportar la base local (en tu PC)

Con MySQL encendido en XAMPP:

```bash
/c/xampp/mysql/bin/mysqldump.exe -u root --no-tablespaces --default-character-set=utf8mb4 --single-transaction revista_digital > revista_digital_azure.sql
```

No lo subas a git: tiene los usuarios del panel.

## 2. Crear la base MySQL

Portal → *Azure Database for MySQL flexible servers* → Crear.

- Plan de carga: **Desarrollo o pruebas**, tamaño **Standard_B1ms** (debe aparecer la etiqueta de gratuito/free; si no aparece, detente: se cobraría).
- Almacenamiento: 20 GB. Alta disponibilidad: **desactivada**. Copias de seguridad: 7 días.
- Autenticación: solo MySQL. Guarda el usuario administrador y la contraseña.
- Redes: acceso público, y marca **Permitir que servicios de Azure accedan** (así entra la Web App). Agrega también tu IP actual para poder importar.
- Cuando termine: *Bases de datos* → crear `revista_digital`.

Importa el dump (necesita el cliente `mysql`; el de XAMPP sirve):

```bash
/c/xampp/mysql/bin/mysql.exe -h <SERVIDOR>.mysql.database.azure.com -u <USUARIO> -p --ssl revista_digital < revista_digital_azure.sql
```

## 3. Crear la Web App

Portal → *App Services* → Crear → Aplicación web.

- Publicar: **Código**. Pila: **PHP 8.2**. SO: **Linux**.
- Plan: **Free F1**. No elijas B1 ni superior.
- Cuando termine, en *Configuración → Configuración general*, activa **Credenciales de publicación de autenticación básica (SCM)**.
- *Configuración → Variables de entorno* (App Settings):

| Nombre | Valor |
|---|---|
| `DB_HOST` | `<SERVIDOR>.mysql.database.azure.com` |
| `DB_NAME` | `revista_digital` |
| `DB_USER` | usuario administrador |
| `DB_PASS` | contraseña de la base |
| `DB_SSL` | `1` |
| `CODIGO_RECUPERACION` | un código nuevo, no el del README |
| `CODIGO_REGISTRO` | un código nuevo, o vacío para cerrar el registro |

## 4. Conectar GitHub

1. En la Web App: *Información general → Obtener perfil de publicación*. Se descarga un archivo.
2. En GitHub → repo `reportaje` → *Settings → Secrets and variables → Actions*:
   - Secret `AZURE_WEBAPP_PUBLISH_PROFILE` = contenido completo del archivo descargado.
   - Variable `AZURE_WEBAPP_NAME` = nombre de la Web App.
3. Haz push a `main` (o *Actions → Run workflow*). Al terminar abre `https://<webapp>.azurewebsites.net`.

## Límites del plan gratuito

- F1: 60 minutos de CPU al día; la web se duerme por inactividad y la primera visita tarda.
- Las imágenes o PDF subidos desde el panel se guardan en el disco de la Web App y **se pierden en el siguiente despliegue**. Lo que esté en git (carpeta `assets/images`) se conserva.
- `admin/setup.php` se eliminó del repositorio: con la tabla `usuarios` vacía permitía crear un administrador a cualquiera. La base de Azure se importa con los usuarios existentes; para crear el primero desde cero, ver el README (instalación local, paso 5).
