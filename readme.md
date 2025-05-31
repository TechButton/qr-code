# QR Code Tracker

A simple PHP-based QR code generator and tracker, containerized with Docker. This project lets you generate QR codes, host them, redirect users, and track scans using a MySQL database.

---

## Features

- Generate QR codes for any URL or text
- Host and serve QR code images
- Redirect QR code scans to the original URL
- Track scan statistics (IP, user agent, timestamp)
- Web interface for management
- Dockerized for easy deployment

---

## Prerequisites

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/)

---

## Quick Start

1. **Clone the repository**

   ```sh
   git clone https://github.com/TechButton/qr-code.git
   cd qr-code
   ```

2. **Edit Configuration (Required Before First Run)**

   - **Database Credentials:**  
     Edit `docker-compose.yml` if you want to change MySQL root/user passwords or database name.

3. **Build and Start the Containers**

   ```sh
   docker-compose up --build
   ```

   - The web interface will be available at [http://localhost:8000](http://localhost:8000).

4. **Initialize the Database**

   - Visit `http://localhost:8000/init_db.php` in your browser to create the required tables.

5. **Use the App**

   - Go to [http://localhost:8000](http://localhost:8000) to generate QR codes and view scan stats.

---

## Configuration

### Environment Variables

Edit the `docker-compose.yml` file to change:

- `MYSQL_ROOT_PASSWORD`
- `MYSQL_DATABASE`
- `MYSQL_USER`
- `MYSQL_PASSWORD`

---

## File Structure

```
.
├── docker-compose.yml
├── php/                # PHP Docker build context
├── src/                # Application source code
│   ├── index.php
│   ├── generate.php
│   ├── redirect.php
│   ├── init_db.php
│   └── ...etc
└── qrcodes/            # Generated QR code images
```

---

## Security Notes

- **Change all default passwords** before deploying in production.
- If exposing to the internet, use HTTPS.
- Consider restricting access to `init_db.php` after setup.

---

## Troubleshooting

- If the web app cannot connect to the database, check the logs with:
  ```sh
  docker-compose logs
  ```
- Make sure the database is healthy before accessing the web interface.

---

## License

MIT

---

## Author

Kyle Button  
kyle@kylebutton.com
