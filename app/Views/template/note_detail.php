
<h2><?= esc($cours['code_ue']) ?> — <?= esc($cours['intitule']) ?></h2>
<p>Étudiant : <strong><?= esc($etudiant['nom']) ?> <?= esc($etudiant['prenoms']) ?></strong></p>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<?php if (empty($notes)): ?>
    <p>Aucune note saisie pour cette matière.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Note</th>
                <th>Date de saisie</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($notes as $i => $n): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td>
                    <!-- Formulaire modification inline -->
                    <form action="<?= site_url('/note/update/' . $n['id']) ?>" method="POST">
                        <?= csrf_field() ?>
                        <input
                            type="number"
                            name="note"
                            value="<?= $n['note'] ?>"
                            min="0" max="20" step="0.25"
                            style="width:80px"
                        />
                        <button type="submit" class="btn btn-primary btn-sm">Modifier</button>
                    </form>
                </td>
                <td><?= $n['date_saisie'] ?></td>
                <td>
                    <a href="<?= site_url('/note/delete/' . $n['id']) ?>"
                       onclick="return confirm('Supprimer cette note ?')"
                       class="btn btn-danger btn-sm">
                        Supprimer
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p><strong>Note retenue (max) : <?= max(array_column($notes, 'note')) ?>/20</strong></p>
<?php endif; ?>

<a href="<?= site_url('/etudiants/' . $etudiant['id'] . '/notes') ?>" class="btn btn-secondary">
    ← Retour aux notes
</a>

<?= $this->endSection() ?>