<?php

namespace Todo\Controllers;

use Todo\Core\Connection;
use Todo\Core\AuthService;

class AdminController
{
    private AuthService $auth;

    public function __construct(Authservice $auth){
        $this->auth = $auth;
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function logout(){
        if (session_status() == PHP_SESSION_ACTIVE) {
            $this->auth->logout();
        }
    }
    // Admin dashboard betöltése
    public function showDashboard() {
        if (!$this->auth->isAuthenticated() || !$this->auth->isAdmin()) {
            header("Location: /");
            exit;
        }

        $content = renderView('admin_dashboard', [
            'user'  => $this->auth->getUser(),
        ]);

        if (isAjaxRequest()) return $content;

        return renderLayout($content, [
            'user' => $this->auth->getUser(),
            'extraCss' => '',
        ]);
    }
    
    public function login(){
        if ($this->auth->isAdmin()) {
            header("Location: /admin/dashboard");
            exit;
        }elseif($this->auth->isAuthenticated()){
            header("Location: /user/tasks");
            exit;
        }
        
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $this->auth->login($email, $password); // Ha sikeres a login role-t ad vissza, ha nem akkor false-t.
            if ($role == 'admin') {
                header("Location: /admin/dashboard");
                exit;
            }elseif($role == 'user'){
                header("Location: /user/tasks");
                exit;
            }else{
                $error = "Hibás e-mail vagy jelszó!";
            }
        }

        $content = renderView('login', [
            'user'  => $this->auth->getUser(),
            'error' => $error,
        ]);

        if (isAjaxRequest()) return $content;
    
        return renderLayout($content, [
            'user' => $this->auth->getUser(),
            'extraCss' => '',
        ]);

    }

}