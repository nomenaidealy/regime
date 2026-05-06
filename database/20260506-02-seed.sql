USE diet;

-- =========================
-- 1. SPORTS
-- =========================
INSERT INTO sport (libelle, description, variation_poids_seance) VALUES
('Course à pied', 'Course modérée à rapide', -0.20),
('Musculation', 'Séances de renforcement musculaire', 0.05),
('Natation', 'Activité complète', -0.15),
('Cyclisme', 'Sorties sur route ou VTT', -0.12),
('Yoga', 'Activités douces et étirements', -0.02);

-- =========================
-- 2. DIETS
-- =========================
INSERT INTO diet (nom, description, variation_poids_jour, viande_percent, volaille_percent, poisson_percent, id_sport) VALUES
('Régime Perte Rapide', 'Régime hypocalorique pour perte rapide', -0.10, 40, 30, 30, 1),
('Régime Équilibré', 'Apport équilibré', -0.02, 33, 34, 33, 2),
('Régime Protéiné', 'Riche en protéines', 0.03, 50, 25, 25, 2),
('Régime Méditerranéen', 'Basé sur poissons et légumes', -0.03, 20, 30, 50, 3),
('Régime Doux', 'Perte lente', -0.01, 30, 40, 30, 5);

-- =========================
-- 3. PRIX
-- =========================
INSERT INTO diet_prix (id_diet, duree, prix) VALUES
(1, 30, 29.99),
(1, 60, 49.99),
(1, 90, 69.99),
(2, 30, 24.99),
(2, 60, 44.99),
(3, 30, 34.99),
(3, 90, 79.99),
(4, 30, 27.50),
(5, 30, 19.99),
(5, 60, 34.99);

-- =========================
-- 4. OBJECTIFS
-- =========================
INSERT INTO objectif (libelle) VALUES
('Augmenter son poids'),
('Réduire son poids'),
('Atteindre son IMC idéal');

-- =========================
-- 5. USERS
-- =========================
INSERT INTO users (nom, email, genre, mdp, taille, poids) VALUES
('Alice Martin', 'alice@example.com', 'Femme', '$2y$10$hash1', 1.65, 68.00),
('Bob Dupont', 'bob@example.com', 'Homme', '$2y$10$hash2', 1.80, 85.00),
('Chloé Bernard', 'chloe@example.com', 'Femme', '$2y$10$hash3', 1.60, 54.00),
('David Moreau', 'david@example.com', 'Homme', '$2y$10$hash4', 1.75, 77.50),
('Eva Simon', 'eva@example.com', 'Femme', '$2y$10$hash5', 1.70, 62.00);

-- =========================
-- 6. GOLD (historique)
-- =========================
INSERT INTO user_gold_at_time (id_user, date_achat_gold) VALUES
(2, '2026-04-01 10:00:00'),
(5, '2026-03-15 12:30:00');

-- =========================
-- 7. MOUVEMENTS
-- =========================
INSERT INTO mouvement (id_user, montant, type, montant_apres, description) VALUES
(2, 50.00, 'CREDIT', 50.00, 'Recharge initiale'),
(3, 10.00, 'CREDIT', 10.00, 'Recharge initiale'),
(5, 100.00, 'CREDIT', 100.00, 'Recharge initiale');

-- =========================
-- 8. CODES PROMO
-- =========================
INSERT INTO code_promo (code, montant) VALUES
('PROMO10', 10.00),
('WELCOME5', 5.00),
('SPRING15', 15.00),
('GOLD20', 20.00),
('SUMMER7', 7.00),
('FREEMONTH', 30.00),
('HALFOFF', 50.00),
('SAVE3', 3.00),
('GET10', 10.00),
('TRIAL25', 25.00),
('CODE1', 1.00),
('CODE2', 2.00),
('CODE3', 3.00),
('CODE4', 4.00),
('CODE5', 5.00);

-- =========================
-- 9. USER_OBJECTIF
-- =========================
INSERT INTO user_objectif (id_user, id_objectif, valeur_objectif) VALUES
(1, 2, 60.00),
(2, 3, 22.00),
(3, 1, 65.00);

-- =========================
-- 10. USER_DIET
-- =========================
INSERT INTO user_diet (id_user, id_diet_prix, date_debut, prix_paye, remise_gold) VALUES
(2, 2, '2026-04-02', 42.49, 1),
(5, 1, '2026-03-16', 29.99, 1),
(1, 4, '2026-04-15', 24.99, 0);