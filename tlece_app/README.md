# Tlece Assignment

Assignement project for Tlece

## Prerequisites
Make sure you have the following installed on your system:

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)
- [Git](https://git-scm.com/)

## Installation Steps

### 1. Clone the Repository
```sh
git clone https://github.com/your-username/tlece-assignment.git
cd tlece-assignment
```

### 2. Set Up Environment Variables
Copy the example `.env` file and modify it as needed:
```sh
cp tlece_app/.env.example tlece_app/.env
```
Ensure the database and Redis configurations match the ones set in `docker-compose.yml`.

### 3. Build and Start the Containers
Run the following command to build and start the application:
```sh
docker-compose up -d --build
```
This will start:
- **PHP FPM container** for Laravel
- **MySQL database**
- **Nginx web server**
- **Redis** for caching and queue handling
- **PhpMyAdmin** for database management

### 4. Install Dependencies
Run the following inside the `app` container:
```sh
docker exec -it tlece_app bash
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan key:generate
php artisan migrate --seed
exit
```

### 5. Set Permissions
Ensure Laravel storage and cache directories have the correct permissions:
```sh
docker exec -it tlece_app bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
exit
```

### 6. Running the Application
Once the containers are running, access the application via:
- **Application URL**: [http://localhost:8000](http://localhost:8000)
- **PhpMyAdmin**: [http://localhost:8080](http://localhost:8080) (Username: `tlece_user`, Password: `tlece_password`)

### 7. Managing Queues
Laravel queues are managed using Redis. Start the queue worker:
```sh
docker exec -it tlece_app bash
php artisan queue:work
```

### 8. Stopping the Application
To stop and remove the running containers, use:
```sh
docker-compose down
```

### 9. Logs & Debugging
To view logs for debugging:
```sh
docker logs -f tlece_app
```
To get into the app container shell:
```sh
docker exec -it tlece_app bash
```
