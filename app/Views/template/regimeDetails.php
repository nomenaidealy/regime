<?= $this->extend('layout') ?>

<?= $this->section('title') ?>NutriPlan — Détails régime<?= $this->endSection() ?>

<?= $this->section('content') ?>

<link href="<?= base_url('assets/template/regimeForm.css') ?>" rel="stylesheet">">

<div class="regime-container">

  <div class="regime-left">
    <div class="logo">Nutri<span>Plan</span></div>
    <div>
      <h2>Détails du régime</h2>
      <p>Informations complètes sur la suggestion sélectionnée.</p>
    </div>
    <div class="regime-left-bottom">
      <a href="<?= base_url('user/mes-regimes') ?>"><i class="bi bi-arrow-left"></i> Retour aux régimes</a>
    </div>
  </div>

  <div class="regime-right">
    <div class="form-header">
      <div class="tag">Suggestion</div>
      <h1><?= esc($regime['diet_nom']) ?></h1>
      <p><?= esc($regime['diet_description'] ?: 'Aucune description disponible.') ?></p>
    </div>

    <div style="display:flex; gap:20px; margin-bottom:18px; flex-wrap:wrap;">
      <div style="flex:1 1 320px; background:#fff; padding:18px; border-radius:12px;">
        <h3>Résumé</h3>
        <p><strong>Activité associée :</strong> <?= esc($regime['sport_libelle'] ?: '—') ?></p>
        <p><strong>Description activité :</strong> <?= esc($regime['sport_description'] ?: '—') ?></p>
        <p><strong>Variation régime :</strong> <?= esc($regime['variation_poids_jour']) ?> kg / jour</p>
        <p><strong>Variation sport :</strong> <?= esc($regime['variation_poids_seance']) ?> kg / séance</p>
      </div>

      <div style="flex:1 1 320px; background:#fff; padding:18px; border-radius:12px;">
        <h3>Composition</h3>
        <p><strong>Viande :</strong> <?= esc($regime['viande_percent']) ?>%</p>
        <p><strong>Volaille :</strong> <?= esc($regime['volaille_percent']) ?>%</p>
        <p><strong>Poisson :</strong> <?= esc($regime['poisson_percent']) ?>%</p>
      </div>
    </div>

    <div style="background:#fff; padding:18px; border-radius:12px;">
      <h3>Durées et prix</h3>
      <?php if (empty($prixs)): ?>
        <div>Aucun tarif disponible.</div>
      <?php else: ?>
        <table style="width:100%; border-collapse:collapse;">
          <thead>
            <tr style="text-align:left; border-bottom:1px solid #e6e6e6;">
              <th style="padding:8px">Durée</th>
              <th style="padding:8px">Prix</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($prixs as $p): ?>
              <tr style="border-bottom:1px solid #f0f0f0;">
                <td style="padding:8px"><?= esc($p['duree']) ?> jours</td>
                <td style="padding:8px"><?= number_format($p['prix'], 2) ?> €</td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

    <div style="background:#fff; padding:18px; border-radius:12px; margin-top:18px;">
      <h3 style="margin-top:0;">Acheter ce régime</h3>
      <?php if (!empty($possible) && $possible): ?>
        <form method="post" action="<?= base_url('user/mes-regimes/souscrire/' . $regime['diet_id']) ?>" style="display:flex; gap:12px; align-items:end; flex-wrap:wrap; margin-top:10px;">
          <?= csrf_field() ?>
          <?php if (!empty($prixs)): ?>
            <input type="hidden" name="prix_id" value="<?= esc($prixs[0]['id']) ?>">
          <?php endif; ?>
          <label style="display:flex; flex-direction:column; gap:6px; min-width:180px;">
            <span style="font-weight:600;">Nombre de jours</span>
            <input type="number" name="nombre_jour" min="1" step="1" value="<?= esc($jours_estimes ?? 30) ?>" style="padding:8px 10px; border:1px solid #ddd; border-radius:8px;">
          </label>
          <button type="submit" style="display:inline-flex; align-items:center; gap:8px; background:#28a745; color:#fff; padding:10px 14px; border-radius:8px; border:0; font-weight:600; font-size:14px; cursor:pointer;">
            <i class="bi bi-cart-plus"></i> Faire ce régime
          </button>
        </form>
      <?php else: ?>
        <div style="margin-top:10px; color:#a94442; font-weight:600;">Ce régime n’est pas recommandé pour votre objectif.</div>
      <?php endif; ?>
    </div>

  </div>

</div>

<?= $this->endSection() ?>
