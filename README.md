# Rese（リーズ）

概要説明
飲食店予約サービス
会員登録者様は飲食店の予約（日付・時間・人数）ができ、会員登録をしていない場合も飲食店の店名・地域・ジャンル・概要等を見ることができます。<br>
会員登録者様はお気に入り登録をすることで、自分の好みに合った飲食店を探しやすくすることもできます。


## 目的
自社で飲食店予約サービスを運営

## アプリケーションURL
https://github.com:syakuyaku514/advanced-case

## 関連リポジトリ
https://github.com:syakuyaku514/advanced-case

## 機能一覧
* ユーザー別ログイン（認証機能）

## 使用技術（実行環境）
* PHP 7.4.9（使用言語）
* Laravel 8.83.8（フレームワーク）
* MySQL 8.0.26


# ER図
![ER図](https://github.com/user-attachments/assets/142daaaa-5579-4d65-80b9-4b1cdd888fb3)






# 環境構築
Dockerビルド
1. `git clone git@github.com:syakuyaku514/advanced-case.git`
2. DockerDesktopアプリを立ち上げる
3. `docker-composer up -d --build`
4. DockerDesktopアプリでコンテナが作成されているか確認

###Laravel環境構築
1. `docker-composer exec php bash`
2. `composer install`
3. [.env.example]ファイルを[.env]ファイルに命名変更。<br>`cp .env.example .env`<br>または、新しく.envファイルを作成。
4. .envに以下の環境変数を追加
```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
5. アプリケーションキーの作成
```
php artisan key:generate
``` 
6. マイグレーションの実行
```
php artisan migrate
```
7. シーディングの実行 
```
php artisan db:seed
```
8. シンボリックリンクの作成 
```
php artisan storage:link
```


## その他
#### URL
* 開発環境    : http://localhost/
* phpMyAdmin  : http://localhost:8080/

