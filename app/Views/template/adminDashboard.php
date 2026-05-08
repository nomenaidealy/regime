<?= $this->extend('layout') ?>

<?= $this->section('title') ?>NutriPlan — Tableau de bord<?= $this->endSection() ?>

<?= $this->section('content') ?>

<link href="<?= base_url('assets/template/regimeForm.css') ?>" rel="stylesheet">

<div class="regime-container">

  <div class="regime-left">
    <div class="logo">Nutri<span>Plan</span></div>
    <div>
      <h2>Tableau de bord <em>Admin</em></h2>
      <p>Statistiques rapides et indicateurs clés.</p>
    </div>
    <nav style="margin-top:18px;">
      <ul style="list-style:none; padding:0;">
        <li style="margin-bottom:8px;"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
        <li style="margin-bottom:8px;"><a href="<?= base_url('admin/regimes') ?>">Régimes</a></li>
        <li style="margin-bottom:8px;"><a href="<?= base_url('admin/sports') ?>">Activités sportives</a></li>
        <li style="margin-bottom:8px;"><a href="<?= base_url('admin/codepromo/list') ?>">Codes Promo</a></li>
        <li style="margin-bottom:8px;"><a href="<?= base_url('profil') ?>">Utilisateurs</a></li>
      </ul>
    </nav>
    <div class="regime-left-bottom">
      <a href="<?= base_url('/') ?>"><i class="bi bi-arrow-left"></i> Retour au site</a>
      <br>
      <a href="<?= base_url('admin/logout') ?>"><i class="bi bi-box-arrow-right"></i> Déconnexion</a>
    </div>
  </div>

  <div class="regime-right">
    <div class="form-header">
      <div class="tag">Back Office — Dashboard</div>
      <h1>Tableau de bord</h1>
      <p>Visualisez les performances et l'activité de la plateforme.</p>
    </div>

    <div style="display:flex; gap:20px; flex-wrap:wrap; margin-bottom:18px;">
      <div style="flex:1 1 360px; background:#fff; padding:18px; border-radius:12px; box-shadow:0 1px 4px rgba(0,0,0,0.04);">
        <h3>Répartition des régimes</h3>
        <canvas id="chartRegimes" height="200"></canvas>
      </div>

      <div style="flex:1 1 360px; background:#fff; padding:18px; border-radius:12px; box-shadow:0 1px 4px rgba(0,0,0,0.04);">
        <h3>Inscriptions par mois</h3>
        <canvas id="chartInscriptions" height="200"></canvas>
      </div>
    </div>

    <div style="display:flex; gap:20px; flex-wrap:wrap;">
      <div style="flex:1 1 480px; background:#fff; padding:18px; border-radius:12px;">
        <h3>Top régimes</h3>
        <table style="width:100%; border-collapse:collapse;">
          <thead>
            <tr><th style="padding:8px; text-align:left">Nom</th><th style="padding:8px">Inscriptions</th></tr>
          </thead>
          <tbody>
            <?php foreach ($stats['top_regimes'] as $t): ?>
            <tr><td style="padding:8px"><?= esc($t['nom']) ?></td><td style="padding:8px; text-align:center"><?= esc($t['inscriptions']) ?></td></tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div style="flex:1 1 300px; background:#fff; padding:18px; border-radius:12px;">
        <h3>Derniers inscrits</h3>
        <ul style="list-style:none; padding:0; margin:0;">
          <?php foreach ($stats['recent_users'] as $u): ?>
            <li style="padding:8px 0; border-bottom:1px solid #f2f2f2;"><strong><?= esc($u['name']) ?></strong><br><small><?= esc($u['created_at']) ?></small></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

  </div>

</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Data from PHP
  const regimesLabels = <?= json_encode(array_keys($stats['regimes_by_type'])) ?>;
  const regimesValues = <?= json_encode(array_values($stats['regimes_by_type'])) ?>;

  const inscritLabels = <?= json_encode(array_keys($stats['inscriptions_month'])) ?>;
  const inscritValues = <?= json_encode(array_values($stats['inscriptions_month'])) ?>;

  // Pie chart for regimes
  new Chart(document.getElementById('chartRegimes'), {
    type: 'doughnut',
    data: {
      labels: regimesLabels,
      datasets: [{ data: regimesValues, backgroundColor: ['#2D6A4F','#C0392B','#2980B9'] }]
    }
  });

  // Line chart for inscriptions
  new Chart(document.getElementById('chartInscriptions'), {
    type: 'line',
    data: {
      labels: inscritLabels,
      datasets: [{ label: 'Inscriptions', data: inscritValues, borderColor: '#2D6A4F', tension:0.3, fill:true, backgroundColor:'rgba(45,106,79,0.08)'}]
    }
  });
</script>

<?= $this->endSection() ?>
