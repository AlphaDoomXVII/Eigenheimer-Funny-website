-- Fase 1: basaal rechtensysteem
-- Elke rij is een aan/uit-schakelbare feature (bv. 'kamers', 'bestellen').
-- Ontbreekt een feature in deze tabel, dan behandelt FeatureModel::isEnabled()
-- die als standaard aan (fail-open), zie app/Shared/Rechten/Models/FeatureModel.php.

CREATE TABLE IF NOT EXISTS features (
    feature VARCHAR(50) NOT NULL PRIMARY KEY,
    enabled TINYINT(1) NOT NULL DEFAULT 1
);

INSERT INTO features (feature, enabled) VALUES
    ('bestellen', 1),
    ('kamers', 1)
ON DUPLICATE KEY UPDATE feature = feature;

-- Fase 2: kamerbeheer
CREATE TABLE IF NOT EXISTS kamers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    UUID VARCHAR(36) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(8,2) NOT NULL DEFAULT 0,
    photo_path VARCHAR(255) NOT NULL DEFAULT '',
    is_available TINYINT(1) NOT NULL DEFAULT 1
);
