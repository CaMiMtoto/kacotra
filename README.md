## ✨ Inventory Management System

This is a simple inventory management system that allows users to add, update, delete, and view items in the inventory.
The system is built using PHP(Laravel) and MySQL.

## 🚀 Features

- Add items to the inventory
- Sale items
- Purchase items
- Manage Deposits
- Manage Damages
- Manage Expenses
- Manage Recoveries
- Manage Suppliers
- Manage Customers
- Manage Categories
- Manage Brands
- Manage Units
- Manage Users

## Requirements

- PHP >= 8.0 - for windows download latest xampp (https://www.apachefriends.org/)
- Composer - for windows download composer (https://getcomposer.org/Composer-Setup.exe)
- MySQL - for windows it comes with xampp
    - Laravel 10.x -
        ````bash 
      composer global require laravel/installer
      ````

- Node.js - for windows download node.js (https://nodejs.org/en/download/)

## 🛠️ Installation Steps

1. Clone the repository

```bash
git clone https://github.com/CaMiMtoto/kacotra.git
```

2. Change the working directory

```bash
cd kacotra
```

3. Install dependencies

```bash
composer install
```

4. Create a copy of the .env file

```bash
cp .env.example .env
```

5. Generate an application key

```bash
php artisan key:generate
```

6. Create an empty database for the application
7. Update the .env file with the database credentials
8. Run the migrations

```bash
php artisan migrate --seed
```

9. Run the development server

```bash
php artisan serve
```

10. You can now access the server at http://localhost:8000
11. Login with the default credentials

```bash
email: admin@gmail.com
password: password
```

