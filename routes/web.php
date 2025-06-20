<?php
// Routeok definiálása
return [
    ['', [\Todo\Controllers\AdminController::class, 'login']],
    ['user/tasks', [\Todo\Controllers\GuestController::class, 'showUserHomePage']],
    ['user/task/status/{id}', [\Todo\Controllers\TasksController::class, 'updateStatus']],
    ['task/{id}', [\Todo\Controllers\GuestController::class, 'showTask']],
    ['admin/dashboard', [\Todo\Controllers\AdminController::class, 'showDashboard']],
    ['logout', [\Todo\Controllers\AdminController::class, 'logout']],
    ['admin/users', [\Todo\Controllers\UserController::class, 'handleRequest']], //user modify
    ['admin/users/{id}', [\Todo\Controllers\UserController::class, 'getUserById']],
    ['admin/tasks', [\Todo\Controllers\TasksController::class, 'handleRequest']], // task modify
    ['admin/task/{id}', [\Todo\Controllers\TasksController::class, 'getTaskById']],
];
