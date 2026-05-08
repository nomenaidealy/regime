<?= $this->extend('layout') ?>
<?= $this->section('title') ?>NutriPlan — Code Promo<?= $this->endSection() ?>
<?= $this->section('content') ?>

<link href="<?= base_url('assets/template/regimeForm.css') ?>" rel="stylesheet">

<div class="regime-container">

  <!-- LEFT -->
  <div class="regime-left">
    <div class="logo">Nutri<span>Plan</span></div>
    <div>
      <h2>Utiliser un code promo</h2>
      <p>Entrez votre code pour soumettre une demande de crédit.</p>
      <p style="margin-top:12px; font-size:13px; opacity:.75;">
        Votre demande sera examinée par un administrateur.<br>
        Vous serez crédité dès validation.
      </p>
    </div>
    <div class="regime-left-bottom">
      <a href="<?= base_url('dashboard') ?>">
        <i class="bi bi-arrow-left"></i> Retour
      </a>
    </div>
  </div>

  <!-- RIGHT -->
  <div class="regime-right">

    <div class="form-header">
      <div class="tag">Portefeuille</div>
      <h1>Code Promo</h1>
      <p>Saisissez votre code. Un administrateur validera votre demande.</p>
    </div>

    <!-- FORMULAIRE -->
    <form id="codePromoForm" style="max-width:420px;">

      <div style="margin-bottom:18px;">
        <label for="code" style="display:block; margin-bottom:6px; font-weight:600;">
          Code promo
        </label>
        <input
          type="text"
          id="code"
          name="code"
          placeholder="EX: PROMO2026"
          autocomplete="off"
          required
          style="width:100%; padding:10px 14px; border:1px solid #ddd;
                 border-radius:8px; font-size:14px; text-transform:uppercase;
                 outline:none; transition:.2s;"
          onfocus="this.style.borderColor='#1a73e8'"
          onblur="this.style.borderColor='#ddd'"
        >
        <small style="color:#999; display:block; margin-top:4px;">
          Le code est insensible à la casse
        </small>
      </div>

      <button
        type="submit"
        id="submitBtn"
        style="width:100%; padding:12px; background:#1a73e8; color:#fff;
               border:none; border-radius:8px; font-size:14px; font-weight:600;
               cursor:pointer; transition:.2s; display:flex; align-items:center;
               justify-content:center; gap:8px;"
        onmouseover="this.style.background='#1557b0'"
        onmouseout="this.style.background='#1a73e8'"
      >
        <i class="bi bi-send"></i>
        Soumettre la demande
      </button>

    </form>

    <!-- RÉSULTAT -->
    <div id="resultMessage" style="margin-top:20px; max-width:420px;"></div>

    <!-- STATUT DES DEMANDES PRÉCÉDENTES -->
    <div style="margin-top:36px; max-width:420px;">
      <h3 style="font-size:14px; font-weight:600; margin-bottom:12px; color:#444;">
        Mes demandes récentes
      </h3>
      <div id="demandesListe">
        <div style="text-align:center; padding:16px; color:#999; font-size:13px;">
          <i class="bi bi-hourglass-split"></i> Chargement...
        </div>
      </div>
    </div>

  </div>
</div>

<script>
// ── Charger les demandes de l'utilisateur ──────────────────────
async function loadDemandes() {
  const container = document.getElementById('demandesListe');
  try {
    const res  = await fetch('<?= base_url('codepromo/mesDemandes') ?>', {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();

    if (!data.success || data.data.length === 0) {
      container.innerHTML = '<p style="color:#999; font-size:13px;">Aucune demande pour le moment.</p>';
      return;
    }

    const badges = {
      EN_ATTENTE : { bg:'#fff3cd', border:'#ffc107', color:'#856404', icon:'bi-hourglass-split',  label:'En attente' },
      VALIDE     : { bg:'#d4edda', border:'#28a745', color:'#155724', icon:'bi-check-circle',      label:'Validée'    },
      REJETE     : { bg:'#f8d7da', border:'#f5c6cb', color:'#721c24', icon:'bi-x-circle',          label:'Rejetée'    },
    };

    container.innerHTML = data.data.map(d => {
      const b = badges[d.statut] || badges['EN_ATTENTE'];
      return `
        <div style="display:flex; align-items:center; justify-content:space-between;
                    padding:10px 14px; border:1px solid ${b.border}; border-radius:8px;
                    background:${b.bg}; margin-bottom:8px;">
          <div>
            <strong style="color:${b.color}; font-size:13px;">${d.code}</strong>
            <span style="font-size:12px; color:#666; margin-left:8px;">${d.montant} Ar</span>
            ${d.motif_rejet
              ? `<div style="font-size:11px; color:#721c24; margin-top:2px;">
                   Motif : ${d.motif_rejet}
                 </div>`
              : ''}
          </div>
          <span style="font-size:12px; color:${b.color}; white-space:nowrap;">
            <i class="bi ${b.icon}"></i> ${b.label}
          </span>
        </div>`;
    }).join('');

  } catch {
    container.innerHTML = '<p style="color:#999; font-size:13px;">Impossible de charger les demandes.</p>';
  }
}

// ── Soumettre le code ──────────────────────────────────────────
document.getElementById('codePromoForm').addEventListener('submit', async function(e) {
  e.preventDefault();

  const code      = document.getElementById('code').value.trim();
  const resultDiv = document.getElementById('resultMessage');
  const btn       = document.getElementById('submitBtn');

  resultDiv.innerHTML = '';

  if (!code) {
    resultDiv.innerHTML = msgBox('warning', 'Veuillez saisir un code promo.');
    return;
  }

  btn.disabled   = true;
  btn.innerHTML  = '<i class="bi bi-hourglass-split"></i> Envoi en cours...';

  try {
    const res  = await fetch('<?= base_url('codepromo/redeem') ?>', {
      method : 'POST',
      headers: {
        'Content-Type'     : 'application/x-www-form-urlencoded',
        'X-Requested-With' : 'XMLHttpRequest'
      },
      body: 'code=' + encodeURIComponent(code)
    });
    const data = await res.json();

    if (data.success) {
      resultDiv.innerHTML = msgBox('success',
        '<strong>Demande envoyée !</strong> ' + data.message
      );
      document.getElementById('code').value = '';
      loadDemandes();
    } else {
      resultDiv.innerHTML = msgBox('danger', '<strong>Erreur : </strong>' + data.message);
    }

  } catch {
    resultDiv.innerHTML = msgBox('danger', '<strong>Erreur</strong> Une erreur réseau est survenue.');
  } finally {
    btn.disabled  = false;
    btn.innerHTML = '<i class="bi bi-send"></i> Soumettre la demande';
  }
});

// ── Helper style message ───────────────────────────────────────
function msgBox(type, html) {
  const styles = {
    success : { bg:'#d4edda', border:'#28a745', color:'#155724' },
    danger  : { bg:'#f8d7da', border:'#f5c6cb', color:'#721c24' },
    warning : { bg:'#fff3cd', border:'#ffc107', color:'#856404' },
  };
  const s = styles[type];
  return `<div style="padding:12px 14px; background:${s.bg}; border:1px solid ${s.border};
                      border-radius:8px; color:${s.color}; font-size:14px;">
            ${html}
          </div>`;
}

// ── Init ───────────────────────────────────────────────────────
loadDemandes();
</script>

<?= $this->endSection() ?>