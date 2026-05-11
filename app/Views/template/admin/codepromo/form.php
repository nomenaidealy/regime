<?= $this->extend('layout') ?>

<?= $this->section('title') ?>NutriPlan — Créer un Code Promo<?= $this->endSection() ?>

<?= $this->section('content') ?>

<link href="<?= base_url('assets/template/regimeForm.css') ?>" rel="stylesheet">

<div class="regime-container">

  <div class="regime-left">
    <div class="logo">Nutri<span>Plan</span></div>
    <div>
      <h2>Code Promo</h2>
      <p>Créer un nouveau code de réduction.</p>
    </div>
    <div class="regime-left-bottom">
      <a href="<?= base_url('admin/codepromo/list') ?>"><i class="bi bi-arrow-left"></i> Voir les codes</a>
    </div>
  </div>

  <div class="regime-right">
    <div class="form-header">
      <div class="tag">Admin</div>
      <h1>Nouveau Code Promo</h1>
      <p>Créez un code promo pour vos utilisateurs.</p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
      <div style="padding:12px; background:#f8d7da; border:1px solid #f5c6cb; border-radius:8px; color:#721c24; margin-bottom:18px;">
        <strong>Erreur</strong> <?= esc(session()->getFlashdata('error')) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="<?= base_url('admin/codepromo/create') ?>" style="max-width: 500px;">
      <?= csrf_field() ?>

      <div style="margin-bottom:18px;">
        <label style="display:block; margin-bottom:6px; font-weight:600;">Code</label>
        <input 
          type="text" 
          name="code" 
          value="<?= old('code') ?>"
          placeholder="ex: SUMMER2026" 
          style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; font-size:14px; text-transform:uppercase;"
          required
        >
        <small style="color:#999; display:block; margin-top:4px;">Code unique (3-50 caractères)</small>
      </div>

      <div style="margin-bottom:18px;">
        <label style="display:block; margin-bottom:6px; font-weight:600;">Montant (€)</label>
        <input 
          type="number" 
          name="montant" 
          value="<?= old('montant') ?>"
          placeholder="ex: 50.00" 
          step="0.01"
          min="0.01"
          style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; font-size:14px;"
          required
        >
        <small style="color:#999; display:block; margin-top:4px;">Montant à créditer à l'utilisateur</small>
      </div>

      <button 
        type="submit" 
        style="width:100%; padding:12px; background:#28a745; color:white; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; transition:0.2s;"
        onmouseover="this.style.background='#218838'"
        onmouseout="this.style.background='#28a745'"
      >
        Créer le code promo
      </button>

    </form>

  </div>

</div>

<?= $this->endSection() ?>
