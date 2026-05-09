<?= $this->extend('layout') ?>
<?= $this->section('title') ?>Admin — Demandes Code Promo<?= $this->endSection() ?>
<?= $this->section('content') ?>

<link href="<?= base_url('assets/template/admin.css') ?>" rel="stylesheet">

<div style="max-width:960px; margin:0 auto; padding:32px 20px;">

  <!-- EN-TÊTE -->
  <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:28px;">
    <div>
      <h1 style="font-size:22px; font-weight:700; margin:0;">Demandes de code promo</h1>
      <p style="color:#666; font-size:14px; margin:4px 0 0;">
        Validez ou rejetez les demandes en attente.
      </p>
    </div>
    <!-- Badge notifications non lues -->
    <div style="position:relative; display:inline-block;">
      <i class="bi bi-bell" style="font-size:22px; color:#444;"></i>
      <span id="badgeNotif"
            style="display:none; position:absolute; top:-6px; right:-8px;
                   background:#e74c3c; color:#fff; font-size:10px; font-weight:700;
                   border-radius:50%; width:18px; height:18px;
                   line-height:18px; text-align:center;">
      </span>
    </div>
  </div>

  <!-- FILTRES -->
  <div style="display:flex; gap:8px; margin-bottom:20px; flex-wrap:wrap;">
    <?php
      $filtres = [
        null          => ['label' => 'Toutes',     'color' => '#6c757d'],
        'EN_ATTENTE'  => ['label' => 'En attente', 'color' => '#856404'],
        'VALIDE'      => ['label' => 'Validées',   'color' => '#155724'],
        'REJETE'      => ['label' => 'Rejetées',   'color' => '#721c24'],
      ];
      foreach ($filtres as $val => $f):
        $active  = ($statut ?? null) === $val;
        $bgColor = $active ? $f['color'] : '#f1f1f1';
        $txtColor= $active ? '#fff'      : $f['color'];
    ?>
      <a href="<?= base_url('admin/codepromo/demandes' . ($val ? '?statut=' . $val : '')) ?>"
         style="padding:6px 16px; border-radius:20px; font-size:13px; font-weight:600;
                text-decoration:none; background:<?= $bgColor ?>; color:<?= $txtColor ?>;
                border:1px solid <?= $f['color'] ?>; transition:.15s;">
        <?= $f['label'] ?>
      </a>
    <?php endforeach ?>
  </div>

  <!-- MESSAGE FLASH -->
  <?php if (session()->getFlashdata('success')): ?>
    <div style="padding:12px 14px; background:#d4edda; border:1px solid #28a745;
                border-radius:8px; color:#155724; margin-bottom:16px; font-size:14px;">
      <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
    </div>
  <?php endif ?>
  <?php if (session()->getFlashdata('error')): ?>
    <div style="padding:12px 14px; background:#f8d7da; border:1px solid #f5c6cb;
                border-radius:8px; color:#721c24; margin-bottom:16px; font-size:14px;">
      <i class="bi bi-x-circle"></i> <?= session()->getFlashdata('error') ?>
    </div>
  <?php endif ?>

  <!-- TABLEAU -->
  <?php if (empty($demandes)): ?>
    <div style="text-align:center; padding:48px; color:#999;">
      <i class="bi bi-inbox" style="font-size:36px; display:block; margin-bottom:12px;"></i>
      Aucune demande trouvée.
    </div>
  <?php else: ?>

    <div style="background:#fff; border-radius:12px; border:1px solid #e8e8e8; overflow:hidden;">
      <table style="width:100%; border-collapse:collapse; font-size:14px;">
        <thead>
          <tr style="background:#f8f9fa; border-bottom:2px solid #e8e8e8;">
            <th style="padding:12px 16px; text-align:left; font-weight:600; color:#444;">#</th>
            <th style="padding:12px 16px; text-align:left; font-weight:600; color:#444;">Utilisateur</th>
            <th style="padding:12px 16px; text-align:left; font-weight:600; color:#444;">Code</th>
            <th style="padding:12px 16px; text-align:left; font-weight:600; color:#444;">Montant</th>
            <th style="padding:12px 16px; text-align:left; font-weight:600; color:#444;">Date</th>
            <th style="padding:12px 16px; text-align:left; font-weight:600; color:#444;">Statut</th>
            <th style="padding:12px 16px; text-align:center; font-weight:600; color:#444;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($demandes as $d): ?>
          <?php
            $badges = [
              'EN_ATTENTE' => ['bg'=>'#fff3cd','border'=>'#ffc107','color'=>'#856404','label'=>'En attente'],
              'VALIDE'     => ['bg'=>'#d4edda','border'=>'#28a745','color'=>'#155724','label'=>'Validée'   ],
              'REJETE'     => ['bg'=>'#f8d7da','border'=>'#f5c6cb','color'=>'#721c24','label'=>'Rejetée'   ],
            ];
            $b = $badges[$d['statut']] ?? $badges['EN_ATTENTE'];
          ?>
          <tr style="border-bottom:1px solid #f0f0f0;" class="tr-row">
            <td style="padding:12px 16px; color:#999;"><?= $d['id'] ?></td>
            <td style="padding:12px 16px;">
              <div style="font-weight:600;"><?= esc($d['user_nom']) ?></div>
              <div style="font-size:12px; color:#999;"><?= esc($d['user_email']) ?></div>
            </td>
            <td style="padding:12px 16px;">
              <code style="background:#f1f1f1; padding:3px 8px; border-radius:4px;
                           font-size:13px; font-weight:700; letter-spacing:.5px;">
                <?= esc($d['code']) ?>
              </code>
            </td>
            <td style="padding:12px 16px; font-weight:600; color:#1a73e8;">
              <?= number_format($d['montant'], 2) ?> Ar
            </td>
            <td style="padding:12px 16px; color:#666; font-size:13px;">
              <?= date('d/m/Y H:i', strtotime($d['date_demande'])) ?>
            </td>
            <td style="padding:12px 16px;">
              <span style="display:inline-block; padding:3px 10px;
                           background:<?= $b['bg'] ?>; border:1px solid <?= $b['border'] ?>;
                           color:<?= $b['color'] ?>; border-radius:20px; font-size:12px; font-weight:600;">
                <?= $b['label'] ?>
              </span>
              <?php if ($d['statut'] === 'REJETE' && $d['motif_rejet']): ?>
                <div style="font-size:11px; color:#999; margin-top:3px;">
                  <?= esc($d['motif_rejet']) ?>
                </div>
              <?php endif ?>
            </td>
            <td style="padding:12px 16px; text-align:center;">
              <?php if ($d['statut'] === 'EN_ATTENTE'): ?>
                <div style="display:flex; gap:6px; justify-content:center;">
                  <button
                    onclick="valider(<?= $d['id'] ?>, this)"
                    style="padding:5px 12px; background:#28a745; color:#fff; border:none;
                           border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;">
                    <i class="bi bi-check-lg"></i> Valider
                  </button>
                  <button
                    onclick="ouvrirRejet(<?= $d['id'] ?>)"
                    style="padding:5px 12px; background:#e74c3c; color:#fff; border:none;
                           border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;">
                    <i class="bi bi-x-lg"></i> Rejeter
                  </button>
                </div>
              <?php else: ?>
                <span style="font-size:12px; color:#bbb;">—</span>
              <?php endif ?>
            </td>
          </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>

  <?php endif ?>

