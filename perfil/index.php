<?php
session_start();

$base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/CookJoynn/';

require_once '../config/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . $base_url . 'usuarios/login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

try {
    $stmt = $pdo->prepare("SELECT nome, email, criado_em FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        $_SESSION['mensagem_erro'] = "Usuário não encontrado.";
        header("Location: ../index.php");
        exit;
    }
} catch (PDOException $e) {
    $erro = "Erro ao buscar dados do usuário: " . $e->getMessage();
}
?>

<main class="container py-4">
    <h1 class="mb-4">Meu Perfil</h1>

    <?php if (!empty($erro)): ?>
        <div class="alert alert-danger"><?= $erro ?></div>
    <?php else: ?>
        <div class="card shadow-sm p-4">
            <h4 class="mb-3">Informações Pessoais</h4>
            <p><strong>Nome:</strong> <?= htmlspecialchars($usuario['nome']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?></p>
            <p><strong>Usuário desde:</strong> <?= date('d/m/Y', strtotime($usuario['criado_em'])) ?></p>

            <a href="editar_perfil.php" class="btn btn-primary mt-3">Editar Perfil</a>
            <a href="<?= $base_url ?>usuarios/logout.php" class="btn btn-outline-danger mt-3">Sair</a>
        </div>
    <?php endif; ?>
</main>

<?php require_once '../includes/footer.php'; ?>
