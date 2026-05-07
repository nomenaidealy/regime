<?= $this->extend('layout') ?>

<?= $this->section('title') ?>NutriPlan — Code Promo<?= $this->endSection() ?>

<?= $this->section('content') ?>

<link href="<?= base_url('assets/template/regimeForm.css') ?>" rel="stylesheet">

<div class="regime-container">

  <div class="regime-left">
    <div class="logo">Nutri<span>Plan</span></div>
    <div>
      <h2>Utiliser un code promo</h2>
      <p>Entrez votre code pour créditer votre portefeuille.</p>
    </div>
    <div class="regime-left-bottom">
      <a href="<?= base_url('dashboard') ?>"><i class="bi bi-arrow-left"></i> Retour</a>
    </div>
  </div>

  <div class="regime-right">
    <div class="form-header">
      <div class="tag">Portefeuille</div>
      <h1>Code Promo</h1>
      <p>Saisissez votre code pour créditer votre compte instantanément.</p>
    </div>

    <form id="codePromoForm" style="max-width: 400px;">
      
      <div style="margin-bottom:18px;">
        <label style="display:block; margin-bottom:6px; font-weight:600;">Code promo</label>
        <input 
          type="text" 
          id="code"
          name="code" 
          placeholder="EX: PROMO2026" 
          style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; font-size:14px; text-transform:uppercase;"
          required
        >
        <small style="color:#999; display:block; margin-top:4px;">Le code est insensible à la casse</small>
      </div>

      <button 
        type="submit" 
        style="width:100%; padding:12px; background:#1a73e8; color:white; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; transition:0.2s;"
        onmouseover="this.style.background='#1557b0'"
        onmouseout="this.style.background='#1a73e8'"
      >
        Valider le code
      </button>

    </form>

    <div id="resultMessage" style="margin-top:20px;"></div>

  </div>

</div>

<script>
document.getElementById('codePromoForm').addEventListener('submit', async function(e) {
  e.preventDefault();

  const code = document.getElementById('code').value.trim();
  const resultDiv = document.getElementById('resultMessage');

  // Vider le message précédent
  resultDiv.innerHTML = '';

  if (!code) {
    resultDiv.innerHTML = '<div style="padding:12px; background:#fff3cd; border:1px solid #ffc107; border-radius:8px; color:#856404;">Veuillez saisir un code</div>';
    return;
  }

  try {
    const response = await fetch('<?= base_url('codepromo/redeem') ?>', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: 'code=' + encodeURIComponent(code)
    });

    const data = await response.json();

    if (data.success) {
      resultDiv.innerHTML = '<div style="padding:12px; background:#d4edda; border:1px solid #28a745; border-radius:8px; color:#155724;">' +
        '<strong>Succès!</strong> ' + data.message +
        '</div>';
      document.getElementById('code').value = '';
      setTimeout(() => {
        window.location.href = '<?= base_url('dashboard') ?>';
      }, 2000);
    } else {
      resultDiv.innerHTML = '<div style="padding:12px; background:#f8d7da; border:1px solid #f5c6cb; border-radius:8px; color:#721c24;">' +
        '<strong>Erreur</strong> ' + data.message +
        '</div>';
    }
  } catch (error) {
    resultDiv.innerHTML = '<div style="padding:12px; background:#f8d7da; border:1px solid #f5c6cb; border-radius:8px; color:#721c24;">' +
      '<strong>Erreur</strong> Une erreur est survenue' +
      '</div>';
    console.error('Error:', error);
  }
});
</script>

<?= $this->endSection() ?>
