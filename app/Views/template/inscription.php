<?= $this->extend('layout') ?>

<?= $this->section('title') ?>NutriPlan — Accueil<?= $this->endSection() ?>

<?= $this->section('content') ?>

<link href="<?= base_url('assets/template/inscription.css') ?>" rel="stylesheet">   

<!-- LEFT PANEL -->
<div class="left-panel">
  <div class="logo">Nutri<span>Plan</span></div>

  <div class="left-content">
    <h2>Votre voyage vers une <em>meilleure santé</em> commence ici</h2>
    <p>Créez votre compte en 3 étapes simples et obtenez un programme alimentaire personnalisé selon votre IMC et vos objectifs.</p>

    <div class="steps-left" id="stepsLeft">
      <div class="step-ind active" data-step="1">
        <div class="step-dot">1</div>
        <div class="step-ind-text">
          <h4>Informations personnelles</h4>
          <p>Nom, email, genre</p>
        </div>
      </div>
      <div class="step-ind" data-step="2">
        <div class="step-dot">2</div>
        <div class="step-ind-text">
          <h4>Informations de santé</h4>
          <p>Taille, poids & objectif</p>
        </div>
      </div>
      <div class="step-ind" data-step="3">
        <div class="step-dot">3</div>
        <div class="step-ind-text">
          <h4>Sécurité</h4>
          <p>Mot de passe</p>
        </div>
      </div>
    </div>
  </div>

  <div class="left-bottom">
    Déjà un compte ? <a href="/login">Se connecter</a>
  </div>
</div>

