<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="<?= $base_url ?>">
            <img src="<?= $base_url ?>assets/img/logo2.png" alt="COOKJOYNN" width="200">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto">
                <?php
                $menu = [
                    ['href' => 'receitas/', 'icon' => 'book', 'label' => 'Receitas'],
                    ['href' => 'receitas/criar.php', 'icon' => 'plus-circle', 'label' => 'Criar'],
                    ['href' => 'categorias/', 'icon' => 'tags', 'label' => 'Categorias'],
                ];
                foreach ($menu as $item): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url . $item['href'] ?>">
                            <i class="fas fa-<?= $item['icon'] ?> me-1"></i> <?= $item['label'] ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            
            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i>
                            <?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário') ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="<?= $base_url ?>perfil/">
                                    <i class="fas fa-user me-2"></i> Meu Perfil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= $base_url ?>principal.php">
                                    <i class="fas fa-tachometer-alt me-2"></i> Painel
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="<?= $base_url ?>usuarios/logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i> Sair
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link position-relative" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                3
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-2" aria-labelledby="notificationsDropdown" style="width: 300px;">
                            <li><h6 class="dropdown-header">Notificações</h6></li>
                            <li><a class="dropdown-item" href="#">Nova mensagem</a></li>
                            <li><a class="dropdown-item" href="#">Receita favoritada</a></li>
                            <li><a class="dropdown-item" href="#">Novo seguidor</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-center" href="#">Ver todas</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url ?>usuarios/login.php">
                            <i class="fas fa-sign-in-alt me-1"></i> Entrar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url ?>usuarios/registrar.php">
                            <i class="fas fa-user-plus me-1"></i> Registrar
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <form class="d-flex ms-2" action="<?= $base_url ?>receitas/" method="GET">
                <div class="input-group">
                    <input class="form-control form-control-sm" type="search" name="busca" placeholder="Buscar receitas...">
                    <button class="btn btn-outline-light btn-sm" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</nav>

<div style="height: 56px;"></div>