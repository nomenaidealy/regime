<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SysInfo - Notes etudiant</title>
    <link rel="stylesheet" href="<?= base_url('assets/template/style.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/template/notes.css') ?>" />
</head>
<body>
<div class="app">
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="logo-icon">
        <svg viewBox="0 0 24 24" width="18" height="18"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
      </div>
      <div>
        <div class="brand-name">SysInfo</div>
        <div class="brand-sub">v2.4.0</div>
      </div>
    </div>

    <div class="sidebar-section">Navigation</div>
    
    <a href="<?= site_url('list') ?>" class="nav-item active">Etudiants</a>
    <a href="<?= site_url('note/form') ?>" class="nav-item">Formulaire</a>

    <div class="sidebar-bottom">
      <a href="<?= site_url('login') ?>" class="user-row">
        <div class="avatar">AD</div>
        <div class="user-info">
          <div class="name">Admin Sys</div>
          <div class="role">Super administrateur</div>
        </div>
      </a>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div class="topbar-title">Notes de l'etudiant</div>
      <div class="topbar-actions">
        <a href="<?= site_url('list') ?>" class="btn btn-secondary btn-sm">Retour a la liste</a>
      </div>
    </div>

    <div class="content">
      <div class="page-header">
        <div>
          <h2><?= esc($etudiant['nom'] . ' ' . $etudiant['prenoms']) ?></h2>
          <div class="breadcrumb">Accueil / Etudiants / <span>Notes</span></div>
        </div>
      </div>

      <div class="notes-toolbar">
        <div class="student-meta">
          <span>ID: <strong><?= esc($etudiant['id']) ?></strong></span>
          <span>-</span>
          <span>Date naissance: <strong><?= esc($etudiant['date_naissance']) ?></strong></span>
          <span>-</span>
          <span>Lieu: <strong><?= esc($etudiant['lieu_naissance']) ?></strong></span>
        </div>
      </div>

      <div class="tabs-row">
        <?php foreach ($tabs as $tabKey => $tabLabel): ?>
          <a class="tab-pill <?= $tab === $tabKey ? 'active' : '' ?>" href="<?= site_url('etudiants/' . $etudiant['id'] . '/notes') . '?tab=' . $tabKey ?>">
            <?= esc($tabLabel) ?>
          </a>
        <?php endforeach; ?>
      </div>

      <div class="table-card">
        <table>
          <thead>
            <tr>
              <th>Code UE</th>
              <th>Intitule</th>
              <th>Credits</th>
              <th>Semestre</th>
              <th>Note</th>
            </tr>
          </thead>
          <tbody>
          <?php if (empty($notes)): ?>
            <tr>
              <td colspan="5" class="empty-state">Aucune note disponible pour cet onglet.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($notes as $row): ?>
            <tr>
              <td><?= esc($row['code_ue']) ?></td>
          
          <td>   <a href="<?= site_url('/note/detail/' . $etudiant['id'] . '/' . $row['cours_id']) ?>">
    <?= esc($row['intitule']) ?>
</a></td>
              <td><?= esc($row['credits']) ?></td>
              <td><?= esc($row['semestre']) ?></td>
              <td class="note-value"><?= esc(number_format((float) $row['note'], 2, '.', '')) ?></td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php if ($moyenneGenerale !== null): ?>
      <div class="summary-card">
        <div class="summary-label">Moyenne generale L2</div>
        <div class="summary-value"><?= esc(number_format((float) $moyenneGenerale, 2, '.', '')) ?></div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>
