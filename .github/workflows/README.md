# GitHub Actionsの設定手順

## 1. 基本的なGitHub Actionsの導入方法

### 1.1 ワークフローファイルの作成
1. リポジトリのルートディレクトリに`.github/workflows`ディレクトリを作成
2. その中に`main.yml`などのワークフローファイルを作成

### 1.2 基本的なワークフローの構成
```yaml
name: sample workflow

on: 
  push: 
    branches: ["main"] 
  pull_request:
    branches: ["main"]
  workflow_dispatch: 

jobs:
  build:
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v3
      
      - name: Run a one-line script
        run: echo start build
```

### 1.3 主な設定項目の説明
- `name`: ワークフローの名前
- `on`: ワークフローを実行するトリガー
  - `push`: ブランチへのプッシュ時
  - `pull_request`: プルリクエスト作成時
  - `workflow_dispatch`: 手動実行時
- `jobs`: 実行する処理の定義
- `runs-on`: 実行環境の指定
- `steps`: 実行するステップの定義

## 2. PHPUnitテストの自動実行設定

### 2.1 テスト実行用のワークフロー設定
```yaml
name: sample workflow

on: 
  push: 
    branches: ["main"] 
  pull_request:
    branches: ["main"]
  workflow_dispatch: 

jobs:
  build:
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: mbstring, dom, fileinfo, mysql
          coverage: xdebug
          
      - name: Install Dependencies
        run: composer install -q --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist
        
      - name: Execute PHPUnit Tests
        run: vendor/bin/phpunit --coverage-text
```

### 2.2 設定の説明
1. **PHP環境のセットアップ**
   - `shivammathur/setup-php@v2`アクションを使用
   - PHP 8.2をインストール
   - 必要な拡張機能を指定
   - Xdebugを有効化（テストカバレッジ用）

2. **依存関係のインストール**
   - Composerを使用して依存関係をインストール
   - 最適化オプションを指定して高速化

3. **テストの実行**
   - PHPUnitを実行
   - カバレッジレポートを生成

### 2.3 実行タイミング
- `main`ブランチへのプッシュ時
- `main`ブランチへのプルリクエスト作成時
- 手動実行時（`workflow_dispatch`）

## 3. 注意点
- PHPのバージョンは必要に応じて変更
- 必要な拡張機能は`extensions`で指定
- テストの実行コマンドはプロジェクトの設定に合わせて調整
- 環境変数が必要な場合は`env`セクションで指定
