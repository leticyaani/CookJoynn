<?php

session_start();

$host = 'localhost';
$dbname = 'sistema_receitas';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

if (isset($_SESSION['usuario_id'])) {
    header('Location: principal.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];

    try {
        $stmt = $pdo->prepare("SELECT id, nome, senha, tentativas_login, ultimo_login FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && $usuario['tentativas_login'] >= 5) {
            $bloqueado = strtotime($usuario['ultimo_login']) > strtotime('-30 minutes');
            if ($bloqueado) {
                $erro = "Conta temporariamente bloqueada. Tente novamente em 30 minutos.";
            }
        }

        if (!$erro && $usuario && password_verify($senha, $usuario['senha'])) {
            session_regenerate_id(true);
            
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['loggedin'] = true;
            $_SESSION['LAST_ACTIVITY'] = time();
            
            $pdo->prepare("UPDATE usuarios SET tentativas_login = 0, ultimo_login = NOW() WHERE id = ?")
                ->execute([$usuario['id']]);
            
            header('Location: /CookJoynn/principal.php');
            exit;
            
        } else {
            if ($usuario) {
                $pdo->prepare("UPDATE usuarios SET tentativas_login = tentativas_login + 1, ultimo_login = NOW() WHERE id = ?")
                    ->execute([$usuario['id']]);
            }
            
            $erro = "Credenciais inválidas!";
            sleep(2);
        }
        
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        $erro = "Erro no sistema. Por favor, tente mais tarde.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COOKJOYNN - Login</title>
    <link href="../assets/css/login.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card login-card">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <img src="../assets/img/logo.png" alt="COOKJOYNN" class="login-logo">
                            <h2 class="h4">Acesse sua conta</h2>
                            <p class="text-muted">Gerencie suas receitas favoritas</p>
                        </div>

                        <?php if (isset($erro)): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <?= $erro ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           placeholder="seu@email.com" required autofocus>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="senha" class="form-label">Senha</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control" id="senha" name="senha" 
                                           placeholder="••••••••" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">
                                    <a href="recuperar-senha.php">Esqueceu sua senha?</a>
                                </div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i> Entrar
                                </button>
                            </div>

                            <div class="text-center">
                                <p class="mb-0">Não tem uma conta? 
                                    <a href="registrar.php" class="text-primary">Cadastre-se</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentNode.querySelector('input');
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });

    (() => {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
        
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                
                form.classList.add('was-validated')
            }, false)
        })
    })()
    </script>
</body>
</html>