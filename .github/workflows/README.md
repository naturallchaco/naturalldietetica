# Proyecto NaturAll - Tienda E-commerce Saludable

Guía rápida de instalación y configuración inicial.

---

## 1. Requisitos del Sistema

* Servidor Web (Apache/Nginx)
* PHP (Versión 7.4 o superior recomendada)
* MySQL/MariaDB

## 2. Pasos de Instalación Rápida

1.  **Clonar/Descargar el Repositorio:**
    ```bash
    git clone [URL-DEL-REPOSITORIO] naturall
    cd naturall
    ```

2.  **Configuración de la Base de Datos:**
    * Crea una base de datos llamada `mi__db` en tu gestor MySQL.
    * Importa el esquema inicial (si existe un archivo `db_schema.sql`).
    * **Asegúrate de configurar el archivo de conexión:**
        `config/db.php` debe contener las credenciales correctas:
        ```php
        define('DB_HOST', 'localhost');
        define('DB_NAME', 'mi__db');
        define('DB_USER', 'root');
        define('DB_PASS', '1234');
        ```

3.  **Configuración de Directorios:**
    Asegúrate de que el servidor web tenga permisos de escritura en los siguientes directorios para manejar la subida de imágenes:
    * `producto_img/` (Imágenes de productos)
    * `uploads/` (Imágenes de perfil y varios)
    
    *Recomendado en Linux/macOS:*
    ```bash
    chmod -R 775 producto_img uploads
    ```

4.  **Acceso Inicial:**
    * Abre la URL del proyecto en tu navegador (e.g., `http://localhost/naturall/`).
    * El panel de administración se encuentra en: `tienda/admin/login.php`
    * **Credenciales de Prueba (Simulación en `includes/functions.php`):**
        * Usuario: `admin@naturall.com` | Contraseña: `admin123`

---

## 3. Estructura del Proyecto

* `config/`: Archivos de configuración de la BD.
* `includes/`: Archivos modulares (header, footer, funciones, etc.).
* `tienda/`: Lógica principal del e-commerce (carrito, checkout, admin).
* `tienda/subcategorias/`: Páginas específicas de categorías de productos.
* `producto_img/`: Imágenes de productos.
* `uploads/`: Archivos de usuario.