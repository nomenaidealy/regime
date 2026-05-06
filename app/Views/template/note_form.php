<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SysInfo — Formulaire de saisie de note</title>
  <link rel="stylesheet" href="<?= base_url('assets/template/style.css') ?>" />
</head>
<body>

<div class="app">

  <!-- ── Sidebar ──────────────────────────────────────────────────────────── -->
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

   
    <a href="<?= site_url('list') ?>" class="nav-item">
      <svg viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
      Utilisateurs
    </a>
    <a href="<?= site_url('note/form') ?>" class="nav-item active">
      <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
      Saisie de note
    </a>

    <div class="sidebar-section">Modules</div>

    <a href="#" class="nav-item">
      <svg viewBox="0 0 24 24"><path d="M3 3h7v7H3zM14 3h7v7h-7zM14 14h7v7h-7zM3 14h7v7H3z"/></svg>
      Catalogue
    </a>
    <a href="#" class="nav-item">
      <svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      Comptabilité
    </a>
    <a href="#" class="nav-item">
      <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      RH
    </a>
    <a href="#" class="nav-item">
      <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
      Rapports
    </a>

    <div class="sidebar-section">Système</div>

    <a href="#" class="nav-item">
      <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
      Paramètres
    </a>

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

  <!-- ── Main ─────────────────────────────────────────────────────────────── -->
  <div class="main">

    <div class="topbar">
      <div class="topbar-title">Saisie de note</div>
      <div class="topbar-search">
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Rechercher…" />
      </div>
      <div class="topbar-actions">
        <button class="icon-btn">
          <svg viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        </button>
        <button class="icon-btn">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>
        </button>
      </div>
    </div>

    <div class="content">

      <div class="page-header">
        <div>
          <h2>Saisir une nouvelle note</h2>
          <div class="breadcrumb">Accueil / Notes / <span>Nouvelle</span></div>
        </div>
        <a href="<?= site_url('dashboard') ?>" class="btn btn-secondary btn-sm">
          <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
          Retour
        </a>
      </div>

      <div class="alert alert-info">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>Les champs marqués d'un <strong>*</strong> sont obligatoires.</span>
      </div>

      <form id="noteForm">

        <!-- ── 1. Sélection d'étudiant ──────────────────────────────── -->
        <div class="form-card section-gap">
          <div class="form-section-title">1. Informations de l'étudiant</div>
          <div class="form-grid">
            <div>
              <label class="field-label">Étudiant (par matricule) <span class="required">*</span></label>
              <select id="inscriptionSelect" name="inscription_id" required>
                <option value="">— Sélectionner un étudiant —</option>
                <?php foreach ($inscriptions as $inscription): ?>
                  <option value="<?= $inscription['id'] ?>" data-nom="<?= esc($inscription['nom']) ?>" data-prenoms="<?= esc($inscription['prenoms']) ?>">
                    <?= $inscription['matricule'] ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="field-label">Nom &amp; Prénom</label>
              <input type="text" id="studentName" placeholder="Auto-rempli" readonly />
            </div>
          </div>
        </div>

        <!-- ── 2. Sélection du cours et note ────────────────────────── -->
        <div class="form-card section-gap">
          <div class="form-section-title">2. Cours et note</div>
          <div class="form-grid">
            <div>
              <label class="field-label">Semestre <span class="required">*</span></label>
              <select id="semesterSelect" name="semester" required>
                <option value="">— Sélectionner un semestre —</option>
                <?php foreach ($semesters as $semester): ?>
                  <option value="<?= $semester['semestre'] ?>">Semestre <?= $semester['semestre'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="field-label">Cours <span class="required">*</span></label>
              <select id="courseSelect" name="cours_id" required>
                <option value="">— Sélectionner un cours —</option>
              </select>
            </div>
            <div>
              <label class="field-label">Note <span class="required">*</span></label>
              <div class="input-group">
                <input type="number" id="noteInput" name="note" min="0" max="20" step="0.25" placeholder="Ex : 15.5" required />
                <span class="addon addon-right">/ 20</span>
              </div>
            </div>
          </div>
        </div>

        <!-- ── Footer boutons ─────────────────────────────────────────── -->
        <div class="form-footer">
          <a href="<?= site_url('dashboard') ?>" class="btn btn-secondary">Annuler</a>
          <button type="submit" class="btn btn-primary">
            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            Enregistrer la note
          </button>
        </div>

      </form>

    </div><!-- /content -->
  </div><!-- /main -->
</div><!-- /app -->

<script>
  const inscriptionSelect = document.getElementById('inscriptionSelect');
  const studentNameField = document.getElementById('studentName');
  const semesterSelect = document.getElementById('semesterSelect');
  const courseSelect = document.getElementById('courseSelect');
  const noteForm = document.getElementById('noteForm');
  const allCourses = <?= json_encode($courses, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

  function updateStudentName() {
    const selectedOption = inscriptionSelect.selectedOptions[0];

    if (!selectedOption || !selectedOption.value) {
      studentNameField.value = '';
      return;
    }

    const nom = selectedOption.dataset.nom || '';
    const prenoms = selectedOption.dataset.prenoms || '';
    studentNameField.value = `${nom} ${prenoms}`.trim();
  }

  inscriptionSelect.addEventListener('change', updateStudentName);
  updateStudentName();

  function renderCourses(semester) {
    courseSelect.innerHTML = '<option value="">— Sélectionner un cours —</option>';

    if (!semester) {
      return;
    }

    const filteredCourses = allCourses.filter(course => String(course.semestre) === String(semester));

    filteredCourses.forEach(course => {
      const option = document.createElement('option');
      option.value = course.id;
      option.textContent = `${course.code_ue} - ${course.intitule}`;
      courseSelect.appendChild(option);
    });
  }

  // Load courses when semester is selected
  semesterSelect.addEventListener('change', function () {
    renderCourses(this.value);
  });

  // Handle form submission
  noteForm.addEventListener('submit', async function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    try {
      const response = await fetch(`<?= site_url('note/save') ?>`, {
        method: 'POST',
        body: formData,
      });

      const responseText = await response.text();
      let payload = {};

      try {
        payload = responseText ? JSON.parse(responseText) : {};
      } catch (parseError) {
        payload = { message: responseText || 'Réponse serveur invalide' };
      }

      if (response.ok) {
        alert('Note enregistrée avec succès!');
        document.getElementById('noteForm').reset();
        document.getElementById('studentName').value = '';
        courseSelect.innerHTML = '<option value="">— Sélectionner un cours —</option>';
      } else {
        const errorMessage = payload.message || 'Erreur inconnue';
        const errorDetails = payload.errors ? JSON.stringify(payload.errors) : '';
        alert(`Erreur: ${errorMessage}${errorDetails ? ' ' + errorDetails : ''}`);
      }
    } catch (error) {
      console.error('Error saving note:', error);
      alert('Une erreur est survenue lors de l\'enregistrement.');
    }
  });
</script>

</body>
</html>
