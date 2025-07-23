# futurAPI# 🎨 Symfony API for Angular NFT Marketplace

This project is a **RESTful API built with Symfony**, serving as the backend for an **Angular-based NFT marketplace**.  
It includes an **admin dashboard**, data visualization with **Chart.js**, and asset compilation using **Webpack Encore**.

---

## ⚙️ Tech Stack

- **Symfony** (recommended: version 5.4)
- **PHP 7+**
- **PhpMyAdmin**
- **Webpack Encore** for asset bundling
- **Bootstrap 5** for the admin interface
- **Chart.js** for dynamic data visualization
- **TypeScript** supported

---

## 🚀 Quick Start

### 1. Clone the project

```bash
git clone https://github.com/your-username/your-project.git
cd your-project
```

###  2. Install PHP dependencies

```bash
composer install
```

###  3. Create the database

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

###  4. Install frontend dependencies

```bash
npm install
```

###  5. Start the project

```bash
npm run dev          # Compile assets
symfony server:start
```

##  📦 Available Scripts (package.json)

Script	Description
npm run dev	Compile assets in development mode
npm run watch	Watch for changes and recompile
npm run build	Compile assets for production
npm run dev-server	Run the Webpack dev server with hot reload

##  🎯 Frontend Stack (Bundled)

Webpack Encore for asset management

Bootstrap 5 for UI components

TypeScript

Chart.js for advanced graphs

##  🛠 Admin Interface
The admin panel is available at /admin (or your custom route). It allows:

Managing users & datas

Tracking NFT transactions

Displaying statistics with Chart.js

Full CRUD on entities

##  🔐 Security & Authentication
The project uses JWT authentication (or another configurable method) to protect API endpoints.
User roles like ROLE_ADMIN and ROLE_USER are supported.

##  🔗 REST API
The API exposes all resources needed for the Angular frontend: users, NFTs, transactions, etc.
You can use API Platform or create manual controllers like ApiController.php.

##  📂 Project Structure

```php
├── assets/               # Frontend files (js/ts/scss)
├── src/                  # Symfony PHP source
│   ├── Controller/
│   ├── Entity/
│   └── Repository/
├── templates/            # Twig templates (admin panel)
├── public/               # Public-facing assets
├── config/               # Symfony configuration
└── package.json          # Frontend dependencies and scripts
```

##  👨‍💻 Author
Developed by Joris Bertier

##  📝 License
This project is licensed UNLICENSED – for private/internal use only.


---

Let me know if you'd like a version tailored for **public open-source release** (with MIT license)
