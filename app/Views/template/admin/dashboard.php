<?= $this->extend('layout') ?>

<?= $this->section('title') ?>NutriPlan — Tableau de bord<?= $this->endSection() ?>

<?= $this->section('content') ?>

<link href="<?= base_url('assets/template/dashboard.css') ?>" rel="stylesheet">

<div class="regime-container">

  <!-- SIDEBAR (LEFT) -->
  <aside class="regime-left">
    <div>
      <div class="logo">Nutri<span>Plan</span></div>
      <div class="dashboard-tagline">
        <h2>Tableau de bord <em>Admin</em></h2>
        <p>Statistiques rapides et indicateurs clés.</p>
      </div>
      
    </div>
    <div class="regime-left-bottom">
      <a href="<?= base_url('/') ?>"><i class="bi bi-arrow-left"></i> Retour au site</a>
      <a href="<?= base_url('admin/logout') ?>"><i class="bi bi-box-arrow-right"></i> Déconnexion</a>
    </div>
  </aside>

  <!-- MAIN CONTENT (RIGHT) -->
  <main class="regime-right">
    <div class="form-header">
      <div class="tag">Back Office — Dashboard</div>
      <h1>Tableau de bord</h1>
      <p>Visualisez les performances et l'activité de la plateforme.</p>
    </div>

    <!-- Charts row -->
    <div class="dashboard-row">
      <div class="card chart-card">
        <h3 class="card-title">Répartition des régimes</h3>
        <div class="chart-wrapper">
          <canvas id="chartRegimes" height="200"></canvas>
        </div>
      </div>

      <div class="card chart-card">
        <h3 class="card-title">Inscriptions par mois</h3>
        <div class="chart-wrapper">
          <canvas id="chartInscriptions" height="200"></canvas>
        </div>
      </div>
    </div>

    <!-- Tables & lists row -->
    <div class="dashboard-row">
      <div class="card">
        <h3 class="card-title">Top régimes</h3>
        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr><th>Nom</th><th>Inscriptions</th></tr>
            </thead>
            <tbody>
              <?php foreach ($stats['top_regimes'] as $t): ?>
              <tr>
                <td><?= esc($t['nom']) ?></td>
                <td class="text-center"><?= esc($t['inscriptions']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="card">
        <h3 class="card-title">Derniers inscrits</h3>
        <ul class="recent-list">
          <?php foreach ($stats['recent_users'] as $u): ?>
            <li>
              <strong><?= esc($u['name']) ?></strong>
              <small><?= esc($u['created_at']) ?></small>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </main>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Data from PHP (same as original)
  const regimesLabels = <?= json_encode(array_keys($stats['regimes_by_type'])) ?>;
  const regimesValues = <?= json_encode(array_values($stats['regimes_by_type'])) ?>;
  const inscritLabels = <?= json_encode(array_keys($stats['inscriptions_month'])) ?>;
  const inscritValues = <?= json_encode(array_values($stats['inscriptions_month'])) ?>;

  // Doughnut chart (répartition des régimes)
  new Chart(document.getElementById('chartRegimes'), {
    type: 'doughnut',
    data: {
      labels: regimesLabels,
      datasets: [{
        data: regimesValues,
        backgroundColor: ['#2D6A4F', '#C0392B', '#2980B9', '#F39C12', '#8E44AD'],
        borderWidth: 0,
        hoverOffset: 8
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: { position: 'bottom', labels: { font: { size: 12 } } }
      }
    }
  });

  // Line chart (inscriptions par mois)
  new Chart(document.getElementById('chartInscriptions'), {
    type: 'line',
    data: {
      labels: inscritLabels,
      datasets: [{
        label: 'Inscriptions',
        data: inscritValues,
        borderColor: '#2D6A4F',
        tension: 0.3,
        fill: true,
        backgroundColor: 'rgba(45, 106, 79, 0.08)',
        pointBackgroundColor: '#2D6A4F',
        pointBorderColor: '#fff',
        pointRadius: 4,
        pointHoverRadius: 6
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        tooltip: { mode: 'index', intersect: false }
      },
      scales: {
        y: { beginAtZero: true, grid: { color: '#e9ecef' } },
        x: { grid: { display: false } }
      }
    }
  });
</script>

<?= $this->endSection() ?>