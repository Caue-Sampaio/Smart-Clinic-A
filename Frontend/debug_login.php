<?php
session_start();

// Debug do login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<h2>Debug do Login</h2>";
    echo "<pre>";
    echo "Role: " . $_POST['role'] . "\n";
    echo "Username: " . $_POST['username'] . "\n";
    echo "Password: " . $_POST['password'] . "\n";
    echo "</pre>";

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

    echo "<h3>Verificação:</h3>";
    echo "Role não vazio: " . (!empty($role) ? 'SIM' : 'NÃO') . "<br>";
    echo "Username não vazio: " . (!empty($username) ? 'SIM' : 'NÃO') . "<br>";
    echo "Password não vazio: " . (!empty($password) ? 'SIM' : 'NÃO') . "<br>";

    if (!empty($role) && !empty($username) && !empty($password)) {
        echo "Usuário existe no array: " . (isset($users[$role][$username]) ? 'SIM' : 'NÃO') . "<br>";
        if (isset($users[$role][$username])) {
            echo "Senha esperada: '" . $users[$role][$username] . "'<br>";
            echo "Senha recebida: '" . $password . "'<br>";
            echo "Senhas iguais: " . ($users[$role][$username] === $password ? 'SIM' : 'NÃO') . "<br>";
        }
    }

    echo "<h3>Credenciais disponíveis:</h3>";
    echo "<strong>PACIENTES:</strong><br>";
    foreach ($users['paciente'] as $user => $pass) {
        echo "- $user / $pass<br>";
    }
    echo "<strong>MÉDICOS:</strong><br>";
    foreach ($users['medico'] as $user => $pass) {
        echo "- $user / $pass<br>";
    }
} else {
    echo "<h2>Formulário de Debug do Login</h2>";
    ?>
    <form method="POST" action="">
        <div style="margin-bottom: 10px;">
            <label>Perfil:</label><br>
            <select name="role" required>
                <option value="">Selecione</option>
                <option value="paciente">Paciente</option>
                <option value="medico">Médico</option>
            </select>
        </div>

        <div style="margin-bottom: 10px;">
            <label>Usuário:</label><br>
            <input type="text" name="username" required>
        </div>

        <div style="margin-bottom: 10px;">
            <label>Senha:</label><br>
            <input type="password" name="password" required>
        </div>

        <button type="submit">Testar Login</button>
    </form>
    <?php
}
?>