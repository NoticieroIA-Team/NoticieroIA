<?php
// app/controllers/AuthController.php

class AuthController extends Controller
{
    private $userModel;

    public function __construct()
    {
        require_once '../app/models/User.php';
        $this->userModel = new User();
        session_start();
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $user = $this->userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['usuario'] = [
                    'id'     => $user['id'],
                    'nombre' => $user['nombre'],
                    'email'  => $user['email'],
                ];
                header('Location: ' . BASE_URL . 'home/index');
                exit;
            } else {
                $error = 'Credenciales incorrectas';
                $this->view('auth/login', ['error' => $error]);
            }
        } else {
            $this->view('auth/login');
        }
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre   = trim($_POST['nombre'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm  = $_POST['confirm_password'] ?? '';

            $errores = [];

            if ($password !== $confirm) {
                $errores[] = 'Las contraseñas no coinciden';
            }

            if ($this->userModel->findByEmail($email)) {
                $errores[] = 'El email ya está registrado';
            }

            if (empty($errores)) {
                if ($this->userModel->create($nombre, $email, $password)) {
                    header('Location: ' . BASE_URL . 'auth/login');
                    exit;
                } else {
                    $errores[] = 'Error al registrar el usuario';
                }
            }

            $this->view('auth/register', [
                'errores' => $errores,
                'nombre'  => $nombre,
                'email'   => $email,
            ]);
        } else {
            $this->view('auth/register');
        }
    }

    public function logout()
    {
        session_destroy();
        header('Location: ' . BASE_URL . 'home/index');
        exit;
    }
}
