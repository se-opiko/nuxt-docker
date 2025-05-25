# PHPUnit導入ガイド

## 1. 環境構築

### 1.1 必要なパッケージのインストール
```bash
composer require --dev phpunit/phpunit
composer require --dev mockery/mockery
```

### 1.2 設定ファイルの作成
`phpunit.xml`を作成し、以下の基本設定を行う：
```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="./vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
>
    <testsuites>
        <testsuite name="Unit">
            <directory suffix="Test.php">./tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory suffix="Test.php">./tests/Feature</directory>
        </testsuite>
    </testsuites>
    <coverage processUncoveredFiles="true">
        <include>
            <directory suffix=".php">./app</directory>
        </include>
    </coverage>
</phpunit>
```

## 2. テスト環境の設定

### 2.1 テスト用データベースの準備
1. テスト用のデータベースを作成
2. `.env.testing`ファイルを作成し、テスト用のDB設定を記述：
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_test_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 2.2 テスト用のディレクトリ構造
```
tests/
├── Feature/          # 機能テスト
│   └── Controllers/  # コントローラーのテスト
└── Unit/            # ユニットテスト
```

## 3. テストの基本構造

### 3.1 テストクラスの基本構造
```php
<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class YourControllerTest extends TestCase
{
    use RefreshDatabase; // テスト後にDBをリフレッシュ

    protected function setUp(): void
    {
        parent::setUp();
        // テストの前準備
    }

    public function test_something(): void
    {
        // テストコード
    }
}
```

### 3.2 よく使用するアサーション
- `assertStatus()`: HTTPステータスコードの検証
- `assertJson()`: JSONレスポンスの検証
- `assertDatabaseHas()`: データベースにデータが存在することを検証
- `assertDatabaseMissing()`: データベースにデータが存在しないことを検証

## 4. テストの実行

### 4.1 基本的な実行コマンド
```bash
# すべてのテストを実行
php artisan test

# 特定のテストファイルを実行
php artisan test tests/Feature/Controllers/YourControllerTest.php

# 特定のテストメソッドを実行
php artisan test --filter test_something
```

### 4.2 テスト用の環境変数を使用
```bash
php artisan test --env=testing
```

## 5. ベストプラクティス

### 5.1 テストの独立性
- 各テストは独立して実行できるようにする
- テスト間でデータが干渉しないようにする
- `RefreshDatabase`トレイトを使用してDBをリセット

### 5.2 テストデータの準備
- `setUp`メソッドでテストデータを準備
- 必要に応じてファクトリーを使用
- テストデータは必要最小限に

### 5.3 アサーションの使い方
- 具体的なアサーションを使用
- エラーメッセージを明確に
- 複数のアサーションを組み合わせて検証

## 6. トラブルシューティング

### 6.1 よくある問題
1. データベース接続エラー
   - テスト用DBの設定を確認
   - マイグレーションが実行されているか確認

2. テストが失敗する
   - テストデータが正しく準備されているか確認
   - アサーションの条件が正しいか確認

3. 環境変数の問題
   - `.env.testing`の設定を確認
   - テスト実行時に正しい環境変数が読み込まれているか確認

### 6.2 デバッグ方法
- `dd()`や`dump()`を使用して変数の内容を確認
- テストの実行ログを確認
- データベースの状態を確認

