# QR Code Tracker

A simple PHP-based QR code generator and tracker, containerized with Docker. Generate QR codes, host them, redirect users, and track scans using a MariaDB database. Includes user registration, login, password reset, and admin/user roles.

---

## Features

- Generate QR codes for any URL or text
- Host and serve QR code images
- Redirect QR code scans to the original URL
- Track scan statistics (IP, user agent, timestamp)
- User registration and login (first user is admin)
- Admin can view all QR codes; users see only their own
- Password change and reset via email
- Web interface for management
- Dockerized for easy deployment

---

## Prerequisites

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/)

---

## Quick Start

### 1. **Clone the repository**

```sh
git clone https://github.com/TechButton/qr-code.git
cd qr-code
```

### 2. **Set Up Environment Variables**

- Copy the example environment file and edit it:
  ```sh
  cp .env-default .env
  ```
- Open `.env` in your editor and set your own values for:
  - `MYSQL_ROOT_PASSWORD`
  - `MYSQL_DATABASE`
  - `MYSQL_USER`
  - `MYSQL_PASSWORD`
  - `APACHE_SERVER_NAME` (e.g. `localhost` or your domain)

> **Note:**  
> Never commit your `.env` file with real secrets to version control.

### 3. **Build and Start the Containers**

```sh
docker-compose up --build
```

- The web interface will be available at [http://localhost](http://localhost) (or your chosen server name).

### 4. **Database Initialization**

- The database tables and a default admin user (`admin` / `changeme`) are created automatically on first run.
- **No need to visit `init_db.php` manually.**

### 5. **Using the App**

- Visit your site (e.g. [http://localhost](http://localhost)).
- Log in with the default admin account (`admin` / `changeme`).
- Register new users as needed.
- Generate QR codes and track their usage.

---

## Customization

- **Change Database Credentials:**  
  Edit your `.env` file before first run.
- **Change Web Server Name:**  
  Set `APACHE_SERVER_NAME` in `.env` (e.g. `qrcode.yourdomain.com`).
- **Change Default Admin Password:**  
  Log in as `admin` and use the "Change Password" feature.

---

## File Structure

```
.
├── docker-compose.yml
├── .env-default         # Example environment file
├── .env                 # Your actual environment file (not committed)
├── php/                 # PHP Docker build context
├── src/                 # Application source code
│   ├── index.php
│   ├── login.php
│   ├── register.php
│   ├── change_password.php
│   ├── forgot_password.php
│   ├── reset_password.php
│   ├── generate.php
│   ├── redirect.php
│   ├── init_db.php
│   └── ...etc
└── qrcodes/             # Generated QR code images
```

---

## Security Notes

- **Change all default passwords** before deploying in production.
- If exposing to the internet, use HTTPS.
- Restrict or remove `init_db.php` after setup.
- The password reset feature is for demonstration; in production, configure real email sending.

---

## Troubleshooting

- If the web app cannot connect to the database, check the logs:
  ```sh
  docker-compose logs
  ```
- If you change any database credentials in `.env` after the first run, you **must** reset the database:
  ```sh
  docker-compose down -v
  docker-compose up --build
  ```
  This will erase all data and apply the new credentials.

---

## License

MIT

---

## Author

Kyle Button  
kyle@kylebutton.com
