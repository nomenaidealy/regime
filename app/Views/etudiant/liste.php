<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des etudiants</title>
</head>
<body>
    <h1>Liste des etudiants</h1>

    <?php if (empty($etudiants)): ?>
        <p>Aucun etudiant trouve.</p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prenoms</th>
                    <th>Date de naissance</th>
                    <th>Lieu de naissance</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($etudiants as $etudiant): ?>
                    <tr>
                        <td><?= esc($etudiant['id']) ?></td>
                        <td><?= esc($etudiant['nom']) ?></td>
                        <td><?= esc($etudiant['prenoms']) ?></td>
                        <td><?= esc($etudiant['date_naissance']) ?></td>
                        <td><?= esc($etudiant['lieu_naissance']) ?></td>
                        <td><a href="<?= site_url('etudiant/notes/' . $etudiant['id']) ?>">Voir details</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
