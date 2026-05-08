<?= $this->extend('layout') ?>

<?= $this->section('title') ?>NutriPlan — Ajouter une activité sportive<?= $this->endSection() ?>

<?= $this->section('content') ?>

<link href="<?= base_url('assets/template/regimeForm.css') ?>" rel="stylesheet">">

<div class="regime-container">

  <div class="regime-left">
    <div class="logo">Nutri<span>Plan</span></div>
    <div>
      <h2>Créer une <em>activité sportive</em></h2>
      <p>Ajoutez les activités pratiquées avec leur impact sur la variation de poids par séance.</p>
      <div class="info-cards">
        <div class="info-card">
          <i class="bi bi-bicycle"></i>
          <h4>Impact par séance</h4>
          <p>Précisez la variation de poids estimée par séance (kg).</p>
        </div>
      </div>
    </div>
    <div class="regime-left-bottom">
      <a href="<?= base_url('admin/sports') ?>"><i class="bi bi-arrow-left"></i> Retour à la liste</a>
    </div>
  </div>

  <div class="regime-right">

    <div class="form-header">
      <div class="tag">Back Office — Activités</div>
      <h1><?= isset($mode) && $mode === 'edit' ? 'Modifier l\'activité' : 'Nouvelle activité sportive' ?></h1>
      <p>Remplissez les informations ci-dessous.</p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
      <div style="background:#FCEBEB; color:#791F1F; padding:14px 18px; border-radius:12px; margin-bottom:24px; font-size:14px;">
        <i class="bi bi-exclamation-circle"></i> <?= esc(session()->getFlashdata('error')) ?>
      </div>
    <?php endif; ?>

    <form action="<?= isset($mode) && $mode === 'edit' ? base_url('admin/sports/update/' . ($sport['id'] ?? '')) : base_url('admin/sports/save') ?>" method="POST" id="sportForm">
      <?= csrf_field() ?>

      <div class="form-section">
        <div class="form-section-title"><i class="bi bi-info-circle-fill"></i> Informations</div>
        <div class="field">
          <label>Libellé</label>
          <input type="text" name="libelle" placeholder="Ex: Course à pied" required value="<?= esc($sport['libelle'] ?? '') ?>">
        </div>
        <div class="field">
          <label>Variation poids par séance (kg)</label>
          <input type="number" name="variation_poids_seance" step="0.01" placeholder="Ex: 0.05" value="<?= esc($sport['variation_poids_seance'] ?? '') ?>">
        </div>
        <div class="field">
          <label>Description</label>
          <textarea name="description" placeholder="Optionnel..."><?= esc($sport['description'] ?? '') ?></textarea>
        </div>
      </div>

      <div class="form-actions">
        <a href="<?= base_url('admin/sports') ?>" class="btn-cancel"><i class="bi bi-x-lg"></i> Annuler</a>
        <button type="submit" class="btn-submit"><i class="bi bi-check-circle-fill"></i> Enregistrer</button>
      </div>
    </form>

  </div>

</div>

<?= $this->endSection() ?>
