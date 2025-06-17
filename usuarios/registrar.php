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

$erros = [];
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    
    if (empty($nome)) {
        $erros['nome'] = 'Por favor, informe seu nome';
    } elseif (strlen($nome) < 3) {
        $erros['nome'] = 'O nome deve ter pelo menos 3 caracteres';
    }
    
    if (empty($email)) {
        $erros['email'] = 'Por favor, informe seu e-mail';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros['email'] = 'E-mail inválido';
    } else {
        // Verifica se e-mail já existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $erros['email'] = 'Este e-mail já está em uso';
        }
    }
    
    if (empty($senha)) {
        $erros['senha'] = 'Por favor, informe uma senha';
    } elseif (strlen($senha) < 6) {
        $erros['senha'] = 'A senha deve ter pelo menos 6 caracteres';
    }
    
    if ($senha !== $confirmar_senha) {
        $erros['confirmar_senha'] = 'As senhas não coincidem';
    }
    
    if (empty($erros)) {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
            $stmt->execute([$nome, $email, $senha_hash]);
            
            $sucesso = true;
            
            // Redireciona após 3 segundos
            header("Refresh: 3; url=login.php");
            
        } catch (PDOException $e) {
            $erros['geral'] = 'Erro ao cadastrar usuário: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COOKJOYNN - Cadastro</title>
    <link href="../assets/css/registrar.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        
    </style>
</head>
<body>
    <div class="register-container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card register-card">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <img src="../assets/img/logo.png" alt="COOKJOYNN" class="register-logo">
                            <h2 class="h4">Crie sua conta</h2>
                            <p class="text-muted">Comece a gerenciar suas receitas favoritas</p>
                        </div>

                        <?php if ($sucesso): ?>
                            <div class="alert alert-success text-center">
                                <i class="fas fa-check-circle me-2"></i>
                                Cadastro realizado com sucesso! Redirecionando para login...
                            </div>
                        <?php elseif (isset($erros['geral'])): ?>
                            <div class="alert alert-danger">
                                <?= $erros['geral'] ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome Completo</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" class="form-control <?= isset($erros['nome']) ? 'is-invalid' : '' ?>" 
                                           id="nome" name="nome" placeholder="Seu nome completo" 
                                           value="<?= htmlspecialchars($nome ?? '') ?>" required>
                                </div>
                                <?php if (isset($erros['nome'])): ?>
                                    <div class="invalid-feedback"><?= $erros['nome'] ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control <?= isset($erros['email']) ? 'is-invalid' : '' ?>" 
                                           id="email" name="email" placeholder="seu@email.com" 
                                           value="<?= htmlspecialchars($email ?? '') ?>" required>
                                </div>
                                <?php if (isset($erros['email'])): ?>
                                    <div class="invalid-feedback"><?= $erros['email'] ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="senha" class="form-label">Senha</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control <?= isset($erros['senha']) ? 'is-invalid' : '' ?>" 
                                           id="senha" name="senha" placeholder="••••••••" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <?php if (isset($erros['senha'])): ?>
                                    <div class="invalid-feedback"><?= $erros['senha'] ?></div>
                                <?php endif; ?>
                                <div class="form-text">Mínimo de 6 caracteres</div>
                            </div>

                            <div class="mb-4">
                                <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control <?= isset($erros['confirmar_senha']) ? 'is-invalid' : '' ?>" 
                                           id="confirmar_senha" name="confirmar_senha" placeholder="••••••••" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <?php if (isset($erros['confirmar_senha'])): ?>
                                    <div class="invalid-feedback"><?= $erros['confirmar_senha'] ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-plus me-2"></i> Cadastrar
                                </button>
                            </div>

                            <div class="text-center">
                                <p class="mb-0">Já tem uma conta? 
                                    <a href="login.php" class="text-primary">Faça login</a>
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