<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART CLINIC - Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --cor-verde: #28A745;
            --cor-azul: #007BFF;
        }

        .bg-azul { background-color: var(--cor-azul) !important; }
        .bg-verde { background-color: var(--cor-verde) !important; }
        .text-azul { color: var(--cor-azul) !important; }
        .text-verde { color: var(--cor-verde) !important; }

        .btn-verde {
            background-color: var(--cor-verde);
            color: #fff;
            border: none;
            transition: 0.3s;
        }
        .btn-verde:hover {
            background-color: #218838;
            color: #fff;
        }

        .btn-azul {
            background-color: var(--cor-azul);
            color: #fff;
            border: none;
            transition: transform 0.3s ease, background-color 0.3s ease, color 0.3s ease;
        }
        .btn-azul:hover {
            background-color: #0056b3;
            color: #fff;
            transform: scale(1.05);
        }

        .btn-ciano {
            background-color: #09529b;
            color: #fff;
            border: none;
            transition: 0.3s;
        }
        .btn-ciano:hover {
            background-color: #4896e4;
            color: #fff;
        }

        body {
            background: linear-gradient(135deg, var(--cor-azul), var(--cor-verde));
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
            background: var(--cor-azul);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .login-body {
            padding: 2rem;
        }

        .form-control:focus {
            border-color: var(--cor-verde);
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
            <?php
            session_start();
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $role = $_POST['role'];
                $username = $_POST['username'];
                $password = $_POST['password'];

                $users = [
                    'paciente' => [
                        'paciente1' => 'paciente123',
                        'paciente2' => 'senha123'
                    ],
                    'medico' => [
                        'medico1' => 'medico123',
                        'medico2' => 'senha456'
                    ]
                ];

                if (!empty($role) && !empty($username) && !empty($password)) {
                    if (isset($users[$role][$username]) && $users[$role][$username] === $password) {
                        $_SESSION['role'] = $role;
                        $_SESSION['username'] = $username;
                        switch ($role) {
                            case 'paciente':
                                header('Location: paciente_dashboard.php');
                                exit;
                            case 'medico':
                                header('Location: medico_dashboard.php');
                                exit;
                        }
                    } else {
                        $error = "Usuário ou senha incorretos.";
                    }
                } else {
                    $error = "Por favor, preencha todos os campos.";
                }
            }
            ?>

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
                        <option value="paciente">Paciente</option>
                        <option value="medico">Médico</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">Usuário</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Digite seu usuário" required>
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