<?php
session_start(); 

$base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/CookJoynn/';

require_once '../config/db.php';
require_once '../includes/header.php';

$busca = $_GET['busca'] ?? '';

try {
    if ($busca) {
        $stmt = $pdo->prepare("SELECT * FROM receitas WHERE titulo LIKE ? ORDER BY criado_em DESC");
        $stmt->execute(["%$busca%"]);
    } else {
        $stmt = $pdo->query("SELECT * FROM receitas ORDER BY criado_em DESC");
    }
    $receitas = $stmt->fetchAll();
} catch (PDOException $e) {
    $erro = "Erro ao buscar receitas: " . $e->getMessage();
}
?>

<main class="container py-4">
    <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['mensagem_sucesso']; unset($_SESSION['mensagem_sucesso']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['mensagem_erro'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['mensagem_erro']; unset($_SESSION['mensagem_erro']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>

    <h1 class="mb-4">Minhas Receitas</h1>

    <form class="mb-4 d-flex" method="GET">
        <input type="text" name="busca" class="form-control me-2" placeholder="Buscar por título..." value="<?= htmlspecialchars($busca) ?>">
        <button class="btn btn-outline-primary">Buscar</button>
        <?php if (isset($_SESSION['usuario_id'])): ?>
            <a href="criar.php" class="btn btn-success ms-auto">+ Nova Receita</a>
        <?php else: ?>
            <div class="ms-auto d-flex align-items-center text-end">
                <span class="text-muted me-2">Para criar uma receita, faça <a href="<?= $base_url ?>usuarios/login.php">login</a> ou <a href="<?= $base_url ?>usuarios/registrar.php">registre-se</a>.</span>
            </div>
        <?php endif; ?>
    </form>

    <?php if (!empty($erro)): ?>
        <div class="alert alert-danger"><?= $erro ?></div>
    <?php elseif (empty($receitas)): ?>
        <div class="alert alert-warning">Nenhuma receita encontrada.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Categoria</th>
                        <th>Tempo</th>
                        <th>Criado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($receitas as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['titulo']) ?></td>
                            <td><?= htmlspecialchars($r['categoria']) ?></td>
                            <td><?= $r['tempo_preparo'] ?> min</td>
                            <td><?= date('d/m/Y', strtotime($r['criado_em'])) ?></td>
                            <td>
                                <a href="visualizar.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-primary">Ver</a>
                                <a href="editar.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                <a href="excluir.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<?php require_once '../includes/footer.php'; ?>
