<?php
session_start();

$base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/CookJoynn/';

require_once '../config/db.php';
require_once '../includes/header.php';

$receita = null;
$erro = '';

// Verifica se o ID foi passado
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $erro = "Receita inválida.";
} else {
    $id = intval($_GET['id']);

    // Busca a receita no banco de dados
    $sql = "SELECT r.*, u.nome AS nome_autor 
            FROM receitas r
            JOIN usuarios u ON r.usuario_id = u.id
            WHERE r.id = ?";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $receita = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$receita) {
            $erro = "Receita não encontrada.";
        }
    } catch (PDOException $e) {
        $erro = "Erro ao buscar receita: " . $e->getMessage();
    }
}
?>

<main class="container py-4">
    <?php if ($erro): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    <?php elseif ($receita): ?>
        <div class="card">
            <?php if (!empty($receita['imagem'])): ?>
                <img src="../assets/img/uploads/<?= htmlspecialchars($receita['imagem']) ?>" class="card-img-top" alt="Imagem da Receita">
            <?php endif; ?>

            <div class="card-body">
                <h2 class="card-title"><?= htmlspecialchars($receita['titulo']) ?></h2>
                <p><strong>Categoria:</strong> <?= htmlspecialchars(ucfirst($receita['categoria'])) ?></p>
                <p><strong>Tempo de Preparo:</strong> <?= htmlspecialchars($receita['tempo_preparo']) ?> minutos</p>
                <p><strong>Autor:</strong> <?= htmlspecialchars($receita['nome_autor']) ?></p>
                <p><strong>Criado em:</strong> <?= date('d/m/Y H:i', strtotime($receita['criado_em'])) ?></p>

                <hr>

                <h5>Ingredientes</h5>
                <p><?= nl2br(htmlspecialchars($receita['ingredientes'])) ?></p>

                <h5>Modo de Preparo</h5>
                <p><?= nl2br(htmlspecialchars($receita['modo_preparo'])) ?></p>

                <a href="index.php" class="btn btn-primary mt-3">Voltar à Lista</a>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php require_once '../includes/footer.php'; ?>
