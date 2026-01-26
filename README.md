# 勤怠管理アプリ

---

## 環境構築手順

## Dockerビルド
git clone https://github.com/hiro869/attendance-app.git

cd attendance-app

docker compose up -d --build

## 🌱 Laravel セットアップ

docker compose exec app bash

composer install

cp .env.example .env

php artisan key:generate

php artisan migrate --seed

## テスト実行方法

以下のコマンドで PHPUnit のテストを実行できます。

php artisan test


## テスト用ログイン情報

### 管理者ユーザー
- Email：admin@test.com
- Password：password

### 一般ユーザー
- Email：user@test.com
- Password：password

## 補足事項（要件外対応）

- 勤怠データが存在しない日の詳細画面では
  「勤怠データが存在しないため修正できません」と表示されます。

- テストケース11について、
  機能要件と差異があったため機能要件を優先して実装・テストを行いました。


## 使用技術（実行環境）

- PHP：8.3.28

- Laravel：12.39.0

- MySQL：8.0.26

- Nginx：1.25.3

- Docker / Docker Compose

- Mailhog（メール確認用）

- phpMyAdmin

## 🌐 開発環境URL

種類	URL
アプリケーション	http://localhost/login

Mailhog（メール確認）	http://localhost:8025

phpMyAdmin（DB確認）	http://localhost:8080

## ER図

![ER図](src/public/images/er_diagram.png)


