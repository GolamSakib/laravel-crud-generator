# Laravel CRUD Generator

A Laravel-based CRUD (Create, Read, Update, Delete) generator that helps you quickly scaffold complete CRUD operations for your models.

## Features

- Generate complete CRUD operations (Controllers, Models, Views, Routes)
- Support for various field types
- Relationship support (hasMany, belongsTo, etc.)
- Form validation
- Bootstrap-styled views
- Soft deletes support

## Requirements

- PHP >= 7.4.19
- Composer
- Node.js & NPM
- MySQL or other supported database

## Installation

1. Clone the repository:
```bash
git clone https://github.com/GolamSakib/laravel-crud-generator.git
cd laravel-crud-generator
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install JavaScript dependencies:
```bash
npm install
```

4. Create a copy of the .env file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Configure your database in the .env file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

7. Run migrations:
```bash
php artisan migrate
```

8. Compile assets:
```bash
npm run dev
```

## Usage

### Generating CRUD for a Model

To generate CRUD operations for a model, use the following command:

```bash
php artisan make:crud {ModelName} --fields="field1:type,field2:type" --relations="relationName:relationType"
```

Example:
```bash
php artisan make:crud Project --fields="name:string,description:text,status:enum(open,closed)" --relations="tasks:hasMany"
```

### Available Field Types

- string
- text
- integer
- float
- boolean
- date
- datetime
- enum
- json

### Available Relations

- hasMany
- belongsTo
- hasOne
- belongsToMany
- morphTo
- morphMany

## Running the Application

1. Start the development server:
```bash
php artisan serve
```

2. Access the application in your browser:
```
http://localhost:8000
```




