CREATE DATABASE IF NOT EXISTS diet
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE diet;

-- ============================================================
--  TABLES
-- ============================================================

-- 1. ADMIN
CREATE TABLE admin (
    id    INT PRIMARY KEY AUTO_INCREMENT,
    login VARCHAR(100) NOT NULL UNIQUE,
    mdp   VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. OBJECTIF
CREATE TABLE objectif (
    id      INT PRIMARY KEY AUTO_INCREMENT,
    libelle VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. SPORT
CREATE TABLE sport (
    id                     INT PRIMARY KEY AUTO_INCREMENT,
    libelle                VARCHAR(100) NOT NULL,
    description            TEXT,
    variation_poids_seance DECIMAL(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. DIET
CREATE TABLE diet (
    id                   INT PRIMARY KEY AUTO_INCREMENT,
    nom                  VARCHAR(100) NOT NULL,
    description          TEXT,
    variation_poids_jour DECIMAL(5,2) NOT NULL,
    viande_percent       INT NOT NULL CHECK (viande_percent >= 0),
    volaille_percent     INT NOT NULL CHECK (volaille_percent >= 0),
    poisson_percent      INT NOT NULL CHECK (poisson_percent >= 0),
    id_sport             INT,
    FOREIGN KEY (id_sport) REFERENCES sport(id) ON DELETE SET NULL,
    CONSTRAINT chk_percent
        CHECK (viande_percent + volaille_percent + poisson_percent = 100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. PRIX DES RÉGIMES
CREATE TABLE diet_prix (
    id      INT PRIMARY KEY AUTO_INCREMENT,
    id_diet INT NOT NULL,
    duree   INT NOT NULL,
    prix    DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_diet) REFERENCES diet(id) ON DELETE CASCADE,
    UNIQUE (id_diet, duree)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. USERS
CREATE TABLE users (
    id               INT PRIMARY KEY AUTO_INCREMENT,
    nom              VARCHAR(100) NOT NULL,
    email            VARCHAR(150) NOT NULL UNIQUE,
    genre            ENUM('Homme','Femme') NOT NULL,
    mdp              VARCHAR(255) NOT NULL,
    taille           DECIMAL(5,2) NOT NULL,
    poids            DECIMAL(5,2) NOT NULL,
    date_inscription DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE gold (
    id INT PRIMARY KEY AUTO_INCREMENT,
    prix DECIMAL(10,2) NOT NULL,
    percent DECIMAL(3,2) NOT NULL,
    date_update DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. GOLD USER
CREATE TABLE user_gold_at_time (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    id_user         INT NOT NULL,
    id_gold         INT NOT NULL,
    date_achat_gold DATETIME DEFAULT NULL,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_gold) REFERENCES gold(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. MOUVEMENT
CREATE TABLE mouvement (
    id             INT PRIMARY KEY AUTO_INCREMENT,
    id_user        INT NOT NULL,
    montant        DECIMAL(10,2) NOT NULL,
    type           ENUM('CREDIT','DEBIT') NOT NULL,
    montant_apres  DECIMAL(10,2) NOT NULL,
    description    VARCHAR(255),
    date_mouvement DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. CODE PROMO (nettoyée : plus de id_user_utilise / date_utilisation)
CREATE TABLE code_promo (
    id      INT PRIMARY KEY AUTO_INCREMENT,
    code    VARCHAR(50) NOT NULL UNIQUE,
    montant DECIMAL(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. OBJECTIF UTILISATEUR
CREATE TABLE user_objectif (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    id_user         INT NOT NULL,
    id_objectif     INT NOT NULL,
    date_choix      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    valeur_objectif DECIMAL(5,2) NOT NULL,
    FOREIGN KEY (id_user)    REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY (id_objectif) REFERENCES objectif(id) ON DELETE CASCADE,
    UNIQUE (id_user, id_objectif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. SOUSCRIPTION RÉGIME
CREATE TABLE user_diet (
    id           INT PRIMARY KEY AUTO_INCREMENT,
    id_user      INT NOT NULL,
    id_diet_prix INT NOT NULL,
    date_debut   DATE NOT NULL,
    prix_paye    DECIMAL(10,2) NOT NULL,
    remise_gold  TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (id_user)      REFERENCES users(id)      ON DELETE CASCADE,
    FOREIGN KEY (id_diet_prix) REFERENCES diet_prix(id)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. DEMANDE CODE PROMO (nouvelle)
CREATE TABLE demande_code_promo (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    id_user         INT NOT NULL,
    id_code_promo   INT NOT NULL,
    statut          ENUM('EN_ATTENTE','VALIDE','REJETE') NOT NULL DEFAULT 'EN_ATTENTE',
    date_demande    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_traitement DATETIME DEFAULT NULL,
    id_admin        INT DEFAULT NULL,
    motif_rejet     VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (id_user)       REFERENCES users(id)      ON DELETE CASCADE,
    FOREIGN KEY (id_code_promo) REFERENCES code_promo(id) ON DELETE CASCADE,
    FOREIGN KEY (id_admin)      REFERENCES admin(id)      ON DELETE SET NULL,
    UNIQUE (id_user, id_code_promo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE demande_gold (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    id_user         INT NOT NULL,
    statut          ENUM('EN_ATTENTE','VALIDE','REJETE') NOT NULL DEFAULT 'EN_ATTENTE',
    date_demande    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_traitement DATETIME DEFAULT NULL,
    id_admin        INT DEFAULT NULL,
    motif_rejet     VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (id_user)  REFERENCES users(id)  ON DELETE CASCADE,
    FOREIGN KEY (id_admin) REFERENCES admin(id)  ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. NOTIFICATION ADMIN (nouvelle)
CREATE TABLE notification_admin (
    id            INT PRIMARY KEY AUTO_INCREMENT,
    id_admin      INT DEFAULT NULL,
    type          ENUM('CODE_PROMO','DEMANDE_GOLD') NOT NULL,
    id_demande    INT NOT NULL,   -- ID dans la table correspondante au type
    message       VARCHAR(255) NOT NULL,
    lue           TINYINT(1) NOT NULL DEFAULT 0,
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_lecture  DATETIME DEFAULT NULL,

    FOREIGN KEY (id_admin) REFERENCES admin(id) ON DELETE SET NULL
    -- Pas de FK sur demande_id → géré applicativement selon `type`
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  DONNÉES
-- ============================================================
