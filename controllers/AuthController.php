<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/User.php';

/**
 * Controlador de autenticación.
 *
 * Maneja el login y logout de usuarios.
 * Acciones disponibles:
 *   - login()   GET:  muestra el formulario de login.
 *               POST: procesa las credenciales.
 *   - logout()  GET:  destruye la sesión y redirige al login.
 */
class AuthController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function login(): void
    {
        // Si ya hay sesión activa, redirigir según rol
        if (isset($_SESSION['user_id'])) {
            $this->redirectByRole($_SESSION['user_rol']);
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim($_POST['email']    ?? '');
            $password =      $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $error = 'Todos los campos son obligatorios.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'El formato del correo no es válido.';
            } else {
                $user = $this->userModel->findByEmail($email);

                if ($user && password_verify($password, $user['password'])) {
                    // Regenerar ID de sesión para prevenir session fixation
                    session_regenerate_id(true);

                    $_SESSION['user_id']     = $user['id'];
                    $_SESSION['user_nombre'] = $user['nombre'];
                    $_SESSION['user_email']  = $user['email'];
                    $_SESSION['user_rol']    = $user['rol'];

                    $this->redirectByRole($user['rol']);
                }

                // Mismo mensaje para usuario no encontrado o contraseña incorrecta
                // (evita revelar qué campo es incorrecto)
                $error = 'Credenciales incorrectas.';
            }
        }

        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        header('Location: index.php');
        exit;
    }

    /**
     * Redirige al usuario según su rol tras un login exitoso.
     */
    private function redirectByRole(string $rol): void
    {
        header('Location: index.php?controller=solicitud&action=index');
        exit;
    }
}
