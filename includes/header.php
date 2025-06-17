<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COOKJOYNN - Sistema de Receitas</title>
    
    <link href="<?php echo $base_url; ?>assets/css/reset.css" rel="stylesheet">
    <link href="<?php echo $base_url; ?>assets/css/bootstrap-4.1.3-dist/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $base_url; ?>assets/css/style.css" rel="stylesheet">
    <link href="<?php echo $base_url; ?>assets/css/navbar.css" rel="stylesheet">
    <link href="<?php echo $base_url; ?>assets/css/receitas.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="/CookJoynn/index.php"><img src="<?php echo $base_url; ?>assets/img/logo2.png" alt="COOKJOYNN" width="200"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/CookJoynn/receitas/">Receitas</a>
                    </li>
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/CookJoynn/principal.php">Minha Conta</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/CookJoynn/usuarios/logout.php">Sair</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/CookJoynn/usuarios/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/CookJoynn/usuarios/registrar.php">Registrar</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container mt-5">