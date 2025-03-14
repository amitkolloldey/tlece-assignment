## Tlece Assignment
**Assignment Project for Tlece**
Prerequisites
Make sure you have the following installed on your system:
* Docker
* Docker Compose
* Git
Installation Steps
1. **Clone the Repository**
Clone the repository and navigate to the project folder:

```
git clone https://github.com/amitkolloldey/tlece-assignment.git
cd tlece-assignment
```

2. **Set Up Environment Variables**
Copy the example `.env` file to the correct location and modify it to match your environment:

```
cp tlece_app/.env.example tlece_app/.env
```

3. **Build and Start the Containers**
Run the following command to start the containers:

```
docker compose up -d --build
```

This will set up the following containers:
* **PHP FPM** (Laravel runtime)
* **MySQL** (database)
* **Nginx** (web server)
* **PhpMyAdmin** (for database management)
4. **Install Dependencies**
Once the containers are running, install the necessary PHP and JS dependencies inside the `app` container:

```
docker exec -it tlece_app bash
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan key:generate
exit
```

5. **Restart Containers**
After generating the application key, restart the containers to ensure all settings are applied properly:

```
docker compose down
docker compose up -d
```

6. **Running the Application**
Once the setup is complete, you can access the application:
* **Application URL**: http://localhost:8000
* **PhpMyAdmin**: http://localhost:8080
   * Username: `tlece_user`
   * Password: `tlece_password`

7. **Database Credentials (in `.env`)**
Make sure the database credentials in your `.env` file are set correctly:

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=tlece_db
DB_USERNAME=tlece_user
DB_PASSWORD=tlece_password
```

8. **Run Migrations**
```
docker exec -it tlece_app php artisan migrate
```