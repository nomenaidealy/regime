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
      <p>Liste de tous les codes promo créés.</p>
    </div>

    <div style="margin-bottom:18px;">
      <a href="<?= base_url('admin/codepromo/create') ?>" class="btn-nav" style="background:#1a73e8; color:white; padding:10px 18px; border-radius:8px; text-decoration:none; display:inline-block;">
        <i class="bi bi-plus"></i> Nouveau code
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
              <th style="padding:12px; text-align:left; font-weight:600;">Utilisé par</th>
              <th style="padding:12px; text-align:left; font-weight:600;">Date d'utilisation</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($codes as $code): ?>
              <tr style="border-bottom:1px solid #dee2e6;">
                <td style="padding:12px; font-weight:600;"><?= esc($code['code']) ?></td>
                <td style="padding:12px;"><?= number_format($code['montant'], 2) ?> €</td>
                <td style="padding:12px;">
                  <?php if ($code['id_user_utilise']): ?>
                    <span style="background:#d4edda; color:#155724; padding:4px 8px; border-radius:4px; font-size:12px;">
                      Utilisé
                    </span>
                  <?php else: ?>
                    <span style="background:#e7e8ea; color:#6c757d; padding:4px 8px; border-radius:4px; font-size:12px;">
                      Disponible
                    </span>
                  <?php endif; ?>
                </td>
                <td style="padding:12px;">
                  <?= $code['date_utilisation'] ? esc($code['date_utilisation']) : '—' ?>
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
