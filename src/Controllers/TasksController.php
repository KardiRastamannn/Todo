<?php

namespace Todo\Controllers;

use Todo\Models\TasksModel;
use Todo\Models\UserModel;
use Todo\Core\AuthService;

class TasksController
{
    private TasksModel $tasksModel;
    private UserModel $userModel;
    private AuthService $auth;

    public function __construct(TasksModel $tasksModel, UserModel $userModel, AuthService $auth)
    {
        $this->tasksModel = $tasksModel;
        $this->userModel = $userModel;
        $this->auth = $auth;
    }

    // Minden kérés ide érkezik
    public function handleRequest()
    {
        if (!$this->auth->isAdmin()) {
            header("Location: /");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $affectedRows = $this->processTasksRequest();

            if (isAjaxRequest()) {
                return $affectedRows;
            }

            header("Location: /admin/tasks"); 
            exit;
        }

        return $this->showTasks();
    }
    // Felhasználó státusz állításának updatelése.
    public function updateStatus(int $id){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['status'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Érvénytelen kérés']);
            return;
        }

        $status = trim($_POST['status']);
        return $this->tasksModel->updateStatus($id, $status);

    }

    // Egy task lekérdezése
    public function getTaskById($id)
    {
        return $this->tasksModel->getTaskById($id);
    }

    // Admin számára összes task + user lekérdezése
    public function showTasks()
    {
        if (!$this->auth->isAuthenticated() || !$this->auth->isAdmin()) {
            header("Location: /");
            exit;
        }

        $content = renderView('admin_tasks', [
            'tasks' => $this->tasksModel->getAllTasks(),
            'users' => $this->userModel->getAllUsers(),
        ]);

        if (isAjaxRequest()) return $content;

        return renderLayout($content, [
            'user' => $this->auth->getUser(),
            'extraCss' => ''
        ]);
    }

    // Task létrehozás vagy frissítés POST alapján
    private function processTasksRequest()
    {
        if (isset($_POST['delete_task_id'])) {
            return $this->tasksModel->deleteTask((int)$_POST['delete_task_id']);
        }

        if (isset($_POST['title'], $_POST['content'], $_POST['status'])) {
            $title = trim($_POST['title']);
            $content = trim($_POST['content']);
            $status = $_POST['status'];
            $userId = (int)$_POST['user_id'];
            if (!empty($_POST['task_id'])) {
                $taskId = (int)$_POST['task_id'];
                return $this->tasksModel->updateTask($taskId, $title, $content, $userId, $status);
            } else {
                return $this->tasksModel->createTask($userId, $title, $content, $status);
            }
        }

        return 0;
    }
}
