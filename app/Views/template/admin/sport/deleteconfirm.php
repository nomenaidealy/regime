<?= $this->extend('layout') ?>

<?= $this->section('title') ?>NutriPlan — Confirmer suppression<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div style="max-width:900px; margin:40px auto; background:#fff; padding:22px; border-radius:8px;">
  <h2>Confirmer suppression de l'activité</h2>
  <p>Vous êtes sur le point de supprimer l'activité <strong><?= esc($sport['libelle'] ?? '') ?></strong>.</p>

  <?php if (!empty($diets)): ?>
    <div style="background:#FFF7E6; padding:12px; border-radius:8px; margin-bottom:12px;">
      <strong>Attention :</strong> les régimes suivants sont liés à cette activité et seront supprimés également :
      <ul>
        <?php foreach ($diets as $d): ?>
          <li><?= esc($d['nom']) ?> (ID: <?= esc($d['id']) ?>)</li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php else: ?>
    <div style="background:#E6F7FF; padding:12px; border-radius:8px; margin-bottom:12px;">Aucun régime lié.</div>
  <?php endif; ?>

  <form action="<?= base_url('admin/sports/force-delete/' . ($sport['id'] ?? '')) ?>" method="POST">
    <?= csrf_field() ?>
    <a href="<?= base_url('admin/sports') ?>" class="btn-cancel">Annuler</a>
    <button type="submit" class="btn-submit" style="background:#A93226;">Supprimer définitivement</button>
  </form>

  <form action="<?= base_url('admin/sports/remove-keep-diets/' . ($sport['id'] ?? '')) ?>" method="POST" style="margin-top:12px;">
    <?= csrf_field() ?>
    <button type="submit" class="btn-submit" style="background:#2D6A4F;">Supprimer l'activité mais conserver les régimes (ajuster variation)</button>
  </form>
</div>

<?= $this->endSection() ?>