</div>

<!-- MODAL REJET -->
<div id="modalRejet"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45);
            z-index:1000; align-items:center; justify-content:center;">
  <div style="background:#fff; border-radius:12px; padding:28px; width:100%;
              max-width:420px; margin:0 16px; box-shadow:0 8px 32px rgba(0,0,0,.15);">
    <h3 style="margin:0 0 8px; font-size:16px;">Rejeter la demande</h3>
    <p style="font-size:13px; color:#666; margin:0 0 16px;">
      Indiquez un motif (optionnel). L'utilisateur le verra dans ses demandes.
    </p>
    <textarea
      id="motifRejet"
      rows="3"
      placeholder="Ex: Code réservé aux membres Gold..."
      style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px;
             font-size:13px; resize:none; outline:none; box-sizing:border-box;"
    ></textarea>
    <div style="display:flex; gap:8px; margin-top:16px; justify-content:flex-end;">
      <button
        onclick="fermerModal()"
        style="padding:8px 18px; background:#f1f1f1; color:#444; border:none;
               border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;">
        Annuler
      </button>
      <button
        id="btnConfirmRejet"
        style="padding:8px 18px; background:#e74c3c; color:#fff; border:none;
               border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;">
        Confirmer le rejet
      </button>
    </div>
  </div>
