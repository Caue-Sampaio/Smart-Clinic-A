<?php
session_start();
$error = '';
$storedEmail = '';
$selectedRole = '';

require_once dirname(__DIR__) . '/Backend/controller/Database.php';

function authenticateUser(string $role, string $email, string $password): ?array {
    $db = Database::getConnection();

    if ($role === 'paciente') {
        $sql = 'SELECT cod, nome, email, cpf, senha FROM paciente WHERE email = :email LIMIT 1';
    } else {
        $sql = 'SELECT cod, nome, email, cpf, crm, senha FROM medico WHERE email = :email LIMIT 1';
    }

    $stmt = $db->prepare($sql);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && $user['senha'] === $password) {
        return $user;
    }

    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = trim($_POST['role'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $storedEmail = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $selectedRole = $role;

    if ($role === '' || $email === '' || $password === '') {
        $error = 'Por favor, preencha todos os campos.';
    } else {
        try {
            $user = authenticateUser($role, $email, $password);
            if ($user) {
                $_SESSION['role'] = $role;
                $_SESSION['user_id'] = $user['cod'];
                $_SESSION['user_name'] = $user['nome'];
                $_SESSION['user_email'] = $user['email'];

                if ($role === 'paciente') {
                    header('Location: paciente_dashboard.php');
                    exit;
                }
                if ($role === 'medico') {
                    header('Location: medico_dashboard.php');
                    exit;
                }
            }

            $error = 'Email ou senha incorretos.';
        } catch (PDOException $e) {
            $error = 'Erro ao conectar ao banco de dados. Tente novamente mais tarde.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART CLINIC - Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

    <style>
        :root {
            --azul: #2563eb;
            --azul-escuro: #1e40af;
            --verde: #22c55e;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.95) 0%, rgba(34, 197, 94, 0.95) 100%), url('../img/logo-fundo.avif');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }

        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .login-container {
            animation: slideInUp 0.6s ease-out;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            width: 100%;
            max-width: 480px;
        }

        .login-header {
            background: linear-gradient(135deg, var(--azul), var(--azul-escuro));
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-header::before {
            content: '';
            position: absolute;
            top: -50%; right: -50%;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .login-header::after {
            content: '';
            position: absolute;
            bottom: -50%; left: -50%;
            width: 250px; height: 250px;
            background: radial-gradient(circle, rgba(34,197,94,0.2) 0%, transparent 70%);
            border-radius: 50%;
        }

        .login-header > * {
            position: relative;
            z-index: 2;
        }

        .login-header img {
            height: 70px;
            margin-bottom: 1rem;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
        }

        .login-header h2 {
            font-size: 1.9rem;
            font-weight: 700;
            letter-spacing: -1px;
            margin-bottom: 0.4rem;
        }

        .login-header p {
            font-size: 1rem;
            opacity: 0.9;
            font-weight: 500;
            margin: 0;
        }

        .login-body {
            padding: 2rem 2.5rem 2.5rem;
        }

        .alert-erro {
            border-radius: 10px;
            padding: 0.85rem 1rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
            background: #fff0f0;
            color: #c33;
            border-left: 4px solid #c33;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--azul);
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .form-control,
        .form-select {
            border: 2px solid #e0e7ff;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            background: #f9fafb;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--azul);
            background: white;
            box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.12);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--azul), var(--azul-escuro));
            border: none;
            color: white;
            padding: 0.85rem 1rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            margin-top: 0.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(37, 99, 235, 0.4);
            color: white;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .login-footer {
            text-align: center;
            padding-top: 1.25rem;
            border-top: 1px solid #e5e7eb;
        }

        .login-footer a {
            color: var(--azul);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .login-footer a:hover {
            color: var(--azul-escuro);
            transform: translateX(-4px);
        }

        @media (max-width: 480px) {
            .login-card {
                border-radius: 15px;
            }

            .login-header {
                padding: 2rem 1.5rem;
            }

            .login-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-card">

            <div class="login-header">
                <img src="../img/logob.png" alt="Logo Smart Clinic">
                <h2>SMART CLINIC</h2>
                <p>Acesse sua conta</p>
            </div>

            <div class="login-body">

                <?php if ($error !== ''): ?>
                    <div class="alert-erro" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">

                    <div class="form-group">
                        <label for="role" class="form-label">
                            <i class="bi bi-person-badge"></i> Perfil
                        </label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="">Selecione seu perfil</option>
                            <option value="paciente" <?php echo ($selectedRole === 'paciente') ? 'selected' : ''; ?>>👤 Paciente</option>
                            <option value="medico"   <?php echo ($selectedRole === 'medico')   ? 'selected' : ''; ?>>👨‍⚕️ Médico</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope"></i> Email
                        </label>
                        <input type="email" class="form-control" id="email" name="email"
                               placeholder="seu@email.com"
                               value="<?php echo $storedEmail; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock"></i> Senha
                        </label>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="bi bi-box-arrow-in-right"></i> Entrar
                    </button>

                </form>

                <div class="login-footer">
                    <a href="index.php">
                        <i class="bi bi-arrow-left"></i> Voltar ao Site
                    </a>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>