<!-- RIGHT PANEL -->
<div class="right-panel">

  <!-- progress dots -->
  <div class="progress-dots" id="progressDots">
    <div class="dot active"></div>
    <div class="dot inactive"></div>
    <div class="dot inactive"></div>
  </div>

  <!-- STEP 1 : Infos personnelles -->
  <div class="form-step active" id="step1">
    <div class="step-header">
      <div class="step-tag">Étape 1 sur 3</div>
      <h2>Informations personnelles</h2>
      <p>Dites-nous qui vous êtes pour personnaliser votre expérience.</p>
    </div>

    <div class="field">
      <label>Nom complet</label>
      <input type="text" id="nom" placeholder="Ex: Rasoa Rabe">
      <div class="err-msg">Veuillez entrer votre nom</div>
    </div>

    <div class="field">
      <label>Adresse email</label>
      <input type="email" id="email" placeholder="rasoa@gmail.com">
      <div class="err-msg">Email invalide</div>
    </div>

    <div class="field">
      <label>Genre</label>
      <div class="radio-group">
        <div class="radio-option">
          <input type="radio" name="genre" id="homme" value="Homme">
          <label for="homme"><i class="bi bi-gender-male"></i> Homme</label>
        </div>
        <div class="radio-option">
          <input type="radio" name="genre" id="femme" value="Femme">
          <label for="femme"><i class="bi bi-gender-female"></i> Femme</label>
        </div>
      </div>
    </div>

    <button class="btn-next" onclick="goTo(2)">Suivant <i class="bi bi-arrow-right"></i></button>
  </div>

  <!-- STEP 2 : Infos santé + objectif -->
  <div class="form-step" id="step2">
    <div class="step-header">
      <div class="step-tag">Étape 2 sur 3</div>
      <h2>Informations de santé</h2>
      <p>Ces données nous permettent de calculer votre IMC et vous suggérer le régime idéal.</p>
    </div>

    <div class="field-row">
      <div class="field">
        <label>Taille (en m)</label>
        <input type="number" id="taille" placeholder="Ex: 1.65" step="0.01" min="1" max="2.5">
        <div class="err-msg">Taille invalide</div>
      </div>
      <div class="field">
        <label>Poids (en kg)</label>
        <input type="number" id="poids" placeholder="Ex: 65" step="0.1" min="20" max="300">
        <div class="err-msg">Poids invalide</div>
      </div>
    </div>

    <!-- IMC preview -->
    <div id="imcPreview" style="display:none; background:var(--cream-dk); border-radius:var(--radius); padding:16px 20px; margin-bottom:20px; display:flex; align-items:center; justify-content:space-between;">
      <div>
        <div style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px;">Votre IMC estimé</div>
        <div id="imcVal" style="font-family:'Playfair Display',serif; font-size:32px; color:var(--green); font-weight:700;"></div>
      </div>
      <div id="imcStatus" style="font-size:13px; font-weight:500; color:var(--green-mid); text-align:right;"></div>
    </div>

    <div class="field">
      <label>Votre objectif</label>
      <div class="objectif-group">
        <div class="objectif-option">
          <input type="radio" name="objectif" id="obj1" value="1">
          <label for="obj1">
            <span class="objectif-icon"><i class="bi bi-graph-up-arrow"></i></span>
            <div class="objectif-text">
              <h4>Augmenter son poids</h4>
              <p>Gain de masse musculaire ou corporelle</p>
            </div>
          </label>
        </div>
        <div class="objectif-option">
          <input type="radio" name="objectif" id="obj2" value="2">
          <label for="obj2">
            <span class="objectif-icon">
              <i class="bi bi-graph-down-arrow"></i>
            </span>
            <div class="objectif-text">
              <h4>Réduire son poids</h4>
              <p>Perte de poids progressive et saine</p>
            </div>
          </label>
        </div>
        <div class="objectif-option">
          <input type="radio" name="objectif" id="obj3" value="3">
          <label for="obj3">
            <span class="objectif-icon"><i class="bi bi-scale"></i></span>
            <div class="objectif-text">
              <h4>Atteindre son IMC idéal</h4>
              <p>Équilibre et maintien de la santé</p>
            </div>
          </label>
        </div>
      </div>
    </div>

    <button class="btn-next" onclick="goTo(3)">Suivant <i class="bi bi-arrow-right"></i></button>
    <button class="btn-back" onclick="goTo(1)"><i class="bi bi-arrow-left"></i> Retour</button>
  </div>

  <!-- STEP 3 : Mot de passe -->
  <div class="form-step" id="step3">
    <div class="step-header">
      <div class="step-tag">Étape 3 sur 3</div>
      <h2>Sécurisez votre compte</h2>
      <p>Choisissez un mot de passe fort pour protéger votre compte.</p>
    </div>

    <div class="field">
      <label>Mot de passe</label>
      <div class="pwd-wrapper">
        <input type="password" id="mdp" placeholder="Minimum 6 caractères">
        <button class="pwd-toggle" onclick="togglePwd('mdp', this)"><i class="bi bi-eye"></i></button>
      </div>
      <div class="err-msg">Minimum 6 caractères</div>
    </div>

    <div class="field">
      <label>Confirmer le mot de passe</label>
      <div class="pwd-wrapper">
        <input type="password" id="mdpConfirm" placeholder="Répétez votre mot de passe">
        <button class="pwd-toggle" onclick="togglePwd('mdpConfirm', this)"><i class="bi bi-eye"></i></button>
      </div>
      <div class="err-msg">Les mots de passe ne correspondent pas</div>
    </div>

    <button class="btn-next" onclick="submit()"><i class="bi bi-balloon-heart"></i> Créer mon compte</button>
    <button class="btn-back" onclick="goTo(2)"><i class="bi bi-arrow-left"></i> Retour</button>
  </div>

  <!-- SUCCESS -->
  <div class="success-screen" id="successScreen">
    <div class="success-icon"><i class="bi bi-check-circle-fill"></i></div>
    <h2>Compte créé avec succès !</h2>
    <p>Bienvenue sur NutriPlan. Votre profil est prêt, découvrez les régimes adaptés à vos objectifs.</p>
    <a href="accueil.html" class="btn-go">Voir mon tableau de bord →</a>
  </div>

</div>

<script>
let currentStep = 1;

function goTo(step) {
  if (step > currentStep && !validate(currentStep)) return;

  document.getElementById('step' + currentStep).classList.remove('active');
  currentStep = step;
  document.getElementById('step' + currentStep).classList.add('active');

  updateProgress();
  updateLeftSteps();
}

