<?= $this->extend('layout') ?>

<?= $this->section('title') ?>NutriPlan — Liste des activités sportives<?= $this->endSection() ?>

<?= $this->section('content') ?>

<link href="<?= base_url('assets/template/regimeForm.css') ?>" rel="stylesheet">">

<div class="regime-container">

  <div class="regime-left">
    <div class="logo">Nutri<span>Plan</span></div>
    <div>
      <h2>Activités <em>sportives</em></h2>
      <p>Liste et gestion des activités sportives utilisées par les régimes.</p>
    </div>
    <div class="regime-left-bottom">
      <a href="<?= base_url('admin') ?>"><i class="bi bi-arrow-left"></i> Retour</a>
    </div>
  </div>

  <div class="regime-right">
    <div class="form-header">
      <div class="tag">Back Office — Activités</div>
      <h1>Liste des activités</h1>
      <p>Créer, modifier ou supprimer des activités sportives.</p>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
      <div></div>
      <a href="<?= base_url('admin/sports/create') ?>" class="btn-submit" style="display:inline-flex; align-items:center; gap:8px;">
        <i class="bi bi-plus-circle-fill"></i> Nouvelle activité
      </a>
    </div>

    <?php if (empty($sports)): ?>
      <div style="background:#FFF7E6; color:#6A4F1D; padding:18px; border-radius:10px;">Aucune activité trouvée.</div>
    <?php else: ?>
      <table style="width:100%; border-collapse:collapse;">
        <thead>
          <tr style="text-align:left; border-bottom:1px solid #e6e6e6;">
            <th style="padding:12px">Libellé</th>
            <th style="padding:12px">Variation / séance</th>
            <th style="padding:12px">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($sports as $s): ?>
          <tr style="border-bottom:1px solid #f0f0f0;">
            <td style="padding:12px; vertical-align:top"><?= esc($s['libelle'] ?? '') ?></td>
            <td style="padding:12px; vertical-align:top"><?= isset($s['variation_poids_seance']) ? esc($s['variation_poids_seance']) . ' kg' : '—' ?></td>
            <td style="padding:12px; vertical-align:top">
              <a href="<?= base_url('admin/sports/edit/' . ($s['id'] ?? '')) ?>" title="Modifier" style="margin-right:8px; color:#2D6A4F;"><i class="bi bi-pencil-square"></i></a>
              <a href="<?= base_url('admin/sports/delete/' . ($s['id'] ?? '')) ?>" title="Supprimer" onclick="return confirm('Supprimer cette activité ?')" style="color:#A93226;"><i class="bi bi-trash-fill"></i></a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>

  </div>

</div>

<?= $this->endSection() ?>
