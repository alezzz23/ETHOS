<div align="center">
  <img src="Landingpreview.png" alt="ETHOS Landing Preview" width="100%" />

  # ETHOS Platform
  
  **Consulting Firm Landing Page & Internal Management System**
</div>

## 📖 Overview

ETHOS is a comprehensive, production-grade platform tailored for a consulting firm. It serves two main purposes:
1. **Public Landing Page**: A premium, high-converting public website featuring a dark luxury aesthetic, dynamic animations, and clear service value propositions.
2. **Internal CRM & Admin Dashboard**: A feature-rich internal management system integrating the Vuexy admin template, allowing staff to manage clients, projects, POS transactions, and access controls seamlessly.

## ✨ Key Features

- 🌟 **Premium Landing Page**: A visually striking public-facing site with custom cursors, smooth glassmorphism effects, and distinct conversion funnels.
- 📊 **Vuexy Administration Dashboard**: A polished backend administration area for managing operations with complete visual and functional parity.
- 🔐 **Role-Based Access Control**: Highly granular permissions and roles securely implemented using Spatie Laravel Permission.
- 💼 **CRM & Project Management**: Tailor-made tools for tracking client relationships, advancing active projects, and organizing tasks.
- 💳 **Point-Of-Sale (POS) Integration**: Custom payment registration handling, including correct bank statement alignment for manual reconciliation.
- ⚡ **Optimized Performance**: Driven by Laravel 10, Vite, Tailwind CSS, and Alpine.js for rapid load times and optimal developer experience.

## 🛠 Tech Stack

**Backend System**
- Framework: [Laravel 10](https://laravel.com/) (PHP 8.1+)
- Authentication: Laravel Breeze
- Authorization: [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)
- API Docs: L5-Swagger

**Frontend Architecture**
- CSS Framework: [Tailwind CSS](https://tailwindcss.com/)
- JavaScript: [Alpine.js](https://alpinejs.dev/)
- Asset Bundler: [Vite](https://vitejs.dev/)
- UI Template: Vuexy Admin

## 🚀 Getting Started

Follow these steps to set up the project locally for development.

### Prerequisites

- **PHP**: `^8.1`
- **Composer** (Dependency Manager for PHP)
- **Node.js** & **NPM/Yarn**
- **Database**: MySQL, PostgreSQL, or SQLite

### Installation Guide

1. **Install Dependencies**:
   ```bash
   composer install
   npm install
   ```

2. **Environment Configuration**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Make sure to configure your `DB_*` credentials in the new `.env` file.*

3. **Run Migrations & Database Seeding**:
   ```bash
   php artisan migrate --seed
   ```
   *This commands scaffolds the initial database setup alongside necessary roles, permissions, and sample users.*

4. **Start Development Servers**:
   Open two terminal windows to run both Vite and the Laravel server concurrently.
   
   **Terminal 1** (Compile frontend assets):
   ```bash
   npm run dev
   ```
   
   **Terminal 2** (Serve application locally):
   ```bash
   php artisan serve
   ```

5. **Access the Application**:
   - Web Platform: [http://localhost:8000](http://localhost:8000)
   - Admin Login: [http://localhost:8000/login](http://localhost:8000/login)

## 📄 License

This application is proprietary software. All rights reserved by ETHOS.
