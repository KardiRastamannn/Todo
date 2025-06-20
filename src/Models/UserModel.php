<?php
namespace Todo\Models;

use Todo\Core\Connection;

class UserModel
{
    private Connection $db;

    public function __construct(Connection $connection){
        $this->db = $connection;
    }

    // Lekérdezi a felhasználót e-mail alapján 
    public function getUserByEmail(string $email): ?array{
        $stmt = $this->db->pdoSelect(
            "SELECT * FROM users WHERE email = :email LIMIT 1",
            ['email' => $email]
        );
        return $stmt;
    }

    // Lekérdezi a felhasználót ID alapján 
    public function getUserById(int $id): ?array{
        return $this->db->pdoSelect(
            "SELECT user_id, email, role, is_active FROM users WHERE user_id = :user_id LIMIT 1",
            ['user_id' => $id]
        );
    }

    // Minden felhasználó lekérdezése
    public function getAllUsers(): array{
        return $this->db->pdoSelect("SELECT * FROM users");
    }

    // Felhasználó adatainak frissítése ID alapján
    public function updateUser(int $id, string $email, string $role, string $password): ?int{
       return $this->db->pdoQuery(
            "UPDATE users SET email = ?, role = ?, password = ? WHERE user_id = ?",
            [$email, $role, password_hash($password, PASSWORD_DEFAULT), $id]
        );
    }

    // Felhasználó törlése ID alapján
    public function deleteUser(int $id): ?int{
        return $this->db->pdoQuery(
            "DELETE FROM users WHERE user_id = ?",
            [$id]
        );
    }

    // Új felhasználó létrehozása, jelszó titkosítása bcrypt-tel
    public function createUser(string $email, string $password, string $role = 'user'): ?int {
        // $this->validatePassword($password); // opcionálisan bekapcsolható
        return $this->db->pdoQuery(
            "INSERT INTO users (email, password, role) VALUES (?, ?, ?)",
            [$email, password_hash($password, PASSWORD_BCRYPT), $role]
        );
    }

    // Opcionális jelszó validátor (min. 8 karakter, legalább 1 nagybetű és 1 szám)
    private function validatePassword(string $password): void {
        if (strlen($password) < 8) {
            throw new \InvalidArgumentException("A jelszónak minimum 8 karakter hosszúnak kell lennie");
        }

        if (!preg_match('/[A-Z]/', $password) || 
            !preg_match('/[0-9]/', $password)) {
            throw new \InvalidArgumentException("A jelszónak tartalmaznia kell számot és nagybetűt");
        }
    }
}
