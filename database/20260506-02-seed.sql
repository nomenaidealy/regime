USE diet;

-- Seeds for regimeAlimentaire

-- 1. SPORTS
INSERT INTO sport (libelle, description, variation_poids_seance) VALUES
('Course à pied', 'Course modérée à rapide', -0.20),
('Musculation', 'Séances de renforcement musculaire', +0.05),
('Natation', 'Activité complète', -0.15),
('Cyclisme', 'Sorties sur route ou VTT', -0.12),
('Yoga', 'Activités douces et étirements', -0.02);

-- 2. DIETS (régimes)
INSERT INTO diet (nom, description, variation_poids_jour, viande_percent, volaille_percent, poisson_percent, id_sport) VALUES
('Régime Perte Rapide', 'Régime hypocalorique pour perte rapide', -0.10, 40, 30, 30, 1),
('Régime Équilibré', 'Apport équilibré en protéines et lipides', -0.02, 33, 34, 33, 2),
('Régime Protéiné', 'Riche en protéines pour prise de masse', +0.03, 50, 25, 25, 2),
('Régime Méditerranéen', 'Basé sur poissons et légumes', -0.03, 20, 30, 50, 3),
('Régime Doux', 'Perte lente et durable', -0.01, 30, 40, 30, 5);

-- 3. PRIX SELON DURÉE
INSERT INTO diet_duree (id_diet, duree, prix) VALUES
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

-- 4. OBJECTIFS
INSERT INTO objectif (libelle) VALUES
('Augmenter son poids'),
('Réduire son poids'),
('Atteindre son IMC idéal');

-- 5. USERS (5 users) -- mdp are example hashed values (use password_hash in PHP)
INSERT INTO users (nom, email, genre, mdp, taille, poids, is_gold, date_achat_gold) VALUES
('Alice Martin', 'alice@example.com', 'Femme', '$2y$10$examplehashforalice1234567890', 1.65, 68.00, 0, NULL),
('Bob Dupont', 'bob@example.com', 'Homme', '$2y$10$examplehashforbob1234567890', 1.80, 85.00, 1, '2026-04-01 10:00:00'),
('Chloé Bernard', 'chloe@example.com', 'Femme', '$2y$10$examplehashforchloe123456', 1.60, 54.00, 0, NULL),
('David Moreau', 'david@example.com', 'Homme', '$2y$10$examplehashfordavid12345', 1.75, 77.50, 0, NULL),
('Eva Simon', 'eva@example.com', 'Femme', '$2y$10$examplehashforeva123456789', 1.70, 62.00, 1, '2026-03-15 12:30:00');

-- 6. WALLETS (one per user)
INSERT INTO wallet (id_user, solde) VALUES
(1, 0.00),
(2, 50.00),
(3, 10.00),
(4, 0.00),
(5, 100.00);

-- 7. MOUVEMENTS (some initial movements)
INSERT INTO mouvement (id_wallet, montant, type, montant_apres, description) VALUES
(2, 50.00, 'CREDIT', 50.00, 'Balance initiale'),
(3, 10.00, 'CREDIT', 10.00, 'Balance initiale'),
(5, 100.00, 'CREDIT', 100.00, 'Balance initiale');

-- 8. CODES PROMO (15 codes)
INSERT INTO code_promo (code, montant, est_utilise, id_user_utilise, date_utilisation) VALUES
('PROMO10', 10.00, 0, NULL, NULL),
('WELCOME5', 5.00, 0, NULL, NULL),
('SPRING15', 15.00, 0, NULL, NULL),
('GOLD20', 20.00, 0, NULL, NULL),
('SUMMER7', 7.00, 0, NULL, NULL),
('FREEMONTH', 30.00, 0, NULL, NULL),
('HALFOFF', 50.00, 0, NULL, NULL),
('SAVE3', 3.00, 0, NULL, NULL),
('GET10', 10.00, 0, NULL, NULL),
('TRIAL25', 25.00, 0, NULL, NULL),
('CODE1', 1.00, 0, NULL, NULL),
('CODE2', 2.00, 0, NULL, NULL),
('CODE3', 3.00, 0, NULL, NULL),
('CODE4', 4.00, 0, NULL, NULL),
('CODE5', 5.00, 0, NULL, NULL);

-- 9. USER_OBJECTIF (example selections)
INSERT INTO user_objectif (id_user, id_objectif) VALUES
(1, 2),
(2, 3),
(3, 1);

-- 10. USER_DIET (some subscriptions)
INSERT INTO user_diet (id_user, id_diet_duree, date_debut, prix_paye, remise_gold) VALUES
(2, 2, '2026-04-02', 42.49, 1),
(5, 1, '2026-03-16', 29.99, 1),
(1, 4, '2026-04-15', 24.99, 0);

-- End of seed
