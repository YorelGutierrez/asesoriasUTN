# 📚 Asesorías UT - Plataforma de Gestión de Asesorías Académicas

![Laravel](https://img.shields.io/badge/Laravel-10.x-red?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?logo=mysql)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple?logo=bootstrap)
![JWT](https://img.shields.io/badge/JWT-Auth-black?logo=jsonwebtokens)

## 📖 Descripción del proyecto

**Asesorías UT** es una aplicación web diseñada para optimizar el proceso de solicitud, programación y seguimiento de asesorías académicas.  
Centraliza la comunicación entre alumnos, docentes y administradores, y ofrece herramientas como:

- Solicitud de asesorías (individual o grupal)
- Programación de sesiones con modalidad presencial/virtual
- Registro de acuerdos y resultados
- Historial académico del alumno (materias reprobadas, temas no dominados)
- Generación de reportes cuatrimestrales
- Bitácora de actividades y respaldos de base de datos
- Autenticación por roles (admin, docente, alumno) + JWT para API

---

## 📌 Tabla de Contenidos
- [Características principales](#-características-principales)
- [Tecnologías utilizadas](#-tecnologías-utilizadas)
- [Instalación y configuración](#-instalación-y-configuración)
- [Estructura de la base de datos](#-estructura-de-la-base-de-datos)
- [Roles y permisos](#-roles-y-permisos)
- [Créditos](#-créditos)

---

## ✨ Características principales

- ✅ **Login por roles** (admin, docente, alumno, tutor) con redirección a paneles personalizados.
- ✅ **Gestión de grupos, alumnos y docentes** (CRUD + filtros dinámicos).
- ✅ **Solicitud y programación de asesorías** (individual o grupal, virtual o presencial).
- ✅ **Historial de asesorías** con acuerdos y resultados.
- ✅ **Generación de reportes cuatrimestrales** (solo admin/docente).
- ✅ **Respaldos automáticos** de la base de datos mediante `mysqldump` (manual o programado).
- ✅ **Bitácora de actividad** (logs de login, acciones importantes) con soft delete.
- ✅ **Autenticación JWT** para proteger endpoints API.
- ✅ **Diseño responsivo** con Bootstrap 5 y estilos personalizados.
- ✅ **Internacionalización** (español/inglés básico).

---

## 🛠️ Tecnologías utilizadas

| Capa          | Tecnología                         |
|---------------|------------------------------------|
| Backend       | Laravel 10, PHP 8.1+              |
| Frontend      | Bootstrap 5, Blade, CSS3, JS      |
| Base de datos | MySQL 8.0                          |
| Autenticación | Laravel Session + JWT (tymon/jwt-auth) |
| Tareas programadas | Laravel Scheduler + mysqldump |
| Íconos        | Bootstrap Icons                    |
| Fuentes       | Google Fonts (Nunito)              |

---

## 🚀 Instalación y configuración

### Requisitos previos
- PHP >= 8.1
- Composer
- MySQL
- Node.js (solo para assets opcionales)
- Acceso a línea de comandos

### Pasos de instalción

```bash
# 1. Clonar repositorio
git clone https://github.com/YorelGutierrez/asesoriasUTN.git
cd asesorias-ut

# 2. Instalar dependencias PHP
composer install
    #en caso de ya contar con composer utilizar
    composer update

# 3. Copiar archivo de entorno y configurar
cp .env.example .env
php artisan key:generate

# 4. Configurar base de datos en .env
DB_DATABASE=asesorias_ut
DB_USERNAME=root
DB_PASSWORD=

# 5. Ejecutar migraciones y seeders
php artisan migrate --seed

# 6. Crear enlace simbólico para imágenes
php artisan storage:link

# 7. Instalar JWT
composer require tymon/jwt-auth
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
php artisan jwt:secret

# 8. Iniciar servidor de desarrollo
php artisan serve

# 9. (Opcional, utilizado para la realizacon de pruebas de respaldo de manera local) Planificar de tareas
php artisan schedule:work #Nota: la hora esta adelanta 1 hora dentro del proyecto
```
---

## Estructura de la base de datos

Tablas principales:
- users → datos comunes (nombre, email, rol, foto_perfil)
- alumnos, docentes → datos específicos de cada rol
- carreras, grupos, materias → catálogos académicos
- solicitudes_asesoria, sesiones_asesoria, acuerdos_asesoria
- historial_academico (materias reprobadas, temas no dominados)
- logs → bitácora con soft delete
- respaldo_config.json → configuración de backups programados

---

## Roles y Permisos

Roles de la web:

- Administrador
- Docente
- Alumno
- Tutor (Aun no implementado)

---

## 👥 Creditos

| Integrantes del equipo de desarollo 
|--------
| Bernal Hernandez Brandon Eduardo 
| Corona Perez Alain Antonio 
| Gonzalez Lares Alexandra Rubí 
| Gutiérrez Zepeda Yorel Isai 
| Rivera Orozco Vanessa de Jesús 
| Samaniego de Leon Andy Alexander

---

