<?= $this->extend('layout') ?>

<?= $this->section('title') ?>NutriPlan — Ajouter un régime<?= $this->endSection() ?>

<?= $this->section('content') ?>

<link href="<?= base_url('assets/template/regimeForm.css') ?>" rel="stylesheet">">
<style>
</style>

<div class="regime-container">

  <!-- LEFT -->
  <div class="regime-left">
    <div class="logo">Nutri<span>Plan</span></div>
    <div>
      <h2>Créer un <em>nouveau régime</em></h2>
      <p>Définissez la composition, la variation de poids, le sport associé et les tarifs selon la durée.</p>
      <div class="info-cards">
        <div class="info-card">
          <i class="bi bi-pie-chart-fill"></i>
          <h4>Composition équilibrée</h4>
          <p>La somme viande + volaille + poisson doit faire exactement 100%.</p>
        </div>
        <div class="info-card">
          <i class="bi bi-clock-history"></i>
          <h4>Prix selon durée</h4>
          <p>Définissez un prix pour 30, 60 et 90 jours.</p>
        </div>
        <div class="info-card">
          <i class="bi bi-activity"></i>
          <h4>Sport associé</h4>
          <p>Chaque régime est lié à une activité physique recommandée.</p>
        </div>
      </div>
    </div>
    <div class="regime-left-bottom">
      <a href="<?= base_url('admin/regimes') ?>"><i class="bi bi-arrow-left"></i> Retour à la liste</a>
    </div>
  </div>

  <!-- RIGHT -->
  <div class="regime-right">

    <div class="form-header">
      <div class="tag">Back Office — Régimes</div>
      <h1>Nouveau régime</h1>
      <p>Remplissez tous les champs pour créer un régime alimentaire.</p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
      <div style="background:#FCEBEB; color:#791F1F; padding:14px 18px; border-radius:12px; margin-bottom:24px; font-size:14px;">
        <i class="bi bi-exclamation-circle"></i> <?= esc(session()->getFlashdata('error')) ?>
      </div>
    <?php endif; ?>

    <form action="<?= base_url('admin/regimes/save') ?>" method="POST" id="regimeForm">
      <?= csrf_field() ?>

      <!-- SECTION 1 : Infos générales -->
      <div class="form-section">
        <div class="form-section-title">
          <i class="bi bi-info-circle-fill"></i> Informations générales
        </div>

        <div class="field">
          <label>Nom du régime</label>
          <input type="text" name="nom" placeholder="Ex: Méditerranéen Minceur" required>
        </div>

        <div class="field">
          <label>Description</label>
          <textarea name="description" placeholder="Décrivez brièvement ce régime..."></textarea>
        </div>
      </div>

      <!-- SECTION 2 : Composition -->
      <div class="form-section">
        <div class="form-section-title">
          <i class="bi bi-pie-chart-fill"></i> Composition alimentaire (total = 100%)
        </div>

        <div class="field-row-3">
          <div class="field">
            <label><span style="color:#C0392B">●</span> Viande (%)</label>
            <input type="number" name="viande_percent" id="viande" placeholder="Ex: 30" min="0" max="100" oninput="updateBar()">
          </div>
          <div class="field">
            <label><span style="color:#E67E22">●</span> Volaille (%)</label>
            <input type="number" name="volaille_percent" id="volaille" placeholder="Ex: 40" min="0" max="100" oninput="updateBar()">
          </div>
          <div class="field">
            <label><span style="color:#2980B9">●</span> Poisson (%)</label>
            <input type="number" name="poisson_percent" id="poisson" placeholder="Ex: 30" min="0" max="100" oninput="updateBar()">
          </div>
        </div>

        <!-- Barre de prévisualisation -->
        <div class="percent-preview">
          <div class="percent-bar">
            <div class="bar-viande"   id="barViande"   style="width:0%"></div>
            <div class="bar-volaille" id="barVolaille" style="width:0%"></div>
            <div class="bar-poisson"  id="barPoisson"  style="width:0%"></div>
          </div>
          <div class="percent-legend">
            <div class="leg"><div class="leg-dot" style="background:#C0392B"></div> Viande</div>
            <div class="leg"><div class="leg-dot" style="background:#E67E22"></div> Volaille</div>
            <div class="leg"><div class="leg-dot" style="background:#2980B9"></div> Poisson</div>
          </div>
          <div class="percent-total" id="percentTotal">0%</div>
        </div>
      </div>

      <!-- SECTION 3 : Variation poids -->
      <div class="form-section">
        <div class="form-section-title">
          <i class="bi bi-arrow-left-right"></i> Variation de poids
        </div>

        <div class="field-row">
          <div class="field">
            <label>Sens de variation</label>
            <div class="variation-group">
              <div class="variation-option">
                <input type="radio" name="sens_variation" id="sens_moins" value="-1">
                <label for="sens_moins">
                  <span class="v-icon"><i class="bi bi-graph-down-arrow" style="color:#2D6A4F"></i></span>
                  <span class="v-label">Perte</span>
                  <span class="v-sub">Réduire le poids</span>
                </label>
              </div>
              <div class="variation-option">
                <input type="radio" name="sens_variation" id="sens_plus" value="1">
                <label for="sens_plus">
                  <span class="v-icon"><i class="bi bi-graph-up-arrow" style="color:#2D6A4F"></i></span>
                  <span class="v-label">Gain</span>
                  <span class="v-sub">Augmenter le poids</span>
                </label>
              </div>
            </div>
          </div>
          <div class="field">
            <label>Quantité (kg/jour)</label>
            <input type="number" name="variation_valeur" id="variation_valeur" placeholder="Ex: 0.10" step="0.01" min="0.01" max="1">
            <input type="hidden" name="variation_poids_jour" id="hidden_variation">
          </div>
        </div>
      </div>

      <!-- SECTION 4 : Sport associé -->
      <div class="form-section">
        <div class="form-section-title">
          <i class="bi bi-bicycle"></i> Activité sportive associée
        </div>

        <div class="sport-grid">
          <?php foreach ($sports as $sport): ?>
          <div class="sport-option">
            <input type="radio" name="id_sport" id="sport_<?= $sport['id'] ?>" value="<?= $sport['id'] ?>">
            <label for="sport_<?= $sport['id'] ?>">
              <i class="bi bi-activity"></i>
              <div>
                <div class="sport-name"><?= esc($sport['libelle']) ?></div>
                <div class="sport-detail">
                  <?= $sport['variation_poids_seance'] > 0 ? '+' : '' ?><?= $sport['variation_poids_seance'] ?> kg/séance
                </div>
              </div>
            </label>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- SECTION 5 : Prix selon durée -->
      <div class="form-section">
        <div class="form-section-title">
          <i class="bi bi-tags-fill"></i> Prix selon la durée
        </div>

        <div class="prix-grid">
          <div class="prix-row">
            <div class="duree-badge">30 jours</div>
            <input type="number" name="prix_30" placeholder="Prix en Ar" min="0">
            <div class="unit">Ar</div>
          </div>
          <div class="prix-row">
            <div class="duree-badge">60 jours</div>
            <input type="number" name="prix_60" placeholder="Prix en Ar" min="0">
            <div class="unit">Ar</div>
          </div>
          <div class="prix-row">
            <div class="duree-badge">90 jours</div>
            <input type="number" name="prix_90" placeholder="Prix en Ar" min="0">
            <div class="unit">Ar</div>
          </div>
        </div>
      </div>

      <!-- ACTIONS -->
      <div class="form-actions">
        <a href="<?= base_url('admin/regimes') ?>" class="btn-cancel">
          <i class="bi bi-x-lg"></i> Annuler
        </a>
        <button type="submit" class="btn-submit" onclick="return prepareSubmit()">
          <i class="bi bi-check-circle-fill"></i> Enregistrer le régime
        </button>
      </div>

    </form>
  </div>
