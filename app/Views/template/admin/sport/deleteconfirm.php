<?= $this->extend('layout') ?>

<?= $this->section('title') ?>NutriPlan — Confirmer suppression<?= $this->endSection() ?>

<?= $this->section('content') ?>

<link href="<?= base_url('assets/template/sportDelete.css') ?>" rel="stylesheet">

<div class="delete-page">
  <div class="delete-card">
    <div class="delete-header">
      <div class="delete-badge"><i class="bi bi-exclamation-triangle-fill"></i> Suppression d'une activité</div>
      <h1>Confirmer la suppression</h1>
      <p>
        Vous êtes sur le point de supprimer l'activité
        <strong><?= esc($sport['libelle'] ?? '') ?></strong>.
      </p>
    </div>

    <?php if (!empty($diets)): ?>
      <div class="delete-warning">
        <div class="delete-warning-title">
          <i class="bi bi-exclamation-circle-fill"></i>
          Attention
        </div>
        <p>Les régimes suivants sont liés à cette activité et seront supprimés également :</p>
        <ul>
          <?php foreach ($diets as $d): ?>
            <li>
              <span><?= esc($d['nom']) ?></span>
              <small>ID : <?= esc($d['id']) ?></small>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php else: ?>
      <div class="delete-info">
        <i class="bi bi-info-circle-fill"></i>
        <div>
          <strong>Aucun régime lié.</strong>
          <p>La suppression n'affectera aucune fiche régime.</p>
        </div>
      </div>
    <?php endif; ?>

    <div class="delete-actions">
      <a href="<?= base_url('admin/sports') ?>" class="btn-cancel">
        <i class="bi bi-arrow-left"></i>
        Annuler
      </a>

      <form action="<?= base_url('admin/sports/remove-keep-diets/' . ($sport['id'] ?? '')) ?>" method="POST" class="delete-form">
        <?= csrf_field() ?>
        <button type="submit" class="btn-secondary">
          <i class="bi bi-shield-check"></i>
          Supprimer en conservant les régimes
        </button>
      </form>

      <form action="<?= base_url('admin/sports/force-delete/' . ($sport['id'] ?? '')) ?>" method="POST" class="delete-form">
        <?= csrf_field() ?>
        <button type="submit" class="btn-danger">
          <i class="bi bi-trash-fill"></i>
          Supprimer définitivement
        </button>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
