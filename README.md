<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# API Project

## Overview

This API project provides a robust and secure backend solution with various features to support modern web applications. The project includes integrations for payment processing, user authentication, and profile management.

## Features

- **Payment Gateway Integration**:
  - Midtrans Snap
  - Midtrans CoreAPI

- **Authentication**:
  - User login and registration
  - Forgot password and email verification using SMTP
  - Google social login via Laravel Socialite
  - API authentication using Laravel Passport

- **Profile Management**:
  - View and edit user profile

- **Configuration**:
  - Basic routing and configuration setup

## Getting Started

To get started with this API project, follow the instructions below:

### Prerequisites

- PHP 8.0 or higher
- Composer
- Laravel 10.x
- MySQL or any other compatible database
- Node.js (for any front-end dependencies)

### Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/maulanaakgn/api-project-laravel10.git
   cd api-project-laravel10
   
2. Install composer:
   ```bash
   composer install

3. Copy file ` ```dotenvdotexample ... ``` ` to ` ```dotenv ... ``` `:
   ```bash
   cp .env.example .env

4. Generate application key:
   ```bash
   php artisan key:generate

5. Run migrations:
   ```bash
   php artisan migrate
   
6. Install laravel passport:
   ```bash
   php artisan passport:install

7. Copy Client ID & Client Secret to ` ```dotenv ... ``` `:
   ```bash
   PASSPORT_CLIENT_ID=YOUR_PASSPORT_CLIENT_ID_HERE
   PASSPORT_CLIENT_SECRET=YOUR_PASSPORT_CLIENT_SECRET_HERE


