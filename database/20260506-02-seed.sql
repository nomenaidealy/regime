
-- 1. ADMIN
INSERT INTO admin (login, mdp) VALUES
('superadmin', 'admin2026'),
('moderateur', 'mod2026');

-- 2. OBJECTIFS
INSERT INTO objectif (libelle) VALUES
('Augmenter son poids'),
('Réduire son poids'),
('Atteindre son IMC idéal');

-- 3. SPORTS
INSERT INTO sport (libelle, description, variation_poids_seance) VALUES
('Course à pied', 'Course modérée à rapide',          -0.20),
('Musculation',   'Séances de renforcement musculaire', 0.05),
('Natation',      'Activité complète',                 -0.15),
('Cyclisme',      'Sorties sur route ou VTT',          -0.12),
('Yoga',          'Activités douces et étirements',    -0.02);

-- 4. DIETS
INSERT INTO diet (nom, description, variation_poids_jour,
    viande_percent, volaille_percent, poisson_percent, id_sport) VALUES
('Régime Perte Rapide',  'Régime hypocalorique pour perte rapide', -0.10, 40, 30, 30, 1),
('Régime Équilibré',     'Apport équilibré',                       -0.02, 33, 34, 33, 2),
('Régime Protéiné',      'Riche en protéines',                      0.03, 50, 25, 25, 2),
('Régime Méditerranéen', 'Basé sur poissons et légumes',           -0.03, 20, 30, 50, 3),
('Régime Doux',          'Perte lente et progressive',             -0.01, 30, 40, 30, 5);

-- 5. PRIX
INSERT INTO diet_prix (id_diet, duree, prix) VALUES
(1, 30, 119960), (1, 60, 199960), (1, 90, 279960),
(2, 30, 99960), (2, 60, 179960),
(3, 30, 139960), (3, 90, 319960),
(4, 30, 110000),
(5, 30, 79960), (5, 60, 139960);

-- 6. USERS
INSERT INTO users (nom, email, genre, mdp, taille, poids) VALUES
('Alice Martin',  'alice@example.com', 'Femme', 'alice123', 1.65, 68.00),
('Bob Dupont',    'bob@example.com',   'Homme', 'bob123',   1.80, 85.00),
('Chloé Bernard', 'chloe@example.com', 'Femme', 'chloe123', 1.60, 54.00),
('David Moreau',  'david@example.com', 'Homme', 'david123', 1.75, 77.50),
('Eva Simon',     'eva@example.com',   'Femme', 'eva123',   1.70, 62.00);


INSERT INTO gold(prix ,percent , date_update) VALUES 
(600000 ,0.15 , '2026-04-01 10:00:00') ;
-- 7. GOLD
INSERT INTO user_gold_at_time (id_user, id_gold, date_achat_gold) VALUES
(2, 1, '2026-04-01 10:00:00'),
(5, 1, '2026-03-15 12:30:00');

-- 8. MOUVEMENTS
-- Soldes initiaux
INSERT INTO mouvement (id_user, montant, type, montant_apres, description) VALUES
(1, 120000,  'CREDIT', 120000,  'Recharge initiale'),
(2, 200000,  'CREDIT', 200000,  'Recharge initiale'),
(3, 40000,   'CREDIT', 40000,   'Recharge initiale'),
(5, 400000,  'CREDIT', 400000,  'Recharge initiale');

-- Achats régimes (DEBIT)
INSERT INTO mouvement (id_user, montant, type, montant_apres, description) VALUES
(2, 169960, 'DEBIT',  30040,   'Souscription Régime Équilibré 60j (remise gold -15%)'),
(5, 119960, 'DEBIT',  280040,  'Souscription Régime Perte Rapide 30j'),
(1, 99960,  'DEBIT',  20040,   'Souscription Régime Équilibré 30j');

-- Crédit code promo validé (Chloé, après validation admin)
INSERT INTO mouvement (id_user, montant, type, montant_apres, description) VALUES
(3, 40000, 'CREDIT', 80000, 'Code promo PROMO10 validé par admin');

-- 9. CODES PROMO
INSERT INTO code_promo (code, montant) VALUES
('PROMO10',   40000),
('WELCOME5',  20000),
('SPRING15',  60000),
('GOLD20',    80000),
('SUMMER7',   28000),
('FREEMONTH', 120000),
('SAVE50',    200000),
('SAVE3',     12000),
('GET10',     40000),
('TRIAL25',   100000),
('CODE1',     4000),
('CODE2',     8000),
('CODE3',     12000),
('CODE4',     16000),
('CODE5',     20000);

-- 10. OBJECTIFS UTILISATEUR
INSERT INTO user_objectif (id_user, id_objectif, valeur_objectif) VALUES
(1, 2, 60.00),
(2, 3, 22.00),
(3, 1, 65.00);

-- 11. SOUSCRIPTIONS RÉGIME
INSERT INTO user_diet (id_user, id_diet_prix, date_debut, prix_paye, remise_gold) VALUES
(2, 2, '2026-04-02', 169960, 1),   -- Bob, gold, -15% sur 199960
(5, 1, '2026-03-16', 119960, 0),   -- Eva, gold mais pas de remise appliquée
(1, 4, '2026-04-15', 99960, 0);    -- Alice, sans remise

-- 12. DEMANDES CODE PROMO
-- Chloé (user 3) → PROMO10 → VALIDÉ
INSERT INTO demande_code_promo
    (id_user, id_code_promo, statut, date_traitement, id_admin)
VALUES (3, 1, 'VALIDE', '2026-04-10 09:00:00', 1);

-- David (user 4) → GOLD20 → REJETÉ
INSERT INTO demande_code_promo
    (id_user, id_code_promo, statut, date_traitement, id_admin, motif_rejet)
VALUES (4, 4, 'REJETE', '2026-04-11 14:00:00', 1,
        'Code réservé aux membres gold uniquement');

-- Alice (user 1) → WELCOME5 → EN ATTENTE
INSERT INTO demande_code_promo (id_user, id_code_promo, statut)
VALUES (1, 2, 'EN_ATTENTE');

-- 13. NOTIFICATIONS ADMIN
-- Notif pour la demande de Chloé (traitée, lue)
INSERT INTO notification_admin
    (id_admin, type, id_demande, message, lue, date_lecture)
VALUES (NULL, 'CODE_PROMO', 1,
        'Chloé Bernard a soumis le code PROMO10 (40000 Ar)',
        1, '2026-04-10 09:00:00');

-- Notif pour la demande de David (traitée, lue)
INSERT INTO notification_admin
    (id_admin, type, id_demande, message, lue, date_lecture)
VALUES (NULL, 'CODE_PROMO', 2,
        'David Moreau a soumis le code GOLD20 (80000 Ar)',
        1, '2026-04-11 14:00:00');

-- Notif pour la demande d'Alice (non traitée, non lue)
INSERT INTO notification_admin
    (id_admin, type, id_demande, message)
VALUES (NULL, 'CODE_PROMO', 3,
        'Alice Martin a soumis le code WELCOME5 (20000 Ar)');