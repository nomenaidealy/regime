<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?></title>
    <link href="<?= base_url('assets/template/accueil.css') ?>" rel="stylesheet">
    <!-- Remplacez le CDN par le fichier local -->
<link href="<?= base_url('assets/bootstrap/bootstrap-icons/bootstrap-icons.min.css') ?>" rel="stylesheet">
</head>
<body>

<nav>
    <div class="nav-logo">Nutri<span>Plan</span></div>
    <div class="nav-links">
        <?php if (session()->get('is_admin')): ?>
            <a href="<?= base_url('admin/regimes') ?>">Régimes</a>
            <a href="<?= base_url('admin/sports') ?>">Activités Sportives</a>
            <a href="<?= base_url('admin/codepromo/list') ?>">Codes Promo</a>
            <a href="<?= base_url('admin/dashboard') ?>">Dashboard admin</a>
            <a href="<?= base_url('admin/logout') ?>" class="btn-nav">Déconnexion admin</a>
        <?php elseif (session()->get('user_id')): ?>
            <a href="<?= base_url('/') ?>#about">À propos</a>
            <a href="<?= base_url('/') ?>#mission">Notre mission</a>
            <a href="<?= base_url('/') ?>#fonctionnalites">Fonctionnalités</a>
            <a href="<?= base_url('/') ?>#gold">Option Gold</a>
            <a href="<?= base_url('dashboard') ?>">Mon tableau de bord</a>
            <a href="<?= base_url('profil') ?>">Profil</a>
            <a href="<?= base_url('logout') ?>" class="btn-nav">Déconnexion</a>
        <?php else: ?>
            <a href="<?= base_url('/') ?>#about">À propos</a>
            <a href="<?= base_url('/') ?>#mission">Notre mission</a>
            <a href="<?= base_url('/') ?>#fonctionnalites">Fonctionnalités</a>
            <a href="<?= base_url('/') ?>#gold">Option Gold</a>
            <a href="<?= base_url('login') ?>">Se connecter</a>
            <a href="<?= base_url('admin') ?>" class="btn-nav">Connexion admin</a>
            <a href="<?= base_url('inscription') ?>" class="btn-nav">Commencer</a>
        <?php endif; ?>
    </div>
</nav>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<?= $this->renderSection('content') ?>

<footer>
    <div class="footer-logo">Nutri<span>Plan</span></div>
    <p>© 2026 NutriPlan — Application de régime alimentaire personnalisé</p>
</footer>

</body>
</html>