# nuxt-docker

## 📋 プロジェクト概要

`nuxt-docker`は、DockerでコンテナオーケストレーションされたフルスタックTodoアプリケーションです。モダンなフロントエンド技術とスケーラブルなバックエンドAPIを組み合わせ、効率的な開発環境とデプロイメントを実現します。

## 🏗️ アーキテクチャ

### システム構成図
```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │   Backend API   │    │   Database      │
│   (Nuxt.js)     │◄──►│   (Laravel)     │◄──►│   (MySQL)       │
│   Port: 80      │    │   Port: 9000    │    │   Port: 3306    │
└─────────────────┘    └─────────────────┘    └─────────────────┘
        ▲                        ▲                        ▲
        │                        │                        │
        └────────────── Nginx Reverse Proxy ──────────────┘
                        Port: 80/9000
```

## 🚀 技術スタック

### フロントエンド (`front/`)
- **Framework**: [Nuxt.js 3](https://nuxt.com/) - Vue.jsベースのフルスタックフレームワーク
- **UI Library**: [Element Plus](https://element-plus.org/) - Vue 3向けデザインシステム
- **Language**: TypeScript
- **Styling**: UnoCSS + Sass
- **State Management**: Pinia
- **Testing**: Vitest + Vue Test Utils
- **Linting**: ESLint + TypeScript ESLint

### バックエンド (`api/`)
- **Framework**: [Laravel 12](https://laravel.com/) - PHPフレームワーク
- **Language**: PHP 8.2+
- **Authentication**: Laravel Sanctum
- **Testing**: PHPUnit
- **Code Quality**: Laravel Pint

### インフラストラクチャ
- **Containerization**: Docker + Docker Compose
- **Web Server**: Nginx (リバースプロキシ)
- **Database**: MySQL 8.0
- **CI/CD**: GitHub Actions

## 📁 プロジェクト構造

```
nuxt-docker/
├── front/                      # フロントエンドアプリケーション
│   ├── components/             # Vueコンポーネント
│   ├── composables/            # Composition APIユーティリティ
│   ├── pages/                  # ページコンポーネント
│   ├── types/                  # TypeScript型定義
│   ├── utils/                  # ユーティリティ関数
│   ├── nuxt.config.ts          # Nuxt設定
│   └── package.json            # フロントエンド依存関係
├── api/                        # バックエンドAPI
│   ├── app/                    # Laravelアプリケーション
│   ├── config/                 # 設定ファイル
│   ├── database/               # データベースマイグレーション・シーダー
│   ├── routes/                 # ルート定義
│   ├── tests/                  # PHPUnitテスト
│   └── composer.json           # PHP依存関係
├── docker/                     # Docker設定ファイル
│   ├── api/                    # APIコンテナ設定
│   ├── nuxt/                   # Nuxtコンテナ設定
│   ├── nginx/                  # Nginxコンテナ設定
│   └── db/                     # データベース設定
├── .github/workflows/          # GitHub Actions CI/CD
├── docker-compose.yml          # Docker Compose設定
└── README.md                   # プロジェクトドキュメント
```

## 🛠️ セットアップ

### 前提条件
- [Docker](https://www.docker.com/) & Docker Compose
- [Git](https://git-scm.com/)

### インストール手順

1. **リポジトリのクローン**
   ```bash
   git clone https://github.com/se-opiko/nuxt-docker.git
   cd nuxt-docker
   ```

2. **環境変数の設定**
   ```bash
   # APIの環境変数設定
   cp api/.env.example api/.env
   ```

3. **Docker環境の起動**
   ```bash
   # 全サービスの起動
   docker compose up -d
   
   # 初回のみ：依存関係のインストール
   docker compose exec -T api composer install
   ```

4. **データベースのセットアップ**
   ```bash
   # マイグレーションの実行
   docker compose exec api php artisan migrate
   
   # シーダーの実行（オプション）
   docker compose exec api php artisan db:seed
   ```

5. **アプリケーションへのアクセス**
   - フロントエンド: http://localhost
   - API: http://localhost:9000

## 🔧 開発環境

### 開発サーバーの起動
```bash
# 全サービスの起動
docker compose up -d

# ログの確認
docker compose logs -f

# 特定のサービスのログを確認
docker compose logs -f nuxt
docker compose logs -f api
```

### コマンド一覧

#### フロントエンド
```bash
# 開発サーバー起動
docker compose exec nuxt npm run dev

# ビルド
docker compose exec nuxt npm run build

# テスト実行
docker compose exec nuxt npm run test

# Linting
docker compose exec nuxt npm run lint
```

#### バックエンド
```bash
# Artisanコマンド
docker compose exec api php artisan [command]

# テスト実行
docker compose exec api vendor/bin/phpunit

# 依存関係の更新
docker compose exec api composer update

# コードフォーマット
docker compose exec api vendor/bin/pint
```

#### データベース
```bash
# マイグレーション
docker compose exec api php artisan migrate

# マイグレーションのロールバック
docker compose exec api php artisan migrate:rollback

# シーダー実行
docker compose exec api php artisan db:seed
```

## 🧪 テスト

### 自動テスト実行
GitHub Actionsにより、以下のタイミングで自動的にテストが実行されます：
- `main`ブランチへのプッシュ時
- `main`ブランチへのPull Request作成時

### 手動テスト実行

#### バックエンドテスト
```bash
# 全てのテストを実行
docker compose exec api vendor/bin/phpunit

# 特定のテストを実行
docker compose exec api vendor/bin/phpunit tests/Feature/TaskTest.php

# カバレッジレポート生成
docker compose exec api vendor/bin/phpunit --coverage-html coverage
```

#### フロントエンドテスト
```bash
# 単体テスト実行
docker compose exec nuxt npm run test

# カバレッジ付きテスト
docker compose exec nuxt npm run test -- --coverage
```

## 📱 機能

### 実装済み機能
- ✅ タスクの作成・編集・削除
- ✅ タスクの検索機能
- ✅ ステータス別タスク表示（未着手・進行中・完了）
- ✅ レスポンシブデザイン
- ✅ 日本語UI対応

### 今後の予定
- [ ] ユーザー認証・認可
- [ ] タスクの優先度設定
- [ ] タスクの期限設定
- [ ] リアルタイム更新
- [ ] PWA対応

## 🔧 設定

### 環境変数

#### API (.env)
```env
APP_NAME=NuxtDockerAPI
APP_ENV=local
APP_KEY=base64:generated_key
APP_DEBUG=true
APP_URL=http://localhost:9000

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=myapp
DB_USERNAME=user
DB_PASSWORD=password
```

#### Docker Compose (.env)
```env
WEB_PORT=80
```

## 🚀 デプロイ

### 本番環境デプロイ
```bash
# 本番用ビルド
docker compose -f docker-compose.prod.yml up -d

# 環境変数の設定
# APP_ENV=production に設定
# APP_DEBUG=false に設定
```

## 🤝 コントリビューション

### 開発フロー
1. Issueの作成
2. フィーチャーブランチの作成 (`feature/feature-name`)
3. 実装・テスト
4. Pull Requestの作成
5. コードレビュー
6. マージ

### コードレビューガイドライン
プロジェクトルートの[コードレビューガイドライン](./docs/code-review-guidelines.md)を参照してください。

### コミットメッセージ規約
```
type(scope): subject

body

footer
```

**Type:**
- `feat`: 新機能
- `fix`: バグ修正  
- `docs`: ドキュメント
- `style`: スタイル変更
- `refactor`: リファクタリング
- `test`: テスト
- `chore`: その他

## 📄 ライセンス

MIT License

## 👥 メンテナー

- [@se-opiko](https://github.com/se-opiko)

## 📞 サポート

質問やバグ報告は[Issues](https://github.com/se-opiko/nuxt-docker/issues)にお願いします。

---

**Last Updated**: 2025年6月7日
