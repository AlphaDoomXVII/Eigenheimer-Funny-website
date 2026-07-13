-- Gegenereerd door database/parse.php — niet handmatig bewerken.
-- Bron: database/xml/*.xml

CREATE TABLE IF NOT EXISTS bestellingen (
    id INT AUTO_INCREMENT,
    UUID VARCHAR(36) NOT NULL UNIQUE,
    klant_naam VARCHAR(100) NOT NULL DEFAULT '',
    items TEXT NOT NULL,
    totaal DECIMAL(8,2) NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'openstaand',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS features (
    feature VARCHAR(50) NOT NULL,
    enabled TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (feature)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS gebruikers (
    id INT AUTO_INCREMENT,
    UUID VARCHAR(36) NOT NULL UNIQUE,
    naam VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    wachtwoord_hash VARCHAR(255) NOT NULL,
    rol VARCHAR(20) NOT NULL DEFAULT 'gast',
    is_actief TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS kamers (
    id INT AUTO_INCREMENT,
    UUID VARCHAR(36) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(8,2) NOT NULL DEFAULT 0,
    photo_path VARCHAR(255) NOT NULL DEFAULT '',
    is_available TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_food (
    id INT AUTO_INCREMENT,
    UUID VARCHAR(36) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(8,2) NOT NULL DEFAULT 0,
    dagdeel VARCHAR(20) NOT NULL DEFAULT 'ontbijt',
    is_available TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

INSERT INTO features (feature, enabled) VALUES ('bestellen', '1') ON DUPLICATE KEY UPDATE feature = feature;
INSERT INTO features (feature, enabled) VALUES ('kamers', '1') ON DUPLICATE KEY UPDATE feature = feature;

INSERT INTO gebruikers (UUID, naam, email, wachtwoord_hash, rol, is_actief) VALUES ('00000000-0000-4000-8000-000000000001', 'Beheerder', 'admin@bbeigenheimer.nl', '$2y$12$noU4Z8guHxT3I3tyiMuCk.EIqkfB7G/EIa45i8ghYBTDJhU8PhEHO', 'admin', '1') ON DUPLICATE KEY UPDATE UUID = UUID;