</div>

<script>
// Barre de prévisualisation des %
function updateBar() {
  const v = parseInt(document.getElementById('viande').value)   || 0;
  const o = parseInt(document.getElementById('volaille').value) || 0;
  const p = parseInt(document.getElementById('poisson').value)  || 0;
  const total = v + o + p;

  document.getElementById('barViande').style.width   = v + '%';
  document.getElementById('barVolaille').style.width = o + '%';
  document.getElementById('barPoisson').style.width  = p + '%';

  const el = document.getElementById('percentTotal');
  el.textContent = total + '%';
  el.className = 'percent-total ' + (total === 100 ? 'ok' : (total > 100 ? 'error' : ''));
}

// Préparer la variation avant submit
function prepareSubmit() {
  const sens   = document.querySelector('input[name="sens_variation"]:checked');
  const valeur = parseFloat(document.getElementById('variation_valeur').value);

  if (!sens) { alert('Veuillez choisir le sens de variation.'); return false; }
  if (!valeur || valeur <= 0) { alert('Veuillez entrer une valeur de variation valide.'); return false; }

  // Combiner sens + valeur → variation_poids_jour
  const variation = sens.value == '-1' ? -Math.abs(valeur) : Math.abs(valeur);
  document.getElementById('hidden_variation').value = variation;

  // Vérifier total %
  const v = parseInt(document.getElementById('viande').value)   || 0;
  const o = parseInt(document.getElementById('volaille').value) || 0;
  const p = parseInt(document.getElementById('poisson').value)  || 0;
  if (v + o + p !== 100) {
    alert('La somme viande + volaille + poisson doit être égale à 100%.');
    return false;
  }

  return true;
}
</script>

<?= $this->endSection() ?>