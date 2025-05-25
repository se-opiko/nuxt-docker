<?php

namespace Tests\Feature\Controllers;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase; // テスト後にDBをリフレッシュする

    /**
     * テスト用のデータをセットアップ
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // テスト用のデータを作成
        Task::create([
            'title' => 'テストタスク1',
            'description' => 'テストタスク1の説明',
            'priority' => 1,
            'status' => 1
        ]);
    }

    /**
     * タスク一覧の取得テスト
     */
    public function test_index()
    {
        $response = $this->getJson('/api/tasks');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'tasks' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'priority',
                        'status',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

    /**
     * タスクの作成テスト
     */
    public function test_store()
    {
        $taskData = [
            'title' => '新規タスク',
            'description' => '新規タスクの説明',
            'priority' => 2,
            'status' => 1
        ];

        $response = $this->postJson('/api/tasks/store', $taskData);
        
        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'task' => [
                    'id',
                    'title',
                    'description',
                    'priority',
                    'status',
                    'created_at',
                    'updated_at'
                ]
            ]);

        // DBにデータが保存されていることを確認
        $this->assertDatabaseHas('tasks', [
            'title' => $taskData['title'],
            'description' => $taskData['description']
        ]);
    }

    /**
     * タスクの更新テスト
     */
    public function test_update()
    {
        $task = Task::first();
        $updateData = [
            'title' => '更新されたタスク',
            'description' => '更新された説明',
            'priority' => 3,
            'status' => 2
        ];

        $response = $this->patchJson("/api/tasks/{$task->id}", $updateData);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'task' => [
                    'id',
                    'title',
                    'description',
                    'priority',
                    'status',
                    'created_at',
                    'updated_at'
                ]
            ]);

        // DBのデータが更新されていることを確認
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => $updateData['title'],
            'description' => $updateData['description']
        ]);
    }

    /**
     * タスクの削除テスト
     */
    public function test_destroy()
    {
        $task = Task::first();
        
        $response = $this->deleteJson("/api/tasks/{$task->id}");
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message'
            ]);

        // DBからデータが削除されていることを確認
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id
        ]);
    }
}
