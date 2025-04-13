[新店舗追加テスト - シート.csv](https://github.com/user-attachments/files/19722541/-.csv)# Rese（リーズ）

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
* （一般ユーザー）ログイン、ログアウト
* （一般ユーザー）飲食店お気に入り登録、解除
* （一般ユーザー）飲食店来店予約、予約変更、予約解除
* （一般ユーザー）来店確認QRコード
* （一般ユーザー）事前支払機能
* （一般ユーザー）飲食店検索機能
* （一般ユーザー）飲食店口コミ投稿、修正、削除
* （一般ユーザー）飲食店並び替え機能
* （店舗ユーザー）飲食店一覧閲覧
* （店舗ユーザー）飲食店舗新規作成、情報更新、削除
* （店舗ユーザー）一般ユーザー予約情報確認
* （管理ユーザー）店舗代表者作成
* （管理ユーザー）新規店舗作成インポート機能
* （管理ユーザー）一般ユーザーへのメール送信機能
* （管理ユーザー）一般ユーザー口コミ削除機能


## 使用技術（実行環境）
* PHP 7.4.9（使用言語）
* Laravel 8.83.8（フレームワーク）
* MySQL 8.0.26


# ER図
![ER図](https://github.com/user-attachments/assets/ba807036-586d-4276-9240-6606e8c1416f)

# 新店舗追加csvフォーマット
- `store`: 店舗の名前 (文字列)
- `region_id`: 店舗が所在する地域 (例: 東京都、大阪府、福岡県など) (文字列)
- `genre_id`: 店舗のジャンル (例: 寿司、焼肉、ラーメン、イタリアン、居酒屋など) (文字列)
- `overview`: 店舗の概要説明 (文字列)
- `image`: 店舗の画像のパス (例: `storage/images/sushi.jpg`) (文字列)

# テスト用新店舗追加CSV
https://docs.google.com/spreadsheets/d/1odyPMABdT3TGWr7a8r5s-T2Jtn90kg8-_ORvqcQEb_c/edit?gid=0#gid=0


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
2025/3　一般ユーザー口コミ機能を追加
（一般ユーザーは店舗来店予定日時終了後、一店舗に対し一つの口コミを投稿し、自分の投稿を修正・削除できる。管理者ユーザーは一般ユーザーの口コミを削除することができる）

#### URL
* 開発環境    : http://localhost/
* phpMyAdmin  : http://localhost:8080/
* 管理者ページテスト登録　　　　　: http://localhost/admin/register
* 管理者ページテストログイン　　　：http://localhost/admin/login
* 管理者ページテスト、本番どちらもメールアドレス：test@taro.com、パスワードpasswordtaroでログインできます。
* 店舗代表者ページテストログイン　：http://localhost/owner/login
* 店舗代表者は管理者ページで作成したメールアドレスとパスワードでログインしてください。

