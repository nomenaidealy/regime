<?= $this->extend('layout') ?>

<?= $this->section('title') ?>NutriPlan — Liste des régimes<?= $this->endSection() ?>

<?= $this->section('content') ?>

<link href="<?= base_url('assets/template/regimeForm.css') ?>" rel="stylesheet">">

<div class="regime-container">

	<div class="regime-left">
		<div class="logo">Nutri<span>Plan</span></div>
		<div>
			<h2>Gestion des <em>régimes</em></h2>
			<p>Visualisez, modifiez ou supprimez les régimes existants. Créez-en un nouveau si besoin.</p>
			<div class="info-cards">
				<div class="info-card">
					<i class="bi bi-list-ul"></i>
					<h4>Vue rapide</h4>
					<p>Consultez le nom, la composition et le prix principal.</p>
				</div>
				<div class="info-card">
					<i class="bi bi-pencil-square"></i>
					<h4>Actions</h4>
					<p>Modifier ou supprimer un régime en un clic.</p>
				</div>
			</div>
		</div>
		<div class="regime-left-bottom">
			<a href="<?= base_url('admin/dashboard') ?>"><i class="bi bi-arrow-left"></i> Retour</a>
		</div>
	</div>

	<div class="regime-right">
		<div class="form-header">
			<div class="tag">Back Office — Régimes</div>
			<h1>Liste des régimes</h1>
			<p>Gérez l'ensemble des régimes enregistrés.</p>
		</div>

		<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
			<div></div>
			<a href="<?= base_url('admin/regimes/create') ?>" class="btn-submit" style="display:inline-flex; align-items:center; gap:8px;">
				<i class="bi bi-plus-circle-fill"></i> Créer un régime
			</a>
		</div>

		<?php if (session()->getFlashdata('success')): ?>
			<div style="background:#E9F7EF; color:#145A32; padding:12px 16px; border-radius:10px; margin-bottom:16px;">
				<?= esc(session()->getFlashdata('success')) ?>
			</div>
		<?php endif; ?>

		<?php if (empty($regimes)): ?>
			<div style="background:#FFF7E6; color:#6A4F1D; padding:18px; border-radius:10px;">Aucun régime trouvé. Cliquez sur "Créer un régime" pour en ajouter un.</div>
		<?php else: ?>
			<div class="regime-table">
				<table style="width:100%; border-collapse:collapse;">
					<thead>
						<tr style="text-align:left; border-bottom:1px solid #e6e6e6;">
							<th style="padding:12px">Nom</th>
							<th style="padding:12px">Description</th>
							<th style="padding:12px">Composition</th>
							<th style="padding:12px">Prix (30j)</th>
							<th style="padding:12px">Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($regimes as $r): ?>
						<tr style="border-bottom:1px solid #f0f0f0;">
							<td style="padding:12px; vertical-align:top"><?= esc($r['nom'] ?? '') ?></td>
							<td style="padding:12px; vertical-align:top; max-width:360px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= esc($r['description'] ?? '') ?></td>
							<td style="padding:12px; vertical-align:top">
								<?php if (isset($r['viande_percent'])): ?>
									V: <?= esc($r['viande_percent']) ?>% • O: <?= esc($r['volaille_percent'] ?? 0) ?>% • P: <?= esc($r['poisson_percent'] ?? 0) ?>%
								<?php else: ?>
									—
								<?php endif; ?>
							</td>
							<td style="padding:12px; vertical-align:top"><?= isset($r['prix_30']) ? number_format($r['prix_30'], 0, ',', ' ') . ' Ar' : '—' ?></td>
							<td style="padding:12px; vertical-align:top">
								<a href="<?= base_url('admin/regimes/edit/' . ($r['id'] ?? '')) ?>" title="Modifier" style="margin-right:8px; color:#2D6A4F;"><i class="bi bi-pencil-square"></i></a>
								<a href="<?= base_url('admin/regimes/delete/' . ($r['id'] ?? '')) ?>" title="Supprimer" onclick="return confirm('Voulez-vous vraiment supprimer ce régime ?')" style="color:#A93226;"><i class="bi bi-trash-fill"></i></a>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endif; ?>

	</div>

</div>

<?= $this->endSection() ?>

