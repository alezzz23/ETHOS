<div align="center">
  <img src="Landingpreview.png" alt="ETHOS Landing Preview" width="100%" />

  # Plataforma ETHOS
  
  **Landing Page de Consultoría y Sistema de Gestión Interna**
</div>

## 📖 Visión General

ETHOS es una plataforma integral de nivel de producción diseñada para una firma de consultoría. Cumple con dos propósitos principales:
1. **Landing Page Pública**: Un sitio web público de alta conversión con una estética de lujo oscura, animaciones dinámicas y propuestas de valor claras para los servicios.
2. **CRM Interno y Panel de Administración**: Un sistema de gestión interna rico en funcionalidades que integra la plantilla de administración Vuexy, permitiendo al personal gestionar clientes, proyectos, transacciones de punto de venta (POS) y controles de acceso de manera fluida.

## ✨ Características Principales

- 🌟 **Landing Page Premium**: Un sitio orientado al público visualmente impactante con cursores personalizados, efectos de glassmorphism suaves y embudos de conversión diferenciados.
- 📊 **Panel de Administración Vuexy**: Un área administrativa de backend pulida para gestionar operaciones con una paridad visual y funcional completa.
- 🔐 **Control de Acceso Basado en Roles (RBAC)**: Permisos y roles altamente granulares implementados de forma segura mediante Spatie Laravel Permission.
- 💼 **Gestión de CRM y Proyectos**: Herramientas a medida para el seguimiento de las relaciones con los clientes, el avance de proyectos activos y la organización de tareas.
- 💳 **Integración de Punto de Venta (POS)**: Manejo personalizado del registro de pagos, incluyendo la alineación correcta con los estados de cuenta bancarios para la conciliación manual.
- ⚡ **Rendimiento Optimizado**: Impulsado por Laravel 10, Vite, Tailwind CSS y Alpine.js para tiempos de carga rápidos y una experiencia de desarrollo óptima.

## 🛠 Stack Tecnológico

**Sistema Backend**
- Framework: [Laravel 10](https://laravel.com/) (PHP 8.1+)
- Autenticación: Laravel Breeze
- Autorización: [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)
- Documentación de API: L5-Swagger

**Arquitectura Frontend**
- Framework de CSS: [Tailwind CSS](https://tailwindcss.com/)
- JavaScript: [Alpine.js](https://alpinejs.dev/)
- Empaquetador de Activos: [Vite](https://vitejs.dev/)
- Plantilla de UI: Vuexy Admin

## 🚀 Primeros Pasos

Sigue estos pasos para configurar el proyecto localmente para el desarrollo.

### Requisitos Previos

- **PHP**: `^8.1`
- **Composer** (Gestor de dependencias para PHP)
- **Node.js** y **NPM/Yarn**
- **Base de Datos**: MySQL, PostgreSQL o SQLite

### Guía de Instalación

1. **Instalar Dependencias**:
   ```bash
   composer install
   npm install
   ```

2. **Configuración del Entorno**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Asegúrate de configurar tus credenciales `DB_*` en el nuevo archivo `.env`.*

3. **Ejecutar Migraciones y Seeders**:
   ```bash
   php artisan migrate --seed
   ```
   *Este comando prepara la estructura inicial de la base de datos junto con los roles, permisos y usuarios de prueba necesarios.*

4. **Iniciar Servidores de Desarrollo**:
   Abre dos ventanas de terminal para ejecutar tanto Vite como el servidor de Laravel simultáneamente.
   
   **Terminal 1** (Compilar activos frontend):
   ```bash
   npm run dev
   ```
   
   **Terminal 2** (Servir la aplicación localmente):
   ```bash
   php artisan serve
   ```

5. **Acceder a la Aplicación**:
   - Plataforma Web: [http://localhost:8000](http://localhost:8000)
   - Acceso Administrativo: [http://localhost:8000/login](http://localhost:8000/login)

## 📄 Licencia

Esta aplicación es software propietario. Todos los derechos reservados por ETHOS.
