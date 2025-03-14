1. Make sure you install php
2. Make sure you install and are running MySQL as root without a password.
3. cd to this project directory in your terminal
4. Run php server on localhost:8080
   ```
   php -S localhost:8080
   ```
   ```
  5. On your browser visit: localhost:8080/home.php

## Using Docker

  ```bash
  docker-compose up -d # Spin up the project

  docker-compose down -v # This will reset everything and remove database
  docker-compose down # this won't reset the database

  docker ps
  ```
- Start the PHP-Apache server on http://localhost:8080/
- Start the MySQL database on port 3306
- Start phpMyAdmin on http://localhost:8090/
 
