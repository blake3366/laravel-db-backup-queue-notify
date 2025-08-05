<!-- # Laravel DB Backup Queue Notify

This project is a Laravel-based solution for managing database backups. It utilizes queues to handle backup tasks efficiently and sends notifications upon completion or failure of the backup process.

## Features
- Automated database backup scheduling.
- Queue-based task management for scalability.
- Notification system for backup status updates.
- Configurable settings for backup storage and notification preferences.

## Requirements
- PHP 8.0 or higher
- Laravel 9.x
- Queue driver (e.g., Redis, SQS)
- Notification service (e.g., email, Slack)

## Usage
1. Configure the `.env` file with database and notification settings.
2. Set up the queue worker.
3. Schedule the backup tasks using Laravel's scheduler.
4. Monitor notifications for backup status. -->

# Laravel 12 Database Backup & Notification

> 自動備份資料庫，並透過 Mailgun 寄送 Email 通知、Line Message API 推播結果，所有任務都跑在 Queue 上。
## 功能

- 📦 定期備份資料庫  ex: PostrgreSQL, MySQL ...
- ✉️ 成功或失敗透過 Mailgun 寄送 Email 通知  
- 💬 同步透過 Line Message API 傳送結果到指定聊天室  
- 🛠️ 全部備份任務以 Laravel Queue 實作, 可水平擴展

---

## 前置需求

- PHP ≥ 8.1  
- Laravel 12  
- Composer  
- Redis / Database（做為 Queue driver）  
- Mailgun 帳號[需自行申請]
- Line Messaging API Channel[需自行申請]

## 安裝

1. 從 GitHub clone 專案  
   ```bash
   git clone https://github.com/yourname/laravel-db-backup.git
   cd laravel-db-backup
   ```

2. 安裝相依套件
   ```bash
   composer install
   ```
3. 複製環境設定檔
   ```bash
   cp .env.example .env
   ```
    環境設定
    請在 .env 裡面修改以下變數
    ### Mailgun
    也可以用自己想使用的email方式
    ```bash
    MAIL_MAILER=smtp
    MAIL_HOST=smtp.mailgun.org
    MAIL_PORT=587
    MAIL_USERNAME=yourmailgun_username
    MAIL_PASSWORD=yourmailgun_password
    MAIL_ENCRYPTION=tls
    MAIL_FROM_ADDRESS=yourmailgun_address
    MAIL_FROM_NAME=DatabaseBackup
    ```
    ### Line Message API
    ```bash
    LINE_CHANNEL_ACCESS_TOKEN=LineMessagingApiAccessToken
    LINE_RECIPIENT_ID=YourLineUserId
    ```
    ### Backup
    ```bash
    # 備份要通知的email
    BACKUP_NOTIFICATION_EMAIL=youremail
    # 備份資料庫設定
    BACKUP_DB_HOST=localhost
    BACKUP_DB_PORT=5432
    BACKUP_DB_DATABASE=database
    BACKUP_DB_USERNAME=root
    BACKUP_DB_PASSWORD=
    BACKUP_PG_DUMP_PATH=your_dump_path # ex: usr/bin/pg_dump
    BACKUP_STORAGE_PATH=public/backups
    ```
    ### Queue
    此專案是使用database
    ```bash
    QUEUE_CONNECTION=database
    ```
5. 執行資料庫遷移  
    在執行備份任務之前，請先建立 Queue 所需的資料表：  
    ```bash
    php artisan migrate
    ```
    此指令會根據 Laravel 預設的 Queue 設定，建立相關的資料表，例如 `jobs` 和 `failed_jobs`。

6. 啟動 Queue Worker  
    ```bash
    php artisan queue:work
    ```

7. 設定排程任務  
    確保你的伺服器有啟用 Laravel 的排程功能，例如在 Linux 系統中可以透過 `cron` 設定：  
    ```bash
    * * * * * php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1
    ```

8. 測試備份功能 
    可多種方式去測試, 但這邊是使用route<br>

    **使用測試路由**  
   在 `routes/web.php` 中新增一個測試路由：
   ```php
   Route::get('/test-backup', function () {
       Artisan::call('queue:listen');
       dispatch(new App\Jobs\DatabaseBackupJob());
       return "備份任務已加入隊列";
   });
   ```
   然後使用Postman或瀏覽器訪問 `http://your-app-url/test-backup`


9. 檢查通知  
    確保你能收到備份成功或失敗的通知,包含**Email**和**Line**推播。

---

## 注意事項

- **備份儲存空間**：請確保你的伺服器有足夠的儲存空間來保存備份檔案。
- **安全性**：建議將 `.env` 檔案設定為只有伺服器管理員可讀取，避免敏感資訊外洩。
- **排程頻率**：根據你的需求調整排程頻率，避免過於頻繁的備份影響系統效能。

---

## 常見問題

### 1. 無法收到 Email 通知？
- 確認 Mailgun 的帳號與密碼是否正確。
- 檢查 `.env` 檔案中的 Email 設定是否完整。

### 2. Line 推播失敗？
- 確認 Line Messaging API 的 Access Token 是否正確。
- 檢查 Recipient ID 是否為有效的 Line 使用者 ID。

### 3. Queue 無法正常執行？
- 確認 Queue driver 是否正確設定，例如 Redis 或 Database。
- 檢查 Queue Worker 是否有正常啟動。

---

## 參考
- [Laravel 官方文件](https://laravel.com/docs)
- [Mailgun 官方文件](https://documentation.mailgun.com/)
- [Line Messaging API 文件](https://developers.line.biz/en/docs/messaging-api/)
- [Queue 使用指南](https://laravel.com/docs/queues)
- [Laravel Scheduler 使用指南](https://laravel.com/docs/scheduling)