# coachtechフリマ

## 環境構築

### Docker環境構築

git clone git@github.com:riichikawashima-cmd/coachtech-freemarket.git
cd coachtech-freemarket
docker compose up -d --build
docker compose exec php bash
composer install
php artisan key:generate
php artisan migrate

- アプリ：http://localhost
- phpMyAdmin：http://localhost:8080

## 使用技術（実行環境）
- PHP 8.x
- Laravel 10.x
- MySQL 8.0
- Docker / docker-compose
- Nginx

## ER図
（後ほど画像を追加）

## URL
（後ほど追加）

