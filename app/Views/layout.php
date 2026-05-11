<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $this->renderSection('title') ?></title>
  <link href="<?= base_url('assets/template/accueil.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/bootstrap/bootstrap-icons/bootstrap-icons.min.css') ?>" rel="stylesheet">
</head>
<body>

<nav>
  <div class="nav-logo">Nutri<span>Plan</span></div>
  <div class="nav-links">

    <?php if (session()->get('is_admin')): ?>

      <!-- ── Cloche notifications (dropdown) ───────────────── -->
      <div style="position:relative; display:inline-block;">

        <button id="btnNotif"
                style="background:#fff3cd; border:1px solid #f0c36d; cursor:pointer;
                       display:inline-flex; align-items:center; gap:8px;
                       padding:10px 14px; border-radius:999px; font-weight:700;
                       color:#8a6d3b; font-size:14px; position:relative;">
          <i class="bi bi-bell-fill"></i>
          <span>Notifications</span>
          <span id="badgeNotif"
                style="display:none; position:absolute; top:-7px; right:-7px;
                       min-width:22px; height:22px; padding:0 6px;
                       background:#e74c3c; color:#fff; border-radius:999px;
                       font-size:11px; font-weight:700;
                       display:none; align-items:center; justify-content:center;
                       box-shadow:0 2px 6px rgba(0,0,0,.15);">
          </span>
        </button>

        <!-- Dropdown -->
        <div id="dropdownNotif"
             style="display:none; position:absolute; right:0; top:calc(100% + 8px);
                    width:340px; background:#fff; border:1px solid #e0e0e0;
                    border-radius:14px; box-shadow:0 8px 28px rgba(0,0,0,.13);
                    z-index:9999; overflow:hidden;">

          <!-- Header dropdown -->
          <div style="padding:13px 16px; border-bottom:1px solid #f0f0f0;
                      display:flex; justify-content:space-between; align-items:center;">
            <span style="font-weight:700; font-size:14px;">Notifications</span>
            <span id="notifCountLabel"
                  style="font-size:12px; color:#999;"></span>
          </div>

          <!-- Liste -->
          <div id="notifListe" style="max-height:380px; overflow-y:auto;">
            <div style="padding:24px; text-align:center; color:#bbb; font-size:13px;">
              <i class="bi bi-hourglass-split"></i> Chargement...
            </div>
          </div>

          <!-- Footer dropdown -->
          <div style="padding:10px 16px; border-top:1px solid #f0f0f0;
                      display:flex; gap:8px; justify-content:center;">
            <a href="<?= base_url('admin/codepromo/demandes') ?>"
               style="font-size:12px; color:#1a73e8; text-decoration:none;
                      padding:4px 10px; border-radius:6px; background:#f0f4ff;">
              <i class="bi bi-tag"></i> Codes promo
            </a>
            <a href="<?= base_url('admin/gold/demandes') ?>"
               style="font-size:12px; color:#8a6d3b; text-decoration:none;
                      padding:4px 10px; border-radius:6px; background:#fff3cd;">
              <i class="bi bi-star"></i> Demandes Gold
            </a>
          </div>

        </div>
      </div>
      <!-- ─────────────────────────────────────────────────── -->

      <a href="<?= base_url('admin/regimes') ?>">Régimes</a>
      <a href="<?= base_url('admin/sports') ?>">Activités Sportives</a>
      <a href="<?= base_url('admin/codepromo/demandes') ?>">Codes Promo</a>
      <a href="<?= base_url('admin/gold/demandes') ?>">Demandes Gold</a>
      <a href="<?= base_url('admin/gold/new') ?>" title="Paramètres Gold" style="display:inline-flex; align-items:center; gap:6px;">
        <i class="bi bi-gear-fill"></i> Paramètres Gold
      </a>
      <a href="<?= base_url('admin/dashboard') ?>">Dashboard</a>
      <a href="<?= base_url('admin/logout') ?>" class="btn-nav">Déconnexion</a>

    <?php elseif (session()->get('user_id')): ?>

      <a href="<?= base_url('/') ?>#about">À propos</a>
      <a href="<?= base_url('/') ?>#mission">Notre mission</a>
      <a href="<?= base_url('/') ?>#fonctionnalites">Fonctionnalités</a>
      <a href="<?= base_url('/') ?>#gold">Option Gold</a>
      <a href="<?= base_url('user/mes-regimes') ?>">Mes régimes</a>
      <a href="<?= base_url('user/profil') ?>">Profil</a>
      <a href="<?= base_url('logout') ?>" class="btn-nav">Déconnexion</a>

    <?php else: ?>

      <a href="<?= base_url('/') ?>#about">À propos</a>
      <a href="<?= base_url('/') ?>#mission">Notre mission</a>
      <a href="<?= base_url('/') ?>#fonctionnalites">Fonctionnalités</a>
      <a href="<?= base_url('/') ?>#gold">Option Gold</a>
      <a href="<?= base_url('login') ?>">Se connecter</a>
      <a href="<?= base_url('admin') ?>" class="btn-nav">Connexion admin</a>
      <a href="<?= base_url('inscription') ?>" class="btn-nav">Commencer</a>

    <?php endif; ?>

  </div>
</nav>

<!-- ── Flash messages ─────────────────────────────────────────── -->
<?php if (session()->getFlashdata('success')): ?>
  <div class="alert alert-success">
    <i class="bi bi-check-circle-fill"></i>
    <?= esc(session()->getFlashdata('success')) ?>
  </div>
<?php endif ?>
<?php if (session()->getFlashdata('warning')): ?>
  <div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <?= esc(session()->getFlashdata('warning')) ?>
  </div>
