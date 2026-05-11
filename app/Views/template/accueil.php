<?= $this->extend('layout') ?>

<?= $this->section('title') ?>NutriPlan — Accueil<?= $this->endSection() ?>
<?= $this->section('content') ?>
<!-- NAV -->

<style>
  .about {
    background: 
      linear-gradient(rgba(0,0,0,0.75), rgba(0,0,0,0.75)),
      url('<?= base_url("assets/images/jpeg") ?>') center/cover no-repeat;
  }
</style>
<!-- HERO -->


<section class="hero">
  <div class="hero-content">
    <div class="hero-text">
      <div class="hero-badge"><i class="bi bi-leaf"></i> Application de régime alimentaire personnalisé</div>
      <h1>Bienvenue sur <em>NutriPlan</em> — mangez mieux, vivez mieux</h1>
      <p>NutriPlan est une application intelligente qui calcule votre IMC, analyse vos objectifs et vous propose un programme alimentaire et sportif entièrement personnalisé.</p>
      <div class="hero-actions">
        <a href="inscription" class="btn-primary">Créer mon compte <i class="bi bi-arrow-right"></i></a>
        <a href="#about" class="btn-outline">En savoir plus</a>
      </div>
    </div>

    <div class="hero-image">
      <img src="<?= base_url('assets/images/diet.jpeg') ?>" alt="Alimentation saine NutriPlan" />
    </div>
  </div>

  <div class="scroll-hint"><span><i class="bi bi-arrow-down"></i></span>Découvrir</div>
</section>
<!-- À PROPOS -->
<section class="about" id="about">
  <div>
    <h2>Qu'est-ce que <em>NutriPlan</em> ?</h2>
    <p>NutriPlan est une plateforme numérique conçue pour accompagner chaque individu vers une alimentation saine et adaptée à son profil. En combinant les données de santé de l'utilisateur avec une base de régimes alimentaires soigneusement définis, l'application génère des recommandations précises, mesurables et accessibles à tous.</p>
    <br>
    <p>Notre système repose sur le calcul de l'<strong style="color:white">Indice de Masse Corporelle (IMC)</strong>, l'indicateur de référence mondial pour évaluer la corpulence et orienter les choix nutritionnels de manière fiable.</p>
  </div>
  <div class="about-cards">
    <div class="about-card">
      <div class="about-card-icon"><i class="bi bi-bar-chart-fill"></i></div>
      <div>
        <h4>Basé sur l'IMC</h4>
        <p>NutriPlan utilise votre taille et votre poids pour calculer votre IMC et vous orienter vers les régimes les mieux adaptés à votre situation.</p>
      </div>
    </div>
    <div class="about-card">
      <div class="about-card-icon"><i class="bi bi-basket2-fill"></i></div>
      <div>
        <h4>Régimes scientifiquement définis</h4>
        <p>Chaque régime intègre un équilibre précis en pourcentage de viande, volaille et poisson, associé à une variation de poids journalière mesurable.</p>
      </div>
    </div>
    <div class="about-card">
      <div class="about-card-icon"><i class="bi bi-person-walking"></i></div>
      <div>
        <h4>Activité sportive incluse</h4>
        <p>Chaque programme est accompagné d'une activité physique recommandée pour maximiser les résultats et maintenir un équilibre sain.</p>
      </div>
    </div>
  </div>
</section>

<!-- MISSION -->
<section class="mission" id="mission">
  <div class="mission-left">
    <div class="section-tag">Notre mission</div>
    <div class="section-title">Pourquoi NutriPlan existe ?</div>
    <div class="section-sub">Nous croyons que chaque personne mérite un accompagnement nutritionnel adapté à son corps, ses objectifs et son mode de vie.</div>
    <div class="mission-grid">
      <div class="mission-card">
        <div class="mission-icon"><i class="bi bi-bullseye"></i></div>
        <h3>Personnalisation totale</h3>
        <p>NutriPlan ne propose jamais un régime générique. Chaque suggestion est construite sur vos données réelles.</p>
      </div>
      <div class="mission-card">
        <div class="mission-icon"><i class="bi bi-graph-up-arrow"></i></div>
        <h3>Approche fondée sur les données</h3>
        <p>L'IMC est la référence scientifique mondiale. NutriPlan l'exploite pour vous orienter vers les programmes les plus pertinents.</p>
      </div>
      <div class="mission-card">
        <div class="mission-icon"><i class="bi bi-phone"></i></div>
        <h3>Simple et accessible à tous</h3>
        <p>Une interface claire, un processus guidé en 3 étapes, un résultat immédiat.</p>
      </div>
    </div>
  </div>

  <div class="mission-right">
    <img src="<?= base_url('assets/images/mission') ?>" alt="Mission NutriPlan" />
  </div>
</section>

