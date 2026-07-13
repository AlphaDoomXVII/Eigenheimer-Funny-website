<?php

namespace App\Core;

/**
 * Zet database/xml/*.xml om naar SQL (CREATE TABLE IF NOT EXISTS + INSERT IGNORE-seeds).
 * Gebaseerd op hetzelfde patroon als ticketsystemVHE (https://github.com/hetgameboekje/ticketsystemVHE),
 * maar zonder de live-database-apply/dev-sync-kant daarvan — dit project heeft nog geen
 * Beheer-/login-scherm (dat komt pas in Fase 4/5), dus de gegenereerde SQL wordt alleen
 * weggeschreven en moet net als voorheen handmatig ingeladen worden.
 */
class SchemaParser
{
    /** @return array<string, \SimpleXMLElement> tabelnaam => geparste XML, in geen specifieke volgorde */
    private static function loadTables(): array
    {
        $tables = [];
        foreach (glob(APP_ROOT . '/database/xml/*.xml') as $file) {
            $xml = simplexml_load_file($file);
            if ($xml === false) {
                throw new \RuntimeException("Kan {$file} niet parsen als XML.");
            }
            $tables[(string) $xml['name']] = $xml;
        }

        return $tables;
    }

    /** @return string[] tabelnamen in dependency-volgorde (referenties vóór de tabellen die ernaar verwijzen) */
    private static function orderedTableNames(array $tables): array
    {
        $ordered = [];
        $visited = [];
        foreach (array_keys($tables) as $name) {
            self::visitTable($name, $tables, $ordered, $visited);
        }

        return $ordered;
    }

    public static function generateSql(): string
    {
        $tables = self::loadTables();
        $ordered = self::orderedTableNames($tables);

        $sql = "-- Gegenereerd door database/parse.php — niet handmatig bewerken.\n";
        $sql .= "-- Bron: database/xml/*.xml\n\n";

        foreach ($ordered as $name) {
            $sql .= self::buildCreateTable($tables[$name]) . "\n\n";
        }

        foreach ($ordered as $name) {
            $seedSql = self::buildSeed($tables[$name]);
            if ($seedSql !== '') {
                $sql .= $seedSql . "\n\n";
            }
        }

        return $sql;
    }

    public static function writeSchemaFile(string $sql): string
    {
        $path = APP_ROOT . '/database/schema.sql';
        file_put_contents($path, $sql);

        return $path;
    }

    private static function tableDependencies(\SimpleXMLElement $table): array
    {
        $deps = [];
        foreach ($table->columns->column as $column) {
            $ref = (string) $column['references'];
            if ($ref !== '') {
                $deps[] = explode('.', $ref)[0];
            }
        }
        return $deps;
    }

    private static function visitTable(string $name, array $tables, array &$ordered, array &$visited): void
    {
        if (isset($visited[$name]) || !isset($tables[$name])) {
            return;
        }
        $visited[$name] = true;
        foreach (self::tableDependencies($tables[$name]) as $dep) {
            if ($dep !== $name) {
                self::visitTable($dep, $tables, $ordered, $visited);
            }
        }
        $ordered[] = $name;
    }

    /** Bouwt de "kolomnaam TYPE(lengte) [modifiers]"-fragment. */
    private static function buildColumnDefinition(\SimpleXMLElement $column): string
    {
        $colName = (string) $column['name'];
        $type = (string) $column['type'];
        $length = (string) $column['length'];

        $line = "{$colName} {$type}" . ($length !== '' ? "({$length})" : '');

        if ((string) $column['auto_increment'] === 'true') {
            $line .= ' AUTO_INCREMENT';
        }
        if ((string) $column['nullable'] === 'false') {
            $line .= ' NOT NULL';
        }
        if ((string) $column['unique'] === 'true') {
            $line .= ' UNIQUE';
        }
        $default = (string) $column['default'];
        if ($default !== '') {
            $line .= " DEFAULT {$default}";
        } elseif ($type === 'TIMESTAMP' && (string) $column['nullable'] !== 'false') {
            // MySQL geeft een TIMESTAMP-kolom zonder expliciete default anders impliciet
            // NOT NULL DEFAULT '0000-00-00 00:00:00', wat onder strict mode een
            // "Invalid default value"-fout oplevert. Expliciet NULL voorkomt dat.
            $line .= ' NULL DEFAULT NULL';
        }
        $onUpdate = (string) $column['on_update'];
        if ($onUpdate !== '') {
            $line .= " ON UPDATE {$onUpdate}";
        }

        return $line;
    }

    private static function buildCreateTable(\SimpleXMLElement $table): string
    {
        $name = (string) $table['name'];
        $engine = (string) ($table['engine'] ?: 'InnoDB');

        $lines = [];
        $primary = null;
        $foreignKeys = [];

        foreach ($table->columns->column as $column) {
            $lines[] = '    ' . self::buildColumnDefinition($column);

            if ((string) $column['primary'] === 'true') {
                $primary = (string) $column['name'];
            }

            $ref = (string) $column['references'];
            if ($ref !== '') {
                [$refTable, $refCol] = explode('.', $ref);
                $fk = "    FOREIGN KEY (" . (string) $column['name'] . ") REFERENCES {$refTable}({$refCol})";
                $onDelete = (string) $column['on_delete'];
                if ($onDelete !== '') {
                    $fk .= " ON DELETE {$onDelete}";
                }
                $foreignKeys[] = $fk;
            }
        }

        if ($primary !== null) {
            $lines[] = "    PRIMARY KEY ({$primary})";
        }
        $lines = array_merge($lines, $foreignKeys);

        return "CREATE TABLE IF NOT EXISTS {$name} (\n" . implode(",\n", $lines) . "\n) ENGINE={$engine};";
    }

    private static function buildSeed(\SimpleXMLElement $table): string
    {
        if (!isset($table->seed) || !isset($table->seed->row)) {
            return '';
        }

        $name = (string) $table['name'];
        $statements = [];

        foreach ($table->seed->row as $row) {
            $columns = [];
            $values = [];
            foreach ($row->value as $value) {
                $columns[] = (string) $value['column'];
                $values[] = "'" . addslashes((string) $value) . "'";
            }
            $statements[] = "INSERT INTO {$name} (" . implode(', ', $columns) . ') VALUES (' . implode(', ', $values) . ') ON DUPLICATE KEY UPDATE ' . $columns[0] . ' = ' . $columns[0] . ';';
        }

        return implode("\n", $statements);
    }
}
