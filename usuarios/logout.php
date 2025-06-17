<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_logout'])) {
    require_once '../config/db.php';
    try {
        $stmt = $pdo->prepare("UPDATE usuarios SET ultimo_logout = NOW() WHERE id = ?");
        $stmt->execute([$_SESSION['usuario_id']]);
    } catch (PDOException $e) {
        error_log("Erro ao registrar logout: " . $e->getMessage());
    }

    $_SESSION = array();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    
    session_destroy();
    
    header("Location: login.php?logout=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sair do Sistema - COOKJOYNN</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .logout-container {
            max-width: 500px;
            margin: 100px auto;
            padding: 20px;
        }
        .logout-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <div class="card logout-card">
            <div class="card-body text-center p-5">
                <i class="fas fa-sign-out-alt fa-4x text-primary mb-4"></i>
                <h2 class="h4 mb-3">Deseja realmente sair?</h2>
                <p class="text-muted mb-4">Você será desconectado do sistema.</p>
                
                <form method="POST">
                    <div class="d-flex justify-content-center gap-3">
                        <button type="submit" name="confirmar_logout" class="btn btn-danger px-4">
                            <i class="fas fa-sign-out-alt me-2"></i> Sair
                        </button>
                        <a href="principal.php" class="btn btn-outline-secondary px-4">
                            <i class="fas fa-times me-2"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>