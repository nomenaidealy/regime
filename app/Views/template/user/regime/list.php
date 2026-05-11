<?= $this->extend('layout') ?>

<?= $this->section('title') ?>NutriPlan — Mes régimes<?= $this->endSection() ?>

<?= $this->section('content') ?>

<link href="<?= base_url('assets/template/regimeForm.css') ?>" rel="stylesheet">

<div class="regime-container">

  <div class="regime-left">
    <div class="logo">Nutri<span>Plan</span></div>
    <div>
      <h2>Mes régimes</h2>
      <p>Retrouvez vos suggestions de régimes et vos abonnements actifs.</p>
    </div>
    <div class="regime-left-bottom">
      <a href="<?= base_url('profil') ?>"><i class="bi bi-person-badge"></i> Voir mon profil</a>
    </div>
  </div>

  <div class="regime-right">
    <div class="form-header">
      <div class="tag">Mon espace</div>
      <h1>Mes régimes</h1>
      <p>Consultez les suggestions adaptées à votre profil et la liste de vos abonnements.</p>
      <div style="margin-top:10px;">
        <a href="<?= base_url('user/mes-regimes/export') ?>" style="display:inline-flex; align-items:center; gap:8px; background:#0d6efd; color:#fff; padding:8px 12px; border-radius:8px; text-decoration:none; font-weight:600; font-size:13px;">
          <i class="bi bi-file-earmark-pdf"></i> Exporter en PDF
        </a>
      </div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
      <div style="background:#fdecea; color:#a94442; border:1px solid #f5c6cb; padding:12px 14px; border-radius:10px; margin-bottom:14px; font-weight:600; max-width:100%; box-sizing:border-box; white-space:normal; overflow-wrap:anywhere; word-break:break-word; line-height:1.45;">
        <?= esc(session()->getFlashdata('error')) ?>
      </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
      <div style="background:#e9f7ef; color:#145a32; border:1px solid #c3e6cb; padding:12px 14px; border-radius:10px; margin-bottom:14px; font-weight:600; max-width:100%; box-sizing:border-box; white-space:normal; overflow-wrap:anywhere; word-break:break-word; line-height:1.45;">
        <?= esc(session()->getFlashdata('success')) ?>
      </div>
    <?php endif; ?>

    <div style="display:flex; gap:20px; margin-bottom:18px; flex-wrap:wrap;">
      <div style="flex:1 1 300px; background:#fff; padding:18px; border-radius:12px;">
        <h3>Profil rapide</h3>
        <p><strong>Nom :</strong> <?= esc($user['nom']) ?></p>
        <p><strong>IMC :</strong> <?= esc($imc) ?></p>
        <p><strong>Objectif :</strong> <?= esc($objectif ? $objectif->libelle : '—') ?></p>
        <p><strong>Statut Gold :</strong> <?= $isGold ? 'Compte Gold' : 'Compte Standard' ?></p>
      </div>

      <div style="flex:1 1 300px; background:#fff; padding:18px; border-radius:12px;">
        <h3>Solde</h3>
        <p style="font-size:22px; font-weight:700; margin:0; color:#1a73e8;"><?= number_format($solde,2) ?> €</p>
        <p style="margin-top:10px; color:#666;">Votre portefeuille actuel.</p>
        <a href="<?= base_url('codepromo/form') ?>" style="display:inline-block; margin-top:10px; background:#1a73e8; color:white; padding:8px 14px; border-radius:6px; text-decoration:none; font-size:13px; font-weight:600;">
          <i class="bi bi-plus-circle"></i> Créditer avec code promo
        </a>
      </div>
    </div>

    <div style="background:#fff; padding:18px; border-radius:12px; margin-bottom:18px;">
      <h3 style="margin-top:0;">Suggestions de régimes</h3>
      <p style="margin-top:-6px; color:#666;">Tous les régimes disponibles avec un accès aux détails.</p>

      <?php if (empty($suggestions)): ?>
        <div>Aucune suggestion de régime disponible.</div>
      <?php else: ?>
        <div style="overflow:auto;">
          <table style="width:100%; border-collapse:collapse;">
            <thead>
              <tr style="text-align:left; border-bottom:1px solid #e6e6e6;">
                <th style="padding:8px">Régime</th>
                <th style="padding:8px">Sport</th>
                <th style="padding:8px">Description</th>
                <th style="padding:8px">Prix</th>
                <th style="padding:8px">Variation</th>
                <th style="padding:8px">Composition</th>
                <th style="padding:8px">Jours estimés</th>
                <th style="padding:8px">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($suggestions as $s): ?>
                <tr style="border-bottom:1px solid #f0f0f0;">
                  <td style="padding:8px"><?= esc($s['diet_nom']) ?></td>
                  <td style="padding:8px"><?= esc($s['sport_libelle'] ?: '—') ?></td>
                  <td style="padding:8px"><?= esc(mb_strimwidth($s['diet_description'] ?? '', 0, 120, '...')) ?></td>
                  <td style="padding:8px"><?= isset($s['prix_30']) ? number_format($s['prix_30'],2) . ' €' : '—' ?></td>
                  <td style="padding:8px"><?= esc($s['variation_poids_jour']) ?> kg/j</td>
                  <td style="padding:8px">V <?= esc($s['viande_percent']) ?>% • O <?= esc($s['volaille_percent']) ?>% • P <?= esc($s['poisson_percent']) ?>%</td>
                  <td style="padding:8px"><?php if ($s['jours_estimes'] === 0): ?>Objectif atteint<?php elseif ($s['possible']): ?><?= esc($s['jours_estimes']) ?> jours<?php else: ?>Non recommandé<?php endif; ?></td>
                  <td style="padding:8px">
                    <a href="<?= base_url('user/mes-regimes/' . $s['diet_id']) ?>" style="display:inline-flex; align-items:center; gap:8px; background:#1a73e8; color:#fff; padding:8px 12px; border-radius:8px; text-decoration:none; font-weight:600; font-size:13px;">
                      <i class="bi bi-eye"></i> Détails
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

    <div style="background:#fff; padding:18px; border-radius:12px;">
      <h3 style="margin-top:0;">Mes abonnements</h3>
      <?php if (empty($subscriptions)): ?>
        <div>Aucun abonnement actif.</div>
      <?php else: ?>
        <table style="width:100%; border-collapse:collapse;">
          <thead>
            <tr style="text-align:left; border-bottom:1px solid #e6e6e6;">
              <th style="padding:8px">Régime</th>
              <th style="padding:8px">Durée</th>
              <th style="padding:8px">Prix payé</th>
              <th style="padding:8px">Début</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($subscriptions as $s): ?>
              <tr style="border-bottom:1px solid #f0f0f0;">
                <td style="padding:8px"><?= esc($s['diet_nom']) ?></td>
                <td style="padding:8px"><?= esc($s['duree']) ?> jours</td>
                <td style="padding:8px"><?= number_format($s['prix_paye'],2) ?> €</td>
                <td style="padding:8px"><?= esc($s['date_debut']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

  </div>

</div>

<?= $this->endSection() ?>