function validate(step) {
  let ok = true;

  if (step === 1) {
    const nom   = document.getElementById('nom');
    const email = document.getElementById('email');
    const genre = document.querySelector('input[name="genre"]:checked');

    if (!nom.value.trim()) { nom.classList.add('error'); ok = false; }
    else nom.classList.remove('error');

    const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRe.test(email.value)) { email.classList.add('error'); ok = false; }
    else email.classList.remove('error');

    if (!genre) { alert('Veuillez sélectionner votre genre.'); ok = false; }
  }

  if (step === 2) {
    const taille = document.getElementById('taille');
    const poids  = document.getElementById('poids');
    const obj    = document.querySelector('input[name="objectif"]:checked');

    if (!taille.value || taille.value < 1 || taille.value > 2.5) { taille.classList.add('error'); ok = false; }
    else taille.classList.remove('error');

    if (!poids.value || poids.value < 20 || poids.value > 300) { poids.classList.add('error'); ok = false; }
    else poids.classList.remove('error');

    if (!obj) { alert('Veuillez choisir votre objectif.'); ok = false; }
  }

  if (step === 3) {
    const mdp        = document.getElementById('mdp');
    const mdpConfirm = document.getElementById('mdpConfirm');

    if (mdp.value.length < 6) { mdp.classList.add('error'); ok = false; }
    else mdp.classList.remove('error');

    if (mdp.value !== mdpConfirm.value) { mdpConfirm.classList.add('error'); ok = false; }
    else mdpConfirm.classList.remove('error');
  }

  return ok;
}

function updateProgress() {
  const dots = document.querySelectorAll('.dot');
  dots.forEach((d, i) => {
    d.className = 'dot';
    if (i + 1 === currentStep) d.classList.add('active');
    else if (i + 1 < currentStep) d.classList.add('done');
    else d.classList.add('inactive');
  });
}

function updateLeftSteps() {
  document.querySelectorAll('.step-ind').forEach(el => {
    const s = parseInt(el.dataset.step);
    el.className = 'step-ind';
    if (s === currentStep) el.classList.add('active');
    else if (s < currentStep) el.classList.add('done');

    const dot = el.querySelector('.step-dot');
    if (s < currentStep) dot.innerHTML = '✓';
    else dot.innerHTML = s;
  });
}

function submit() {
  if (!validate(3)) return;
  document.getElementById('step3').classList.remove('active');
  document.getElementById('successScreen').classList.add('active');
  document.getElementById('progressDots').style.display = 'none';
}

function togglePwd(id, btn) {
  const input = document.getElementById(id);
  if (input.type === 'password') { input.type = 'text'; btn.innerHTML = '<i class="bi bi-eye-slash"></i>'; }
  else { input.type = 'password'; btn.innerHTML = '<i class="bi bi-eye"></i>'; }
}

// IMC live preview
function calcIMC() {
  const t = parseFloat(document.getElementById('taille').value);
  const p = parseFloat(document.getElementById('poids').value);
  const preview = document.getElementById('imcPreview');

  if (t > 0.5 && t < 3 && p > 10 && p < 400) {
    const imc = (p / (t * t)).toFixed(1);
    document.getElementById('imcVal').textContent = imc;

    let status = '';if (imc < 18.5)      status = '<i class="bi bi-exclamation-triangle-fill" style="color:#BA7517"></i> Insuffisance pondérale';
    else if (imc < 25)   status = '<i class="bi bi-check-circle-fill" style="color:#2D6A4F"></i> Poids normal';
    else if (imc < 30)   status = '<i class="bi bi-exclamation-triangle-fill" style="color:#BA7517"></i> Surpoids';
    else                  status = '<i class="bi bi-x-circle-fill" style="color:#A32D2D"></i> Obésité';

    document.getElementById('imcStatus').innerHTML = status; // innerHTML au lieu de textContent !
    preview.style.display = 'flex';
  } else {
    preview.style.display = 'none';
  }
}

document.getElementById('taille').addEventListener('input', calcIMC);
document.getElementById('poids').addEventListener('input', calcIMC);
</script>

<?= $this->endSection() ?>