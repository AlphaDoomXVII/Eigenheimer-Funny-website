-- Gegenereerd door database/parse.php — niet handmatig bewerken.
-- Bron: database/xml/*.xml

CREATE TABLE IF NOT EXISTS features (
    feature VARCHAR(50) NOT NULL,
    enabled TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (feature)
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

INSERT INTO features (feature, enabled) VALUES ('bestellen', '1') ON DUPLICATE KEY UPDATE feature = feature;
INSERT INTO features (feature, enabled) VALUES ('kamers', '1') ON DUPLICATE KEY UPDATE feature = feature;

