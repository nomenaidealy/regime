<?= $this->extend('layout') ?>

<?= $this->section('title') ?>NutriPlan — Connexion<?= $this->endSection() ?>

<?= $this->section('content') ?>

<link href="<?= base_url('assets/template/inscription.css') ?>" rel="stylesheet">   

<!-- LEFT PANEL -->
<div class="left-panel">
	<div class="logo">Nutri<span>Plan</span></div>

	<div class="left-content">
		<h2>Ravi de vous revoir !</h2>
		<p>Connectez-vous pour accéder à votre espace personnel et suivre votre programme alimentaire personnalisé.</p>

		<div class="steps-left" id="stepsLeft">
			<div class="step-ind active" data-step="1">
				<div class="step-dot">1</div>
				<div class="step-ind-text">
					<h4>Connexion</h4>
					<p>Email & mot de passe</p>
				</div>
			</div>
		</div>
	</div>

	<div class="left-bottom">
		Pas encore de compte ? <a href="/inscription">S'inscrire</a>
	</div>
</div>

<!-- RIGHT PANEL -->
<div class="right-panel">
		<form action="<?= base_url('login') ?>" method="POST" id="loginForm">
			<?= csrf_field() ?>
			<!-- progress dot unique -->
			<div class="progress-dots" id="progressDots">
				<div class="dot active"></div>
			</div>

			<div class="form-step active" id="step1">
				<div class="step-header">
					<div class="step-tag">Connexion</div>
					<h2>Connectez-vous</h2>
					<p>Entrez vos identifiants pour accéder à votre compte.</p>
				</div>

						<div class="field">
							<label>Adresse email</label>
							<input type="email" name="email" id="email" placeholder="Votre email" required>
							<div class="err-msg" id="emailError" style="display:none;color:#A32D2D;margin-top:5px;"></div>
							<?php if (session('errors.email')): ?>
								<div class="err-msg" style="color:#A32D2D;"><?= session('errors.email') ?></div>
							<?php endif; ?>
						</div>

				<div class="field">
					<label>Mot de passe</label>
					<div class="pwd-wrapper">
						<input type="password" name="mdp" id="mdp" placeholder="Votre mot de passe" required>
						<button class="pwd-toggle" type="button" onclick="togglePwd('mdp', this)"><i class="bi bi-eye"></i></button>
					</div>
					<?php if (session('errors.mdp')): ?>
						<div class="err-msg"><?= session('errors.mdp') ?></div>
					<?php endif; ?>
				</div>

				<?php if (session('login_error')): ?>
					<div class="err-msg" style="margin-bottom:10px;color:#A32D2D;"><?= session('login_error') ?></div>
				<?php endif; ?>

				<button class="btn-next" type="submit"><i class="bi bi-box-arrow-in-right"></i> Se connecter</button>
			</div>
		</form>
</div>

<script>
function togglePwd(id, btn) {
	const input = document.getElementById(id);
	if (input.type === 'password') { input.type = 'text'; btn.innerHTML = '<i class="bi bi-eye-slash"></i>'; }
	else { input.type = 'password'; btn.innerHTML = '<i class="bi bi-eye"></i>'; }
}

// Validation email côté client
document.getElementById('loginForm').addEventListener('submit', function(e) {
	const emailInput = document.getElementById('email');
	const emailError = document.getElementById('emailError');
	const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
	if (!emailRe.test(emailInput.value)) {
		emailError.textContent = 'Email invalide';
		emailError.style.display = 'block';
		e.preventDefault();
	} else {
		emailError.style.display = 'none';
	}
});

// Autocompletion @gmail.com après tabulation
document.getElementById('email').addEventListener('keydown', function(e) {
	if (e.key === 'Tab') {
		const val = this.value;
		if (val && !val.includes('@')) {
			this.value = val + '@gmail.com';
			// Laisse le focus passer au champ suivant après autocomplétion
			// On ne fait pas e.preventDefault() pour permettre la tabulation
			// Optionnel : sélectionner la partie "@gmail.com" pour modification rapide
			setTimeout(() => {
				this.setSelectionRange(val.length + 1, this.value.length);
			}, 0);
		}
	}
});
</script>

<?= $this->endSection() ?>
