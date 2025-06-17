<?php
session_start();

$base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/CookJoynn/';

require_once 'config/db.php';
require_once 'includes/header.php';

try {
    $stmt = $pdo->query("SELECT * FROM receitas ORDER BY criado_em DESC LIMIT 6");
    $receitas = $stmt->fetchAll();
} catch (PDOException $e) {
    $receitas = [];
    $erro = "Erro ao carregar receitas: " . $e->getMessage();
}
?>

<main class="container">
    <section class="hero bg-light p-5 rounded-3 mb-5 text-center">
        <h1 class="display-4">Bem-vindo ao COOKJOYNN</h1>
        <p class="lead">Descubra e compartilhe suas receitas favoritas</p>
        
        <?php if (!isset($_SESSION['usuario_id'])): ?>
            <div class="mt-4">
                <a href="usuarios/login.php" class="btn btn-primary btn-lg me-2">Login</a>
                <a href="usuarios/registrar.php" class="btn btn-outline-secondary btn-lg">Registrar</a>
            </div>
        <?php else: ?>
            <div class="mt-4">
                <a href="/CookJoynn/principal.php" class="btn btn-primary btn-lg me-2">Minha Conta</a>
                <a href="receitas/criar.php" class="btn btn-success btn-lg">Nova Receita</a>
            </div>
        <?php endif; ?>
    </section>

    <section class="mb-5">
        <h2 class="mb-4">Receitas em Destaque</h2>
        
        <?php if (isset($erro)): ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php elseif (empty($receitas)): ?>
            <div class="alert alert-info">Nenhuma receita encontrada.</div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($receitas as $receita): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <?php if ($receita['imagem']): ?>
                                <img src="assets/img/uploads/<?= htmlspecialchars($receita['imagem']) ?>" 
                                     class="card-img-top" 
                                     alt="<?= htmlspecialchars($receita['titulo']) ?>"
                                     style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-secondary text-white text-center p-5" style="height: 200px;">
                                    <i class="fas fa-utensils fa-4x"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($receita['titulo']) ?></h5>
                                <p class="card-text">
                                    <span class="badge bg-primary"><?= htmlspecialchars($receita['categoria']) ?></span>
                                    <span class="text-muted ms-2"><?= $receita['tempo_preparo'] ?> min</span>
                                </p>
                            </div>
                            
                            <div class="card-footer bg-white">
                                <a href="receitas/visualizar.php?id=<?= $receita['id'] ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                   Ver Receita
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-3">
                <a href="receitas/" class="btn btn-primary">Ver Todas as Receitas</a>
            </div>
        <?php endif; ?>
    </section>

    <!-- Categorias -->
    <section class="mb-5">
        <h2 class="mb-4">Explore por Categoria</h2>
        <div class="row">
            <div class="col-md-2 col-6 mb-3">
                <a href="receitas/?categoria=almoco" class="btn btn-outline-primary w-100">Almço</a>
            </div>
            <div class="col-md-2 col-6 mb-3">
                <a href="receitas/?categoria=sobremesa" class="btn btn-outline-success w-100">Sobremesa</a>
            </div>
            <div class="col-md-2 col-6 mb-3">
                <a href="receitas/?categoria=lanche" class="btn btn-outline-info w-100">Lanche</a>
            </div>
            <div class="col-md-2 col-6 mb-3">
                <a href="receitas/?categoria=jantar" class="btn btn-outline-warning w-100">Jantar</a>
            </div>
            <div class="col-md-2 col-6 mb-3">
                <a href="receitas/?categoria=cafe-da-manha" class="btn btn-outline-danger w-100">Café</a>
            </div>
            <div class="col-md-2 col-6 mb-3">
                <a href="receitas/" class="btn btn-outline-dark w-100">Todas</a>
            </div>
        </div>
    </section>

    <!-- Benefícios -->
    <section class="bg-white p-5 rounded-3 mb-5 shadow-sm">
        <h2 class="mb-4 text-center">Por que usar o CookJoynn?</h2>
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <i class="fas fa-heart fa-3x text-danger mb-3"></i>
                <h5>Favoritar Receitas</h5>
                <p>Salve suas receitas favoritas e acesse sempre que quiser.</p>
            </div>
            <div class="col-md-4 mb-4">
                <i class="fas fa-share-alt fa-3x text-info mb-3"></i>
                <h5>Compartilhar com Amigos</h5>
                <p>Compartilhe receitas em suas redes sociais com um clique.</p>
            </div>
            <div class="col-md-4 mb-4">
                <i class="fas fa-star fa-3x text-warning mb-3"></i>
                <h5>Avaliar Receitas</h5>
                <p>Ajude outros usuários avaliando e comentando receitas.</p>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="text-center p-5 bg-primary text-white rounded-3 mb-5">
        <h2 class="mb-3">Pronto para compartilhar sua receita?</h2>
        <p class="lead">Inspire outros com suas habilidades culinárias. Publique agora!</p>

        <?php if (isset($_SESSION['usuario_id'])): ?>
            <a href="receitas/criar.php" class="btn btn-light btn-lg mt-3">Criar Receita</a>
        <?php else: ?>
            <p class="mt-3">
                <span class="fw-bold">Você precisa estar logado para criar uma receita.</span><br>
                <a href="<?= $base_url ?>usuarios/login.php" class="btn btn-outline-light btn-sm mt-2 me-2">Fazer Login</a>
                <a href="<?= $base_url ?>usuarios/registrar.php" class="btn btn-outline-light btn-sm mt-2">Registrar</a>
            </p>
        <?php endif; ?>
    </section>

</main>

<?php require_once 'includes/footer.php'; ?>
