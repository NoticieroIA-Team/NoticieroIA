<?php
// conectamos con la BD
require_once __DIR__ . "/../db/db.php";

class user_model
{
    /** @var PDO */
    private $pdo;

    public function __construct()
    {
        // Ahora conectar() devuelve un PDO (MySQL)
        $this->pdo = Database::conectar();

        if (!$this->pdo) {
            throw new Exception("Error de conexión a la base de datos.");
        }
    }

    public function buscarPorEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function buscarPorDNI($dni)
    {
        $sql = "SELECT * FROM users WHERE dni = :dni LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':dni' => $dni]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function registrar($dni, $nombre, $apellidos, $numero_empresa, $email, $password)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users 
                    (dni, nombre, apellidos, numero_empresa, email, password)
                VALUES 
                    (:dni, :nombre, :apellidos, :numero_empresa, :email, :password)";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':dni'            => (string)$dni,
                ':nombre'         => (string)$nombre,
                ':apellidos'      => (string)$apellidos,
                ':numero_empresa' => (int)$numero_empresa,
                ':email'          => (string)$email,
                ':password'       => (string)$hash,
            ]);

            return true;
        } catch (PDOException $e) {
            // 23000 = violación de restricción (p.ej. UNIQUE)
            if ($e->getCode() === '23000') {
                // Asumimos clave única en email o dni
                return "duplicate";
            }

            error_log('Error al registrar usuario en MySQL: ' . $e->getMessage());
            return false;
        }
    }
}