<!-- FONCTIONNALITÉS -->
<section class="features" id="fonctionnalites">

  <!-- Images décoratives -->
  <img src="<?= base_url('assets/images/fonctionnalite.jpeg') ?>" class="feat-deco feat-deco-1" alt="">
  <img src="<?= base_url('assets/images/fonctionnalite.jpeg') ?>" class="feat-deco feat-deco-2" alt="">
  <img src="<?= base_url('assets/images/fonctionnalite.jpeg') ?>" class="feat-deco feat-deco-3" alt="">
  <img src="<?= base_url('assets/images/fonctionnalite.jpeg') ?>" class="feat-deco feat-deco-4" alt="">

  <div class="features-header">
    <div class="section-tag">Ce que propose NutriPlan</div>
    <div class="section-title">Toutes les fonctionnalités</div>
   

  <div class="features-grid">
    <div class="feat">
      <div class="feat-num">1</div>
      <div>
        <h4>Inscription guidée en 3 étapes</h4>
        <p>Un formulaire wizard clair : informations personnelles, données de santé, puis sécurisation du compte. Rapide et sans friction.</p>
      </div>
    </div>
    <div class="feat">
      <div class="feat-num">2</div>
      <div>
        <h4>Calcul et affichage de l'IMC</h4>
        <p>Dès la connexion, votre tableau de bord affiche votre IMC actuel, votre catégorie (normal, surpoids…) et l'objectif à atteindre.</p>
      </div>
    </div>
    <div class="feat">
      <div class="feat-num">3</div>
      <div>
        <h4>Suggestions de régimes adaptés</h4>
        <p>L'application sélectionne automatiquement les régimes compatibles avec votre objectif et les présente avec les durées et tarifs disponibles.</p>
      </div>
    </div>
    <div class="feat">
      <div class="feat-num">4</div>
      <div>
        <h4>Programme sportif associé</h4>
        <p>Chaque régime est couplé à une activité physique recommandée, avec estimation de la variation de poids par séance d'entraînement.</p>
      </div>
    </div>
    <div class="feat">
      <div class="feat-num">5</div>
      <div>
        <h4>Export du plan en PDF</h4>
        <p>Téléchargez votre programme complet — régime, activité, durée, objectif, résultats attendus — dans un fichier PDF propre et partageable.</p>
      </div>
    </div>
    <div class="feat">
      <div class="feat-num">6</div>
      <div>
        <h4>Portefeuille numérique intégré</h4>
        <p>Rechargez votre solde via des codes promo et payez vos régimes directement depuis l'application, sans carte bancaire externe.</p>
      </div>
    </div>
  </div>

</section>

<!-- GOLD -->
<section class="gold" id="gold">
  <div>
    <div class="gold-tag"><i class="bi bi-star-fill"></i> Option Gold</div>
    <h2>Passez à l'expérience <em>Gold</em></h2>
    <p>L'option Gold est un accès premium à paiement unique qui vous donne droit à une remise permanente de 15% sur l'ensemble des régimes disponibles sur NutriPlan.</p>
    <div class="gold-perks">
      <div class="gold-perk">15% de remise sur tous les régimes, définitivement</div>
      <div class="gold-perk">Paiement unique — aucun abonnement mensuel</div>
      <div class="gold-perk">Badge Gold affiché sur votre profil</div>
      <div class="gold-perk">Remise appliquée automatiquement à chaque achat</div>
      <div class="gold-perk">Accessible directement depuis votre portefeuille NutriPlan</div>
    </div>
  </div>
  <div>
    <div>
    <div class="gold-card">
      <div class="gc-label">Accès Premium</div>
      <div class="gc-title">Option Gold</div>
      <div class="gc-sub">Paiement unique — valable à vie</div>

      <div class="gc-price">
        <small>Ar </small><?= number_format($gold['prix'], 2, '.', ' ') ?>
    </div>
    <div class="gc-note">
        <?= ($gold['percent'] * 100) ?>% de remise permanente sur tous vos régimes
    </div>
      <a href="<?= session()->get('user_id') ? base_url('user/gold/activate') : base_url('login') ?>" class="btn-gold">
        Activer l'option Gold
      </a>
    </div>
</div>
  </div>
</section>

<!-- WALLET -->
<section class="wallet">
  <!-- Image décorative gauche -->
  <div class="wallet-image">
    <img src="<?= base_url('assets/images/wallet.jpeg') ?>" alt="Portefeuille NutriPlan" />
  </div>

  <div class="wallet-box">
    <div class="big-icon"><i class="bi bi-wallet2"></i></div>
    <h2>Le portefeuille NutriPlan</h2>
    <p>NutriPlan intègre un système de portefeuille numérique. Rechargez votre solde avec des codes promo et utilisez-le pour souscrire à vos régimes — sans carte bancaire, directement dans l'application.</p>
    <div class="wallet-steps">
      <div class="ws">
        <div class="ws-icon"><i class="bi bi-ticket-perforated"></i></div>
        <h4>Obtenez un code</h4>
        <p>Recevez un code promo depuis NutriPlan</p>
      </div>
      <div class="ws">
        <div class="ws-icon"><i class="bi bi-credit-card"></i></div>
        <h4>Rechargez</h4>
        <p>Saisissez le code dans votre espace personnel</p>
      </div>
      <div class="ws">
        <div class="ws-icon"><i class="bi bi-cart-check"></i></div>
        <h4>Payez</h4>
        <p>Utilisez votre solde pour souscrire à un régime</p>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta">
  <h2>Prêt à commencer avec NutriPlan ?</h2>
  <p>Rejoignez NutriPlan dès aujourd'hui et obtenez votre plan nutritionnel personnalisé en quelques minutes seulement.</p>
  <a href="/inscription" class="btn-cta">Créer mon compte gratuitement <i class="bi bi-arrow-right"></i></a>
</section>
<?= $this->endSection() ?>

</body>
</html>