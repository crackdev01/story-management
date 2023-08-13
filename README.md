# Story Management System

A laravel-vue app with built-in role-based authentication.
You can manage your stories in this app.


## Demo

`Email: admin@example.com`
`password: password`

`Email: user@example.com`
`password: password`


## Installation

#### Client side
```bash
  cd backend
  npm install
```
#### Server side
```bash
  cd frontend
  composer install
  cp .env.example .env
  php artisan key:generate
  php artisan migrate:fresh --seed
  php artisan passport:install
```
#### If you can't access app with provided credential please follow.
```bash
  php artisan cache:clear
  php artisan passport:install
```
  
## Screenshots
