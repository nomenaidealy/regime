<?= $this->extend('layout') ?>

<?= $this->section('title') ?>NutriPlan — Mon tableau de bord<?= $this->endSection() ?>

<?= $this->section('content') ?>

<link href="<?= base_url('assets/template/regimeForm.css') ?>" rel="stylesheet">">

<div class="regime-container">

  <div class="regime-left">
    <div class="logo">Nutri<span>Plan</span></div>
    <div>
      <h2>Mon compte</h2>
      <p>Informations personnelles et abonnements.</p>
    </div>
    <div class="regime-left-bottom">
      <a href="<?= base_url('/') ?>"><i class="bi bi-arrow-left"></i> Retour</a>
    </div>
  </div>

  <div class="regime-right">
    <div class="form-header">
      <div class="tag">Mon espace</div>
      <h1>Tableau de bord</h1>
      <p>Résumé rapide de votre profil.</p>
    </div>

    <div style="display:flex; gap:20px; margin-bottom:18px;">
      <div style="flex:1 1 300px; background:#fff; padding:18px; border-radius:12px;">
        <h3>Profil</h3>
        <p><strong>Nom :</strong> <?= esc($user['nom']) ?></p>
        <p><strong>Email :</strong> <?= esc($user['email']) ?></p>
        <p><strong>Genre :</strong> <?= esc($user['genre']) ?></p>
        <p><strong>IMC :</strong> <?= esc($imc) ?></p>
        <p><strong>Objectif :</strong> <?= esc($objectif ? $objectif->libelle : '—') ?></p>
      </div>

      <div style="flex:1 1 300px; background:#fff; padding:18px; border-radius:12px;">
        <h3>Compte</h3>
        <p><strong>Solde :</strong> <?= number_format($solde,2) ?> €</p>
        <p><strong>Statut Gold :</strong> <?= $isGold ? 'Oui' : 'Non' ?></p>
      </div>
    </div>

    <div style="background:#fff; padding:18px; border-radius:12px;">
      <h3>Abonnements</h3>
      <?php if (empty($subscriptions)): ?>
        <div>Aucun abonnement actif.</div>
      <?php else: ?>
        <table style="width:100%; border-collapse:collapse;">
          <thead><tr><th style="padding:8px">Régime</th><th style="padding:8px">Durée</th><th style="padding:8px">Prix payé</th><th style="padding:8px">Début</th></tr></thead>
          <tbody>
            <?php foreach ($subscriptions as $s): ?>
              <tr>
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
