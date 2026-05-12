# NexaAuth — User Management System
**Full Stack Intern Assessment | Built by Anuj (alonesurvivor03@gmail.com)**

---

## Tech Stack
- **Frontend**: HTML5, CSS3, Vanilla JavaScript (fetch API)
- **Backend**: PHP 8+ (PDO)
- **Database**: MySQL

## Features
- User Registration & Login with session auth
- Role-based system: `admin` / `user`
- **Only ONE admin allowed** — enforced on both frontend & backend
- **Duplicate email prevention** — frontend + backend validation
- Dashboard with user table & stats
- **"Show Only Admins" filter** button
- Fully JSON API backend (`register.php`, `login.php`, `users.php`)
- No page reloads — all via `fetch()`
- Responsive, professional dark UI

---

## Project Structure
```
├── index.html       ← Frontend SPA (all HTML/CSS/JS)
├── config.php       ← DB connection + session helpers
├── register.php     ← POST /register
├── login.php        ← POST /login
├── logout.php       ← POST /logout
├── users.php        ← GET  /users?role=admin (protected)
└── schema.sql       ← Database setup
```

---

## Local Setup

### 1. Database
```sql
-- Run schema.sql in MySQL
mysql -u root -p < schema.sql
```

### 2. Backend Config
Edit `config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'user_management');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### 3. Run Locally
Use XAMPP / WAMP / MAMP — place files in `htdocs/user-management/`.
Or: `php -S localhost:8000`

Open: `http://localhost:8000`

### 4. Update API Base URL in index.html
Near the top of the `<script>` block:
```js
const API = 'http://localhost:8000'; // or your live domain
```

---

## Default Admin Account
| Field  | Value                        |
|--------|------------------------------|
| Name   | Anuj                         |
| Email  | alonesurvivor03@gmail.com    |
| Pass   | Admin@123                    |
| Role   | admin                        |

---

## Deployment (Free Hosting)

### Option A — InfinityFree / 000WebHost
1. Upload all PHP files via File Manager
2. Create MySQL DB in control panel
3. Import `schema.sql`
4. Update `config.php` with live credentials
5. Update `API` const in `index.html` to your live domain

### Option B — Railway / Render
1. Push to GitHub
2. Connect Railway → add MySQL plugin
3. Set env vars for DB credentials (update config.php to use `getenv()`)
4. Deploy

---

## API Reference

### POST /register.php
```json
{ "name": "Anuj", "email": "...", "password": "...", "role": "user" }
```
**Responses**: `201 Created` | `400 Validation` | `409 Duplicate Email` | `403 Admin Exists`

### POST /login.php
```json
{ "email": "...", "password": "..." }
```
**Responses**: `200 OK` | `400 Validation` | `401 Invalid credentials`

### GET /users.php
Auth required (session). Optional: `?role=admin`
**Response**: `{ success, total, summary, users[] }`

---

## Validation Rules

| Field    | Frontend                        | Backend                          |
|----------|---------------------------------|----------------------------------|
| Name     | Required, min 2 chars           | Required, 2–100 chars            |
| Email    | Required, regex format          | `FILTER_VALIDATE_EMAIL`          |
| Password | Min 8 chars, letters + numbers  | Same + `password_hash(BCRYPT)`   |
| Role     | Select (admin/user)             | In-array check + admin count     |

---

*Submitted by Anuj • alonesurvivor03@gmail.com*
