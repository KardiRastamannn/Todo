<?php

namespace Todo\Controllers;
use Todo\Core\Connection;
use Todo\Models\TasksModel;
use Todo\Core\AuthService;

class GuestController
{
    private TasksModel $tasksModel;
    private AuthService $auth;

    public function __construct(TasksModel $tasksModel, AuthService $auth){
        $this->tasksModel = $tasksModel;
        $this->auth = $auth;
    }
    //User homepage betöltése
    public function showUserHomePage(){
 
        $user = $this->auth->getUser();
        $content = renderView('user_home', [
            'tasks' => $this->tasksModel->getAssignedUserTasks($user['id']),
            'user'  => $user,
        ]);

        if (isAjaxRequest()) return $content;    

        return renderLayout($content, [
            'user' => $user,
            'extraCss' => '',
        ]);
    }
}