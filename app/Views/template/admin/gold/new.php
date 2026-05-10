<?= $this->extend('layout') ?>
<?= $this->section('title') ?>Admin — Paramètres Gold<?= $this->endSection() ?>
<?= $this->section('content') ?>

<link href="<?= base_url('assets/template/regimeForm.css') ?>" rel="stylesheet">

<div class="regime-container">

  <div class="regime-left">
    <div class="logo">Nutri<span>Plan</span></div>
    <div>
      <h2>Option Gold</h2>
      <p>Configurer le prix et la remise Gold.</p>
    </div>
    <div class="regime-left-bottom">
      <a href="<?= base_url('admin/gold/demandes') ?>"><i class="bi bi-arrow-left"></i> Voir les demandes Gold</a>
    </div>
  </div>

  <div class="regime-right">
    <div class="form-header">
      <div class="tag">Admin</div>
      <h1>Paramètres Gold</h1>
      <p>Ajoutez une nouvelle configuration pour le prix et la remise Gold.</p>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
      <div style="padding:12px; background:#d4edda; border:1px solid #28a745; border-radius:8px; color:#155724; margin-bottom:18px;">
        <strong>Succès</strong> <?= esc(session()->getFlashdata('success')) ?>
      </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
      <div style="padding:12px; background:#f8d7da; border:1px solid #f5c6cb; border-radius:8px; color:#721c24; margin-bottom:18px;">
        <strong>Erreur</strong> <?= esc(session()->getFlashdata('error')) ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($gold)): ?>
      <div style="background:#fff8e1; border:1px solid #f0c36d; color:#8a6d3b; border-radius:10px; padding:14px 16px; margin-bottom:18px;">
        <div style="font-weight:700; margin-bottom:6px;">
          Configuration actuelle
        </div>
        <div style="font-size:14px; line-height:1.6;">
          Prix : <strong><?= number_format((float) $gold['prix'], 2, ',', ' ') ?> Ar</strong><br>
          Remise : <strong><?= number_format(((float) $gold['percent']) * 100, 2, ',', ' ') ?>%</strong><br>
          Mise à jour : <strong><?= esc($gold['date_update'] ?? '—') ?></strong>
        </div>
      </div>
    <?php endif; ?>

    <form method="POST" action="<?= base_url('admin/gold/new') ?>" style="max-width: 520px;">
      <?= csrf_field() ?>

      <div style="margin-bottom:18px;">
        <label style="display:block; margin-bottom:6px; font-weight:600;">Prix Gold</label>
        <input
          type="number"
          name="prix"
          value="<?= old('prix') ?>"
          placeholder="ex: 150000"
          min="1"
          step="0.01"
          style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; font-size:14px;"
          required
        >
        <small style="color:#999; display:block; margin-top:4px;">Montant à débiter pour activer l'option Gold.</small>
      </div>

      <div style="margin-bottom:18px;">
        <label style="display:block; margin-bottom:6px; font-weight:600;">Remise Gold</label>
        <input
          type="number"
          name="percent"
          value="<?= old('percent') ?>"
          placeholder="ex: 0.15"
          min="0.01"
          max="0.99"
          step="0.01"
          style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; font-size:14px;"
          required
        >
        <small style="color:#999; display:block; margin-top:4px;">Saisir une valeur décimale entre 0 et 1, par exemple 0.15 pour 15%.</small>
      </div>

      <button
        type="submit"
        style="width:100%; padding:12px; background:#f0a500; color:#fff; border:none; border-radius:8px; font-size:14px; font-weight:700; cursor:pointer; transition:0.2s;"
        onmouseover="this.style.background='#d99200'"
        onmouseout="this.style.background='#f0a500'"
      >
        Enregistrer la configuration Gold
      </button>
    </form>
  </div>

</div>

<?= $this->endSection() ?>
