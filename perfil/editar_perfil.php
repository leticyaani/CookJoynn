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
$erros = [];
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (!$nome) $erros[] = "Nome é obrigatório.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = "Email inválido.";

    if (empty($erros)) {
        try {
            $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
            $stmt->execute([$nome, $email, $usuario_id]);
            $sucesso = true;

            // Atualizar sessão
            $_SESSION['usuario_nome'] = $nome;

        } catch (PDOException $e) {
            $erros[] = "Erro ao atualizar perfil: " . $e->getMessage();
        }
    }
} else {
    // Carrega dados atuais
    $stmt = $pdo->prepare("SELECT nome, email FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch();

    $nome = $usuario['nome'] ?? '';
    $email = $usuario['email'] ?? '';
}
?>

<main class="container py-4">
    <h1 class="mb-4">Editar Perfil</h1>

    <?php if ($sucesso): ?>
        <div class="alert alert-success">Perfil atualizado com sucesso!</div>
        <a href="perfil.php" class="btn btn-primary">Voltar ao Perfil</a>
    <?php else: ?>
        <?php if (!empty($erros)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($erros as $erro): ?>
                        <li><?= htmlspecialchars($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($nome) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>">
            </div>

            <button type="submit" class="btn btn-success">Salvar Alterações</button>
            <a href="perfil.php" class="btn btn-secondary">Cancelar</a>
        </form>
    <?php endif; ?>
</main>

<?php require_once '../includes/footer.php'; ?>
