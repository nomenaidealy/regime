<?= $this->extend('layout') ?>

<?= $this->section('title') ?>NutriPlan — Codes Promo<?= $this->endSection() ?>

<?= $this->section('content') ?>

<link href="<?= base_url('assets/template/regimeForm.css') ?>" rel="stylesheet">

<div class="regime-container">

  <div class="regime-left">
    <div class="logo">Nutri<span>Plan</span></div>
    <div>
      <h2>Codes Promo</h2>
      <p>Gérer les codes de réduction.</p>
    </div>
    <div class="regime-left-bottom">
      <a href="<?= base_url('admin/dashboard') ?>"><i class="bi bi-arrow-left"></i> Retour</a>
    </div>
  </div>

  <div class="regime-right">
    <div class="form-header">
      <div class="tag">Admin</div>
      <h1>Codes Promo</h1>
      <p>Liste des codes promo et suivi des demandes utilisateurs.</p>
    </div>

    <div style="margin-bottom:18px; display:flex; gap:10px; flex-wrap:wrap;">
      <a href="<?= base_url('admin/codepromo/create') ?>" class="btn-nav" style="background:#1a73e8; color:white; padding:10px 18px; border-radius:8px; text-decoration:none; display:inline-block;">
        <i class="bi bi-plus"></i> Nouveau code
      </a>
      <a href="<?= base_url('admin/codepromo/demandes') ?>" class="btn-nav" style="background:#2D6A4F; color:white; padding:10px 18px; border-radius:8px; text-decoration:none; display:inline-block;">
        <i class="bi bi-list-check"></i> Voir les demandes
      </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
      <div style="padding:12px; background:#d4edda; border:1px solid #28a745; border-radius:8px; color:#155724; margin-bottom:18px;">
        <strong>Succès!</strong> <?= esc(session()->getFlashdata('success')) ?>
      </div>
    <?php endif; ?>

    <?php if (empty($codes)): ?>
      <div style="padding:20px; background:#f8f9fa; border-radius:8px; text-align:center; color:#6c757d;">
        Aucun code promo créé.
      </div>
    <?php else: ?>
      <div style="background:#fff; border-radius:12px; overflow:hidden;">
        <table style="width:100%; border-collapse:collapse;">
          <thead style="background:#f8f9fa; border-bottom:2px solid #dee2e6;">
            <tr>
              <th style="padding:12px; text-align:left; font-weight:600;">Code</th>
              <th style="padding:12px; text-align:left; font-weight:600;">Montant</th>
              <th style="padding:12px; text-align:left; font-weight:600;">Demandes</th>
              <th style="padding:12px; text-align:left; font-weight:600;">Dernière demande</th>
              <th style="padding:12px; text-align:left; font-weight:600;">Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($codes as $code): ?>
              <tr style="border-bottom:1px solid #dee2e6;">
                <td style="padding:12px; font-weight:600;"><?= esc($code['code']) ?></td>
                <td style="padding:12px;"><?= number_format($code['montant'], 2) ?> €</td>
                <td style="padding:12px;"><?= esc($code['total_demandes'] ?? 0) ?></td>
                <td style="padding:12px;">
                  <?= !empty($code['derniere_demande']) ? esc($code['derniere_demande']) : '—' ?>
                </td>
                <td style="padding:12px;">
                  <?php
                    $isPris = ($code['statut_pris'] ?? 'Non pris') === 'Pris';
                    $bg = $isPris ? '#d4edda' : '#f8d7da';
                    $color = $isPris ? '#155724' : '#721c24';
                    $border = $isPris ? '#28a745' : '#f5c6cb';
                  ?>
                  <span style="display:inline-block; padding:4px 10px; border-radius:20px; background:<?= $bg ?>; border:1px solid <?= $border ?>; color:<?= $color ?>; font-size:12px; font-weight:600;">
                    <?= esc($code['statut_pris'] ?? 'Non pris') ?>
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

  </div>

</div>

<?= $this->endSection() ?>
