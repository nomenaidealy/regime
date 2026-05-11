<?= $this->extend('layout') ?>

<?= $this->section('title') ?>NutriPlan — Code promo<?= $this->endSection() ?>

<?= $this->section('content') ?>

<link href="<?= base_url('assets/template/regimeForm.css') ?>" rel="stylesheet">

<div class="regime-container">
  <div class="regime-left">
    <div class="logo">Nutri<span>Plan</span></div>
    <div>
      <h2>Créditer votre solde</h2>
      <p>Saisissez un code promo valide pour recharger votre portefeuille NutriPlan.</p>
    </div>
    <div class="regime-left-bottom">
      <a href="<?= base_url('profil') ?>"><i class="bi bi-arrow-left"></i> Retour au profil</a>
    </div>
  </div>

  <div class="regime-right">
    <div class="form-header">
      <div class="tag">Portefeuille</div>
      <h1>Code promo</h1>
      <p>Entrez votre code puis validez pour envoyer une demande de crédit.</p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
      <div style="background:#fdecea; color:#a94442; border:1px solid #f5c6cb; padding:12px 14px; border-radius:10px; margin-bottom:14px; font-weight:600;">
        <?= esc(session()->getFlashdata('error')) ?>
      </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
      <div style="background:#e9f7ef; color:#145a32; border:1px solid #c3e6cb; padding:12px 14px; border-radius:10px; margin-bottom:14px; font-weight:600;">
        <?= esc(session()->getFlashdata('success')) ?>
      </div>
    <?php endif; ?>

    <div style="background:#fff; padding:18px; border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,.06);">
      <form id="codePromoForm" method="POST" action="<?= base_url('codepromo/redeem') ?>">
        <?= csrf_field() ?>

        <div class="field" style="margin-bottom:14px;">
          <label for="code">Code promo</label>
          <input type="text" id="code" name="code" placeholder="Ex: ABCD1234" autocomplete="off" required>
        </div>

        <div id="formMsg" style="display:none; margin-bottom:14px; padding:12px 14px; border-radius:10px; font-weight:600;"></div>

        <button class="btn-next" type="submit" style="width:100%; justify-content:center;">
          <i class="bi bi-ticket-perforated"></i> Envoyer la demande
        </button>
      </form>
    </div>
  </div>
</div>

<script>
const form = document.getElementById('codePromoForm');
const msg  = document.getElementById('formMsg');

function showMsg(text, ok) {
  msg.textContent = text;
  msg.style.display = 'block';
  msg.style.background = ok ? '#e9f7ef' : '#fdecea';
  msg.style.color = ok ? '#145a32' : '#a94442';
  msg.style.border = ok ? '1px solid #c3e6cb' : '1px solid #f5c6cb';
}

form.addEventListener('submit', async (e) => {
  e.preventDefault();

  const code = document.getElementById('code').value.trim();
  if (!code) {
    showMsg('Veuillez saisir un code promo.', false);
    return;
  }

  const data = new FormData(form);

  try {
    const res = await fetch(form.action, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: data
    });

    const json = await res.json();

    if (json.success) {
      showMsg(json.message || 'Demande envoyée.', true);
      form.reset();
    } else {
      showMsg(json.message || 'Une erreur est survenue.', false);
    }
  } catch (error) {
    showMsg('Impossible de contacter le serveur.', false);
  }
});
</script>

<?= $this->endSection() ?>