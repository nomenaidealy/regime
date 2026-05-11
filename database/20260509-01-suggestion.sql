-- Migration 2026-05-09-01
-- Ajoute la colonne `nombre_jour` à la table `user_diet`
-- La logique de normalisation (choix du tarif correspondant) est gérée
-- côté application : si l'utilisateur choisit un nombre de jours non
-- disponible, l'application choisira la durée maximale disponible.



-- 1) Ajouter la colonne nombre_jour (si elle n'existe pas)
ALTER TABLE user_diet
    ADD COLUMN  nombre_jour INT NOT NULL DEFAULT 30 ;

-- 2) NOTE (Pas de trigger ici car MySQL / politique sans trigger)
-- La migration n'installe pas de trigger. L'application doit gérer la logique suivante
-- au moment de l'insertion dans `user_diet` :
--  - si l'utilisateur fournit `nombre_jour`, rechercher dans `diet_prix` une ligne
--    avec `id_diet = (SELECT id_diet FROM diet_prix WHERE id = id_diet_prix)` et `duree = nombre_jour`;
--  - si introuvable, choisir la ligne avec la plus grande `duree` pour ce `id_diet` et utiliser son `id`;
--  - si `nombre_jour` absent ou <= 0, utiliser la `duree` du `id_diet_prix` fourni.
-- Le modèle côté application devra normaliser `nombre_jour` et sélectionner l'`id_diet_prix`
-- approprié avant d'insérer dans `user_diet`.

