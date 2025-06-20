<?php

namespace Todo\Models;

use Todo\Core\Connection;

class TasksModel
{
    private Connection $connection;

    public function __construct(Connection $connection){
        $this->connection = $connection;
    }

    // Egy adott task lekérdezése ID alapján
    public function getTaskById(int $taskId): ?array{
        $sql = "SELECT * FROM tasks WHERE task_id = ?";
        $result = $this->connection->pdoSelect($sql, [$taskId]);
        return $result[0] ?? null;
    }

    // Új task beszúrása
    public function createTask(int $userId, string $title, string $content, string $status = 'pending'): int{
        $sql = "INSERT INTO tasks (user_id, title, content, status) VALUES (?, ?, ?, ?)";
        return $this->connection->pdoQuery($sql, [$userId, $title, $content, $status]);
    }

    // Task frissítése
    public function updateTask(int $taskId, string $title, string $content, int $userId, string $status = 'pending'): int{
        $sql = "UPDATE tasks SET title = ?, content = ?, status = ?, update_at = CURRENT_TIMESTAMP, user_id = ? WHERE task_id = ?";
        return $this->connection->pdoQuery($sql, [$title, $content, $status, $userId, $taskId]);
    }

    // Task törlése
    public function deleteTask(int $taskId): int{
        $sql = "DELETE FROM tasks WHERE task_id = ?";
        return $this->connection->pdoQuery($sql, [$taskId]);
    }

    // Összes task lekérése (pl. admin számára)
    public function getAllTasks(): array{
        $sql = "SELECT * FROM tasks LEFT JOIN users on tasks.user_id = users.user_id ORDER BY created_at DESC";
        return $this->connection->pdoSelect($sql);
    }

    // Egy userhez rendelt taskok lekérdezése (például a főoldalra)
    public function getAssignedUserTasks(int $userId): array{
        $sql = "SELECT * FROM tasks WHERE user_id = ?";
        return $this->connection->pdoSelect($sql, [$userId]);
    }
    // User oldali task státusz frissítése
    public function updateStatus(int $taskId, string $status): int{
        $sql = "UPDATE tasks SET status = :status, update_at = NOW() WHERE task_id = :task_id";
        return $this->connection->pdoQuery($sql, [$status, $taskId]);
    }
}
