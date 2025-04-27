# demoapi1

# Prueba de API REST
# Demo API - Dashboard de Valores Medidos


API REST segura + Frontend PWA + Notificaciones Push.

---

## 📦 Estructura del Proyecto

/api/ -> API REST PHP (login, consultas, push) 
/frontend/ -> Frontend web PWA Bootstrap 5 
/sql/ -> Scripts SQL para creación de base de datos 
/postman/ -> Colección Postman para pruebas API 
composer.json -> Dependencias PHP 
README.md -> Este archivo

## Requisitos PHP:

- pdo_mysql
- openssl
- mbstring
- curl


## Requerido:
### 1. Instalar la librería web-push:

composer require minishlink/web-push

### 2. Tener tus claves VAPID reales generadas (en vendor/bin/web-push generate:vapid).

dentro de carpeta api, instalar dependencias con:

composer install


## Instalación

### 1. Clonar el repositorio

git clone https://github.com/mruizrivas/demoapi1.git
cd demoapi1

### 2. Configurar el backend

Modifica /api/ApiMedidas.php con tus credenciales de base de datos MariaDB.

Coloca tu clave secreta JWT en ApiMedidas.php.

Instala dependencias PHP:

composer install


### 3. Crear base de datos
Importa el archivo SQL:

mysql -u tu_usuario -p tu_base_datos < sql/estructura.sql

## Uso del frontend

Uso del Frontend
Abre en el navegador:

https://tu-dominio.com/frontend/index.html
Funciones:

- Login por correo y clave.
- Consultar medidas por fecha, tensión y peso.
- Ver resultados en tabla responsive.
- Instalar como App PWA.
- Cerrar sesión.
- Recibir notificaciones push.

## API REST - Ejemplos

### 1. Login

curl -X POST https://tu-dominio.com/api/login.php \
     -d "cuenta=usuario1@example.com&clave_acceso=1234"

### 2. Obtener Medidas

curl -H "Authorization: Bearer TU_TOKEN_AQUI" \
     "https://tu-dominio.com/api/api_medidas.php?fecha_inicio=2025-04-01&fecha_fin=2025-04-30&tension_min=210&tension_max=230"

### 3. Regenerar claves VAPID para Push Notifications

./vendor/bin/web-push generate:vapid









