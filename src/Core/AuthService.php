<?php
namespace Todo\Core;

use Todo\Models\UserModel;

class AuthService
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->userModel = new UserModel($connection);

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function login(string $email, string $password): bool {
        // if($this->isLoginBlocked()) {
        //     throw new \Exception("Túl sok próbálkozás, próbálkozz később");
        // }

        $user = $this->userModel->getUserByEmail($email);

        if (empty($user)) {
           // $this->recordFailedAttempt();
            return false;
        }

        $user = $user[0];

        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['user_id'],
                'email' => $user['email'],
                'role' => $user['role']
            ];
            return $user['role'];
        }
        //$this->recordFailedAttempt();
        return false;
    }

    public function logout(): void {
        session_destroy();
        header("Location: /");
        exit;
    }

    public function isAuthenticated(): bool {
        return isset($_SESSION['user']);
    }

    public function isAdmin(): bool {
        return $this->isAuthenticated() && $_SESSION['user']['role'] === 'admin';
    }

    public function getUser(): ?array {
        return $_SESSION['user'] ?? null;
    }

    private function isLoginBlocked(): bool {
        $key = 'login_attempts_' . $_SERVER['REMOTE_ADDR'];
        $attempts = $_SESSION[$key] ?? 0;
        return $attempts >= 5;
    }

    private function recordFailedAttempt(): void {
        $key = 'login_attempts_' . $_SERVER['REMOTE_ADDR'];
        $_SESSION[$key] = ($_SESSION[$key] ?? 0) + 1;
    }
}