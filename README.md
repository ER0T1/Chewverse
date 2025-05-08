# Chewverse 專案說明文件

## 系統概述

Chewverse 是一個餐飲互動平台，結合了訂餐系統、AI 聊天功能和社交互動元素。使用者可以在平台上訂餐、與 AI 聊天、加入主題聊天室與其他用戶交流，以及管理自己的餐廳資訊。

## 安裝說明

### 系統需求

* PHP 8.4+
* Apache 2.4+
* Docker (可選)

### 使用 Docker 部署

1. 將專案檔案放置在本地目錄中
2. 啟動 Docker 容器:

   ```apache
   docker run --name php -p 8080:80 -v $(pwd):/var/www/html php:8.4-apache
   ```
3. 設定檔案權限:

   ```apache
   chown -R www-data:www-data database/restaurant.json database/users.json uploads/images
   chmod -R 755 database/restaurant.json database/users.json uploads/images
   ```

### 手動安裝

1. 將專案檔案複製到 Web 伺服器的根目錄
2. 確保以下檔案與目錄具有適當的讀寫權限以及擁有者:

   ```apache
   chown -R www-data:www-data database/restaurant.json database/users.json uploads/images
   chmod -R 755 database/restaurant.json database/users.json uploads/images
   ```

## 系統功能

### 主要功能

1. **用戶管理**
   * 註冊/登入
   * 個人資料設定
   * 密碼重設
2. **聊天功能**
   * AI 聊天 (`chat_with_ai.php`)
   * 雙人主題聊天 (`table_for_two.php`)
   * 多人主題聊天 (`join_table.php`)
3. **餐飲相關功能**
   * 訂餐系統 (`order.php`)
   * 餐廳內點餐 (`order_in_restaurant.php`)
   * AI 輔助點餐 (`order_with_ai.php`)
   * 結帳流程 (`checkout.php`)
4. **餐廳管理**
   * 新增/編輯餐廳資訊
   * 餐廳管理介面 (`management.php`)

### 資料存儲

系統使用 JSON 檔案進行資料存儲:

* `database/users.json`: 儲存用戶資料
* `database/restaurant.json`: 儲存餐廳資料
* `database/topic.json`: 儲存聊天主題

## 頁面說明

* `app/index.php`: 首頁
* `app/login.php` / `app/register.php`: 登入與註冊頁面
* `app/dashboard.php`: 使用者主控台
* `app/profile.php`: 個人檔案
* `app/settings.php`: 設定頁面
* `app/chat_with_ai.php`: AI 聊天
* `app/table_for_two.php`: 雙人主題聊天
* `app/join_table.php`: 多人主題聊天
* `app/order.php`: 點餐介面
* `app/order_in_restaurant.php`: 餐廳內點餐介面
* `app/scan_qr_code.php`: 掃描 QR 碼
* `app/checkout.php`: 結帳流程
* `app/management.php`: 餐廳管理頁面

## 重要的後端功能

主要的後端功能位於 `app/back-end` 目錄下:

* 使用者驗證與管理
* 餐廳資訊管理
* 密碼重設流程
* 頭像上傳與管理

## 開發注意事項

1. 請確保資料目錄權限正確以避免讀寫問題
2. 系統使用 `sessions` 進行使用者狀態管理
3. 所有頁面都應進行使用者身份驗證
4. 表單輸入應該進行適當的驗證與過濾

## 存取應用

應用程式運行後，可透過瀏覽器存取:

* 使用 Docker: [http://localhost:8080/app/](http://localhost:8080/app/)
* 本機安裝: 根據您的 Web 伺服器設定存取

## 附註

* 所有上傳的圖像存儲在 `uploads/images/` 目錄
* 系統使用 Bootstrap 作為前端框架
* 使用者需要登入才能存取大多數功能

---

![](https://avatars.githubusercontent.com/u/87009067?v=4)
