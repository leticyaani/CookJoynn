<?php
session_start();

$base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/CookJoynn/';

require_once '../config/db.php';
require_once '../includes/header.php';

$categorias = ['Almoço', 'Sobremesa', 'Lanche', 'Jantar', 'Café', 'Todas'];
$categoria_selecionada = $_GET['categoria'] ?? 'Todas';

try {
    if ($categoria_selecionada === 'Todas' || !in_array($categoria_selecionada, $categorias)) {
        // Buscar todas receitas
        $stmt = $pdo->query("SELECT * FROM receitas ORDER BY criado_em DESC");
        $receitas = $stmt->fetchAll();
        $categoria_selecionada = 'Todas'; // forçar pra "Todas" caso categoria inválida
    } else {
        // Buscar receitas por categoria (convertendo para minúsculo para combinar com banco)
        $cat_lower = strtolower($categoria_selecionada);
        $stmt = $pdo->prepare("SELECT * FROM receitas WHERE categoria = ? ORDER BY criado_em DESC");
        $stmt->execute([$cat_lower]);
        $receitas = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    $erro = "Erro ao buscar receitas: " . $e->getMessage();
}
?>

<main class="container py-4">
    <h1 class="mb-4">Explore por Categoria</h1>

    <div class="mb-4">
        <?php foreach ($categorias as $cat): ?>
            <a href="?categoria=<?= urlencode($cat) ?>" class="btn btn-outline-primary m-1 <?= ($cat === $categoria_selecionada) ? 'active' : '' ?>">
                <?= $cat ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($erro)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <?php if (empty($receitas)): ?>
        <div class="alert alert-warning">Nenhuma receita encontrada para a categoria "<?= htmlspecialchars($categoria_selecionada) ?>".</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($receitas as $r): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <?php if ($r['imagem']): ?>
                            <img src="../assets/img/uploads/<?= htmlspecialchars($r['imagem']) ?>" class="card-img-top" alt="Imagem da Receita">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($r['titulo']) ?></h5>
                            <p class="card-text">Tempo: <?= $r['tempo_preparo'] ?> min</p>
                            <a href="visualizar.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-primary">Ver Receita</a>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif; ?>
</main>

<?php require_once '../includes/footer.php'; ?>
