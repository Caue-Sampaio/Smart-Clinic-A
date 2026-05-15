<?php
session_start();
$error = null;
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

        body {
            background: linear-gradient(135deg, var(--azul), var(--verde));
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }

        .login-header {
            background: var(--azul);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .login-body {
            padding: 2rem;
        }

        .form-control:focus {
            border-color: var(--verde);
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <img src="../img/logob.png" alt="Logo" class="mb-3" style="height: 60px;">
            <h2 class="mb-0">SMART CLINIC</h2>
            <p class="mb-0">Acesse sua conta</p>
        </div>

        <div class="login-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="role" class="form-label">Perfil</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="">Selecione seu perfil</option>
                        <option value="paciente"<?php echo ($selectedRole === 'paciente') ? ' selected' : ''; ?>>Paciente</option>
                        <option value="medico"<?php echo ($selectedRole === 'medico') ? ' selected' : ''; ?>>Médico</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Digite seu email" value="<?php echo $storedEmail; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Digite sua senha" required>
                </div>

                <button type="submit" class="btn btn-verde w-100 mb-3">Entrar</button>
            </form>

            <div class="text-center">
                <a href="index.php" class="text-decoration-none">
                    <i class="bi bi-arrow-left"></i> Voltar ao Site
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>