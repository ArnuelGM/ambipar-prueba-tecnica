# 🗺️ Sistema de Optimización de Rutas (Laravel + Inertia.js + Vue)

Este proyecto es una prueba técnica para calcular rutas óptimas y desglosar sus tramos utilizando Laravel, Inertia.js (Vue), consumiendo servicios geográficos gratuitos OSRM.

## 🚀 Guía de Instalación Rápida

Sigue estos 4 pasos para clonar y ejecutar el proyecto en tu entorno local.

### 1. Descargar el repositorio
Clona el proyecto desde tu servidor de repositorios y accede a la carpeta:
```bash
git clone https://github.com/ArnuelGM/ambipar-prueba-tecnica.git
cd ambipar-prueba-tecnica
```

### 2. Instalar dependecias
Instala los paquetes necesarios para el Backend (PHP) y el Frontend (JavaScript):
```bash
# Instalar dependencias de PHP
composer install

# Instalar dependencias de Node.js usando pnpm
pnpm i --ignore-scripts

# compilar assets
pnpm build
```

> **Nota:** 
    Antes de continuar, duplica el archivo .env.example, renombralo a .env y configura tus credenciales de base de datos PostgreSQL.

```bash
cp .env.example .env
php artisan key:generate
```
### 3. Ejecuta las migraciones
Crea las tablas necesarias en tu base de datos (SQLite).
```bash
php artisan migrate
```

### 4. Levantar los servidores

Abre dos terminales para ejecutar tanto el servidor de desarrollo de PHP como el compilador del Frontend:

- #### Terminal 1 (Backend PHP):
    ```bash
    php artisan serve
    ```

- #### Terminal 2 (Frontend & SSR):
    ```bash
    pnpm dev
    ```

¡Listo! Ya puedes abrir tu navegador en http://127.0.0.1:8000 para probar la aplicación.
