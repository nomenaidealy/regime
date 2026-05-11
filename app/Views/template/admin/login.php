<?= $this->extend('layout') ?>

<?= $this->section('title') ?>NutriPlan — Connexion admin<?= $this->endSection() ?>

<?= $this->section('content') ?>

<link href="<?= base_url('assets/template/inscription.css') ?>" rel="stylesheet">

<div class="left-panel">
  <div class="logo">Nutri<span>Plan</span></div>

  <div class="left-content">
    <h2>Accès administrateur</h2>
    <p>Connectez-vous pour gérer les régimes, les activités sportives et les codes promo.</p>

    <div class="steps-left" id="stepsLeft">
      <div class="step-ind active" data-step="1">
        <div class="step-dot">1</div>
        <div class="step-ind-text">
          <h4>Connexion admin</h4>
          <p>Login & mot de passe</p>
        </div>
      </div>
    </div>
  </div>

  <div class="left-bottom">
    <a href="<?= base_url('/') ?>">Retour au site</a>
  </div>
</div>

<div class="right-panel">
  <form action="<?= base_url('admin/tolog') ?>" method="POST" id="adminLoginForm">
    <?= csrf_field() ?>

    <div class="progress-dots" id="progressDots">
      <div class="dot active"></div>
    </div>

    <div class="form-step active" id="step1">
      <div class="step-header">
        <div class="step-tag">Admin</div>
        <h2>Se connecter</h2>
        <p>Entrez vos identifiants administrateur.</p>
      </div>

      <div class="field">
        <label>Login</label>
        <input type="text" name="login" placeholder="Votre login admin" required value="superadmin">
      </div>

      <div class="field">
        <label>Mot de passe</label>
        <div class="pwd-wrapper">
          <input type="password" name="mdp" placeholder="Votre mot de passe" required value="admin2026">
        </div>
      </div>

      <?php if (session()->getFlashdata('login_error')): ?>
        <div class="err-msg" style="margin-bottom:10px;color:#A32D2D;">
          <?= esc(session()->getFlashdata('login_error')) ?>
        </div>
      <?php endif; ?>

      <button class="btn-next" type="submit"><i class="bi bi-shield-lock"></i> Se connecter</button>
    </div>
  </form>
</div>

<?= $this->endSection() ?>