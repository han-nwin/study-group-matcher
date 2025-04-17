# Study Group Matcher

## Overview
The Study Group Matcher is a web application that allows students to form and join study groups for their courses. Professors can manage courses and approve study groups. The application includes features for user registration, login, course enrollment, study group creation, and request management. It uses PHP for the backend, MySQL for the database, and includes phpMyAdmin for database management.

## Project Structure
```
.
--- Dockerfile
--- MANUAL.md
--- README.md
--- admin/
    |
|    -- admin.php
|    -- manage.php
|    -- manual.php
--- docker-compose.yml
--- home.php
--- index.php
--- login.php
--- logout.php
--- mysql/
    |
    -- create.sql
    -- load.sql
--- overview.php
--- professor_dashboard.php
--- register.php
--- script.js
--- student_dashboard.php
--- study_group.php
--- test.php
```

## Prerequisites
- **PHP 8.2 or higher:** Required for running the application locally.
- **MySQL 8.0 or higher:** Required for the database.
- **Docker and Docker Compose:** Required for running the application in containers.
- **Web Browser:** To access the application.

## Setup Instructions

### Running Locally (Without Docker)

1. **Install PHP:**
   - Ensure PHP is installed on your system. On Ubuntu/Debian, you can install it with:
     ```bash
     sudo apt update
     sudo apt install php php-mysql
     ```
   - Verify the installation:
     ```bash
     php -v
     ```

2. **Install and Run MySQL as Root Without a Password:**
   - Install MySQL:
     ```bash
     sudo apt install mysql-server
     ```
   - Start the MySQL service:
     ```bash
     sudo systemctl start mysql
     ```
   - Log into MySQL as root:
     ```bash
     sudo mysql -u root
     ```
   - Set the root password to empty (if needed):
     ```sql
     ALTER USER 'root'@'localhost' IDENTIFIED WITH 'mysql_native_password' BY '';
     ```
   - Exit MySQL:
     ```sql
     exit
     ```
   - Verify you can log in without a password:
     ```bash
     mysql -u root
     ```

3. **Navigate to the Project Directory:**
   - Change to the project directory in your terminal:
     ```bash
     cd /path/to/study-group-matcher
     ```

4. **Run the PHP Server on `localhost:8080`:**
   - Start the PHP development server:
     ```bash
     php -S localhost:8080
     ```

5. **Access the Application in Your Browser:**
   - Open your browser and visit:
     ```
     http://localhost:8080
     ```
   - You should see the study group matcher homepage.

### Running with Docker

#### Setup
1. **Map the Custom URL:**
   - To access the application at `http://www.studygroupmatcher.com`, map the URL to `127.0.0.1` in your hosts files.
   - **On Linux/WSL2:**
     ```bash
     sudo nano /etc/hosts
     ```
     Add:
     ```
     127.0.0.1 www.studygroupmatcher.com
     ```
   - **On Windows:**
     - Open `C:\Windows\System32\drivers\etc\hosts` in Notepad as Administrator.
     - Add:
       ```
       127.0.0.1 www.studygroupmatcher.com
       ```
   - Flush the DNS cache on Windows:
     ```cmd
     ipconfig /flushdns
     ```

2. **Navigate to the Project Directory:**
   ```bash
   cd /path/to/study-group-matcher
   ```

3. **Start the Docker Containers:**
   - Spin up the project with Docker Compose:
     ```bash
     docker-compose up -d
     ```
   - This will:
     - Start the PHP-Apache server on `http://www.studygroupmatcher.com` (port `80`).
     - Start the MySQL database on port `3306`.
     - Start phpMyAdmin on `http://www.studygroupmatcher.com:8090`.

4. **Verify Containers Are Running:**
   ```bash
   docker ps
   ```
   - You should see `php_app`, `mysql_db`, and `phpmyadmin`.

#### Accessing the Application
- **Website:** Open your browser and visit:
  ```
  http://www.studygroupmatcher.com
  ```
- **phpMyAdmin:** Access phpMyAdmin to manage the database:
  ```
  http://www.studygroupmatcher.com:8090
  ```
  - Log in with:
    - Username: `root`
    - Password: 

#### Managing Containers
- **Stop Containers (Preserve Database):**
  ```bash
  docker-compose down
  ```
- **Stop Containers and Reset Database:**
  ```bash
  docker-compose down -v
  ```
  - This removes the `mysql_data` volume, resetting the database to the state defined in `mysql/init.sql`.

#### Database Access
- **Access the MySQL Container:**
  ```bash
  docker exec -it mysql_db /bin/bash
  ```
- **Log into MySQL:**
  ```bash
  mysql -u root -p
  ```
- **Use the Database:**
  ```sql
  USE study_group_matcher;
  ```
- **Query Sample Data:**
  ```sql
  SELECT * FROM STUDENT;
  ```

## Sample Data
The database (`study_group_matcher`) is initialized with sample data via `mysql/create.sql` and `mysql/load.sql`:
- **Departments:** Computer Science, Mathematics, Physics.
- **Professors:** John Smith (CS), Emily Johnson (Math), Michael Brown (Physics), password: `prof123`.
- **Courses:** CS101, MATH201, PHYS101.
- **Students:** Alice Davis, Bob Wilson (CS), Charlie Miller (Math), password: `student123`.
- **Study Groups:** Three groups with leaders and schedules.
- **Requests and Enrollments:** Sample requests and course enrollments.



