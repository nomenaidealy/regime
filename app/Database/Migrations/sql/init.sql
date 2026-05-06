-- 1. CreateEtudiantsTable
CREATE TABLE etudiants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenoms VARCHAR(150) NOT NULL,
    date_naissance DATE NOT NULL,
    lieu_naissance VARCHAR(150) NOT NULL
);

-- 2. CreateParcoursTable
CREATE TABLE parcours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    responsable VARCHAR(150) NOT NULL
);

-- 3. CreateCoursTable
CREATE TABLE cours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code_ue VARCHAR(10) NOT NULL,
    intitule VARCHAR(150) NOT NULL,
    credits INT NOT NULL,
    semestre INT NOT NULL
);

-- 4. CreateInscriptionsTable
CREATE TABLE inscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    etudiant_id INT NOT NULL,
    niveau ENUM('L1', 'L2', 'L3', 'M1', 'M2') NOT NULL,
    annee_universitaire VARCHAR(10) NOT NULL,
    matricule VARCHAR(30) NOT NULL UNIQUE,
    FOREIGN KEY (etudiant_id) REFERENCES etudiants(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- 5. CreateParcoursCoursTable
CREATE TABLE parcours_cours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parcours_id INT NOT NULL,
    cours_id INT NOT NULL,
    est_optionnel BOOLEAN DEFAULT FALSE,
    groupe_option VARCHAR(50) NULL,
    FOREIGN KEY (parcours_id) REFERENCES parcours(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (cours_id) REFERENCES cours(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- 6. CreateNotesTable
CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    inscription_id INT NOT NULL,
    cours_id INT NOT NULL,
    note DECIMAL(4,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (inscription_id) REFERENCES inscriptions(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (cours_id) REFERENCES cours(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- 7. CreateUsersTable
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- 8. AlterNotesUpdatedAtTimestamp
ALTER TABLE notes MODIFY updated_at TIMESTAMP NULL DEFAULT NULL;