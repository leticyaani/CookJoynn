<?php

session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: usuarios/login.php');
    exit;
}

require_once 'config/db.php';

$base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/CookJoynn/';

$usuario_id = $_SESSION['usuario_id'];
$stmt = $pdo->prepare("SELECT nome FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM receitas WHERE usuario_id = ? ORDER BY criado_em DESC LIMIT 4");
$stmt->execute([$usuario_id]);
$receitas = $stmt->fetchAll();

$stmt = $pdo->query("SELECT r.*, u.nome as autor FROM receitas r JOIN usuarios u ON r.usuario_id = u.id ORDER BY r.criado_em DESC LIMIT 6");
$todas_receitas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php require_once 'includes/header.php'; ?>
    <link href="assets/css/navbar.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>COOKJOYNN - Minha Conta</title>
</head>
<body>
    <?php require_once 'includes/navbar.php'; ?>

    <div class="container py-5">
        <section class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="display-4">Bem-vindo, <?= htmlspecialchars($usuario['nome']) ?>!</h1>
                <a href="receitas/criar.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus"></i> Nova Receita
                </a>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <h3 class="card-title">Minhas Receitas</h3>
                            <p class="display-4"><?= count($receitas) ?></p>
                            <a href="receitas/" class="btn btn-outline-primary">Ver Todas</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <h3 class="card-title">Receitas Salvas</h3>
                            <p class="display-4">0</p>
                            <a href="#" class="btn btn-outline-success">Explorar</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <h3 class="card-title">Seguidores</h3>
                            <p class="display-4">0</p>
                            <a href="#" class="btn btn-outline-info">Comunidade</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mb-5">
            <h2 class="mb-4"><i class="fas fa-book-open"></i> Minhas Receitas Recentes</h2>
            
            <?php if (empty($receitas)): ?>
                <div class="alert alert-info">
                    Você ainda não criou receitas. <a href="receitas/criar.php" class="alert-link">Crie sua primeira receita</a>!
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($receitas as $receita): ?>
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card h-100">
                                <?php if ($receita['imagem']): ?>
                                    <img src="<?= $base_url ?>assets/img/uploads/<?= htmlspecialchars($receita['imagem']) ?>" 
                                         class="card-img-top" 
                                         alt="<?= htmlspecialchars($receita['titulo']) ?>"
                                         style="height: 150px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light text-center p-4" style="height: 150px;">
                                        <i class="fas fa-utensils fa-4x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($receita['titulo']) ?></h5>
                                    <p class="card-text">
                                        <span class="badge bg-<?= 
                                            $receita['categoria'] == 'sobremesa' ? 'success' : 
                                            ($receita['categoria'] == 'lanche' ? 'warning' : 'primary')
                                        ?>">
                                            <?= ucfirst($receita['categoria']) ?>
                                        </span>
                                        <small class="text-muted ms-2">
                                            <i class="fas fa-clock"></i> <?= $receita['tempo_preparo'] ?> min
                                        </small>
                                    </p>
                                </div>
                                
                                <div class="card-footer bg-white d-flex justify-content-between">
                                    <a href="receitas/visualizar.php?id=<?= $receita['id'] ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                       Ver
                                    </a>
                                    <a href="receitas/editar.php?id=<?= $receita['id'] ?>" 
                                       class="btn btn-sm btn-outline-secondary">
                                       Editar
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="mb-5">
            <h2 class="mb-4"><i class="fas fa-users"></i> Receitas da Comunidade</h2>
            
            <div class="row">
                <?php foreach ($todas_receitas as $receita): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100">
                            <?php if ($receita['imagem']): ?>
                                <img src="<?= $base_url ?>assets/img/uploads/<?= htmlspecialchars($receita['imagem']) ?>" 
                                     class="card-img-top" 
                                     alt="<?= htmlspecialchars($receita['titulo']) ?>"
                                     style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light text-center p-5" style="height: 200px;">
                                    <i class="fas fa-utensils fa-4x text-muted"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($receita['titulo']) ?></h5>
                                <p class="card-text text-muted">
                                    <small>
                                        Por <?= htmlspecialchars($receita['autor']) ?> • 
                                        <?= date('d/m/Y', strtotime($receita['criado_em'])) ?>
                                    </small>
                                </p>
                                <p class="card-text">
                                    <span class="badge bg-<?= 
                                        $receita['categoria'] == 'sobremesa' ? 'success' : 
                                        ($receita['categoria'] == 'lanche' ? 'warning' : 'primary')
                                    ?>">
                                        <?= ucfirst($receita['categoria']) ?>
                                    </span>
                                    <span class="text-muted ms-2">
                                        <i class="fas fa-clock"></i> <?= $receita['tempo_preparo'] ?> min
                                    </span>
                                </p>
                            </div>
                            
                            <div class="card-footer bg-white">
                                <a href="receitas/visualizar.php?id=<?= $receita['id'] ?>" 
                                   class="btn btn-sm btn-outline-primary w-100">
                                   Ver Receita
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-3">
                <a href="receitas/" class="btn btn-primary px-4">
                    <i class="fas fa-book-open"></i> Explorar Todas as Receitas
                </a>
            </div>
        </section>
    </div>

    <?php require_once 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>