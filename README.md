# 📰 Laravel News Aggregator (Case Study)

A **Laravel 11 backend** project that aggregates news from multiple providers (**NewsAPI, The Guardian, New York Times**) and exposes clean REST APIs with authentication.

---

## ✨ Features
- Fetches live articles via **Jobs + Queue + Scheduler**
- Stores **sources, authors, categories, articles**
- Deduplication using a **fingerprint hash**
- REST API with advanced **filtering** (search, source, category, author, date)
- **User Preferences** (save preferred sources/categories/authors)
- **Sanctum authentication** with demo token endpoint
- **Clean architecture** (SOLID, Repository pattern, DRY, KISS)
- Includes **Postman collection** for quick API testing

---

## ⚙️ Installation

1. **Clone repository**
   ```bash
   git clone https://github.com/Neha-Khemchandani-52/laravel-news-aggregator.git
   cd laravel-news-aggregator
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install && npm run dev
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Update `.env` with your:
   - Database credentials
   - Redis config
   - API keys (NewsAPI, Guardian, NYT)

4. **Run migrations + seeders**
   ```bash
   php artisan migrate --seed
   ```

5. **Start server**
   ```bash
   php artisan serve
   ```

---

## 🔑 API Authentication

All APIs are protected with **Laravel Sanctum (Bearer tokens)**.

For demo/testing, use:
```http
GET /api/demo-token
```

This returns a token for a pre-seeded demo user.  

Use in Postman or curl:
```
Authorization: Bearer <token>
```

After that, you can test:
- `GET /api/articles`
- `GET /api/articles?q=technology`
- `GET /api/articles?q=apple&from=2025-09-27&to=2025-09-27`
- `GET /api/sources`
- `GET /api/categories`
- `GET /api/authors`
- `POST /api/user/preferences`
- `GET /api/user/preferences`

---

## 📬 Postman Collection

A ready-to-use Postman collection is included.

- File: `News-Aggregator-APIs.postman_collection.json`
- Usage:
  1. Import into Postman.
  2. Update `{{base_url}}`.
  3. Generate a token via `/api/demo-token` and paste into `{{token}}`.
  4. Run API requests (Articles, Sources, Categories, User Preferences).

---

## 🧪 Sample IDs for Testing Preferences

- **Sources**
  - 1 → NewsAPI
  - 2 → Guardian
  - 3 → New York Times

- **Categories**
  - 1 → Technology
  - 2 → Sports
  etc...

- **Authors**
  - 1 → Anonymous  

---

## ⏰ Scheduler + Queue Setup

### Scheduler
To run scheduled jobs (e.g., news fetching), add to your crontab:
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```
Added scheduled command in file `routes/console.php`

Or run manually:
```bash
php artisan schedule:run
```

### Queue Worker
Start the queue worker:
```bash
php artisan queue:work redis --tries=3
```

---

## 🖐 Manual Fetch Commands

You can also run fetches directly for testing:

```bash
# Fetch latest news from NewsAPI (all categories) and by default latest news articles will be fetched
php artisan news:fetch newsapi

# Fetch only technology news from NewsAPI
php artisan news:fetch newsapi technology
```

Jobs will be queued and processed by:
```bash
php artisan queue:work redis --tries=3
```

---

## 📄 Project Structure (Key Files)

- `app/Console/Commands/SyncLiveData.php` → Scheduler Command
- `app/Jobs/ProcessLiveData.php` → Job for fetching and saving articles  
- `app/Services/NewsProviders/` → Provider classes (NewsAPI, Guardian, NYT)  
- `app/Http/Controllers/Api/ArticleController.php` → Article endpoints  
- `database/seeders/SourceSeeder.php` → Seeds available sources  

---


## Sample Database Data
A sample MySQL dump is provided in folder (`sample_data_sql`) showing schema + a few rows 
from `articles`, `sources`, `authors`, `categories`,`article_category`, `user_preferences`, `users`


---

## 👨‍💻 Author

- Neha Khemchandani 
- [LinkedIn](https://www.linkedin.com/in/neha-khemchandani)  
- [Video Resume](https://www.youtube.com/watch?v=rC9s7ar-x_Y)
- Date : 28th September,2025

---

## 📜 License
This project is open-sourced for demo purposes.