<?php endif ?>
<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-danger">
    <i class="bi bi-x-circle-fill"></i>
    <?= esc(session()->getFlashdata('error')) ?>
  </div>
<?php endif ?>

<!-- ── Contenu ────────────────────────────────────────────────── -->
<?= $this->renderSection('content') ?>

<!-- ── Footer ─────────────────────────────────────────────────── -->
<footer>
  <div class="footer-logo">Nutri<span>Plan</span></div>
  <p>© 2026 NutriPlan — Application de régime alimentaire personnalisé</p>
</footer>

<!-- ── Script notifications (admin seulement) ────────────────── -->
<?php if (session()->get('is_admin')): ?>
<script>
const NOTIF_URL = '<?= base_url('admin/notifications') ?>';

// ── Toggle dropdown ────────────────────────────────────────────
document.getElementById('btnNotif').addEventListener('click', function(e) {
  e.stopPropagation();
  const dd     = document.getElementById('dropdownNotif');
  const isOpen = dd.style.display === 'block';
  dd.style.display = isOpen ? 'none' : 'block';
  if (!isOpen) renderNotifications();
});

// Fermer en cliquant ailleurs
document.addEventListener('click', () => {
  document.getElementById('dropdownNotif').style.display = 'none';
});
document.getElementById('dropdownNotif').addEventListener('click', e => e.stopPropagation());

// ── Rendu des notifications dans le dropdown ───────────────────
async function renderNotifications() {
  const liste = document.getElementById('notifListe');
  liste.innerHTML = '<div style="padding:24px; text-align:center; color:#bbb; font-size:13px;"><i class="bi bi-hourglass-split"></i> Chargement...</div>';

  try {
    const res  = await fetch(NOTIF_URL, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();

    updateBadge(data.count ?? 0);

    if (!data.data || data.data.length === 0) {
      liste.innerHTML = `
        <div style="padding:32px; text-align:center; color:#bbb; font-size:13px;">
          <i class="bi bi-bell-slash" style="font-size:28px; display:block; margin-bottom:8px;"></i>
          Aucune notification non lue
        </div>`;
      return;
    }

    liste.innerHTML = data.data.map(n => {
      // Redirection selon type
      const url = n.type === 'CODE_PROMO'
        ? '<?= base_url('admin/codepromo/demandes') ?>?statut=EN_ATTENTE'
        : '<?= base_url('admin/gold/demandes') ?>?statut=EN_ATTENTE';

      // Icône + couleur selon type
      const isGold  = n.type === 'DEMANDE_GOLD';
      const icon    = isGold
        ? '<i class="bi bi-star-fill" style="color:#f0a500; font-size:16px;"></i>'
        : '<i class="bi bi-tag-fill"  style="color:#1a73e8; font-size:16px;"></i>';
      const bgIcon  = isGold ? '#fff8e1' : '#e8f0fe';

      // Temps relatif
      const date  = new Date(n.date_creation.replace(' ', 'T'));
      const diff  = Math.floor((Date.now() - date) / 60000);
      const temps = diff < 1    ? 'À l\'instant'
                  : diff < 60   ? diff + ' min'
                  : diff < 1440 ? Math.floor(diff / 60) + 'h'
                  : Math.floor(diff / 1440) + 'j';

      return `
        <a href="${url}"
           style="display:flex; align-items:flex-start; gap:12px; padding:13px 16px;
                  text-decoration:none; color:inherit; border-bottom:1px solid #f5f5f5;
                  background:#fffdf5; transition:background .15s;"
           onmouseover="this.style.background='#f5f5f5'"
           onmouseout="this.style.background='#fffdf5'">

          <!-- Icône ronde -->
          <div style="width:36px; height:36px; border-radius:50%; background:${bgIcon};
                      display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            ${icon}
          </div>

          <!-- Texte -->
          <div style="flex:1; min-width:0;">
            <div style="font-size:13px; color:#222; line-height:1.45; word-break:break-word;">
              ${n.message}
            </div>
            <div style="display:flex; align-items:center; gap:6px; margin-top:4px;">
              <span style="font-size:11px; color:#999;">${temps}</span>
              <span style="width:4px; height:4px; border-radius:50%;
                           background:${isGold ? '#f0a500' : '#1a73e8'};
                           display:inline-block;"></span>
              <span style="font-size:11px; color:${isGold ? '#f0a500' : '#1a73e8'}; font-weight:600;">
                ${isGold ? 'Gold' : 'Code promo'}
              </span>
            </div>
          </div>

          <!-- Point non lu -->
          <div style="width:8px; height:8px; border-radius:50%; background:#e74c3c;
                      margin-top:4px; flex-shrink:0;"></div>
        </a>`;
    }).join('');

  } catch {
    liste.innerHTML = '<div style="padding:24px; text-align:center; color:#e74c3c; font-size:13px;">Erreur de chargement</div>';
  }
}

// ── Mettre à jour le badge ─────────────────────────────────────
function updateBadge(count) {
  const badge = document.getElementById('badgeNotif');
  const label = document.getElementById('notifCountLabel');

  if (count > 0) {
    badge.textContent   = count > 9 ? '9+' : count;
    badge.style.display = 'inline-flex';
  } else {
    badge.style.display = 'none';
  }

  if (label) {
    label.textContent = count > 0 ? count + ' non lue(s)' : 'Tout lu';
  }
}

// ── Rafraîchir le badge seul (sans ouvrir le dropdown) ────────
async function refreshBadge() {
  try {
    const res  = await fetch(NOTIF_URL, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    updateBadge(data.count ?? 0);
  } catch {}
}

// Init + polling 30s
refreshBadge();
setInterval(refreshBadge, 30000);
</script>
<?php endif ?>

</body>
</html>