</div>

<script>
let demandeIdEnCours = null;

// ── Valider ────────────────────────────────────────────────────
async function valider(id, btn) {
  if (!confirm('Valider cette demande et créditer le compte ?')) return;

  btn.disabled  = true;
  btn.innerHTML = '<i class="bi bi-hourglass-split"></i>';

  // ✅ DEBUG : afficher la réponse brute avant json()
  const res  = await fetch('<?= base_url('admin/codepromo/valider') ?>', {
    method : 'POST',
    headers: {
      'Content-Type'     : 'application/x-www-form-urlencoded',
      'X-Requested-With' : 'XMLHttpRequest'
    },
    body: 'demande_id=' + id
  });

  // ✅ Lire comme texte d'abord pour voir ce que le serveur renvoie vraiment
  const raw = await res.text();
  console.log('Réponse brute du serveur :', raw);
  console.log('Status HTTP :', res.status);

  try {
    const data = JSON.parse(raw);
    if (data.success) {
      window.location.reload();
    } else {
      alert('Erreur : ' + data.message);
      btn.disabled  = false;
      btn.innerHTML = '<i class="bi bi-check-lg"></i> Valider';
    }
  } catch {
    // ✅ Si ce n'est pas du JSON → afficher le HTML d'erreur
    alert('Le serveur a retourné une erreur HTML. Voir console.');
    btn.disabled  = false;
    btn.innerHTML = '<i class="bi bi-check-lg"></i> Valider';
  }
}

// ── Ouvrir modal rejet ─────────────────────────────────────────
function ouvrirRejet(id) {
  demandeIdEnCours = id;
  document.getElementById('motifRejet').value = '';
  document.getElementById('modalRejet').style.display = 'flex';
}

function fermerModal() {
  document.getElementById('modalRejet').style.display = 'none';
  demandeIdEnCours = null;
}

// ── Confirmer le rejet ─────────────────────────────────────────
document.getElementById('btnConfirmRejet').addEventListener('click', async function() {
  if (!demandeIdEnCours) return;

  const motif = document.getElementById('motifRejet').value.trim();
  this.disabled  = true;
  this.innerHTML = 'Traitement...';

  const res  = await fetch('<?= base_url('admin/codepromo/rejeter') ?>', {
    method : 'POST',
    headers: {
      'Content-Type'     : 'application/x-www-form-urlencoded',
      'X-Requested-With' : 'XMLHttpRequest'
    },
    body: 'demande_id=' + demandeIdEnCours + '&motif=' + encodeURIComponent(motif)
  });
  const data = await res.json();

  if (data.success) {
    window.location.reload();
  } else {
    alert('Erreur : ' + data.message);
    this.disabled  = false;
    this.innerHTML = 'Confirmer le rejet';
  }
});

// Fermer modal en cliquant l'overlay
document.getElementById('modalRejet').addEventListener('click', function(e) {
  if (e.target === this) fermerModal();
});

// ── Badge notifications ────────────────────────────────────────
async function refreshBadge() {
  try {
    const res  = await fetch('<?= base_url('admin/codepromo/notifications') ?>', {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    const badge = document.getElementById('badgeNotif');
    if (data.count > 0) {
      badge.style.display = 'block';
      badge.textContent   = data.count;
    } else {
      badge.style.display = 'none';
    }
  } catch { /* silencieux */ }
}

// Rafraîchir le badge toutes les 30 secondes
refreshBadge();
setInterval(refreshBadge, 30000);
</script>

<?= $this->endSection() ?>