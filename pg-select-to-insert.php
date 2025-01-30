#!/usr/bin/php
<?php
// This tool will take the output of a psql select statement which has the "extended" view (\x) - one row per column,
// and turn it into an insert statement. Pass the data in a text file.

function psqlToInsert($psqlOutput, $tableName) {
    $lines = explode("\n", trim($psqlOutput));
    $columns = [];
    $values = [];

    foreach ($lines as $line) {
        if (preg_match('/^([^|]+)\s*\|\s*(.*)$/', $line, $matches)) {
            $column = trim($matches[1]);
            $value = trim($matches[2]);

            $columns[] = $column;

            if ($value === "") {
                $values[] = "NULL";
            } elseif ($value === "t") {
                $values[] = "TRUE";
            } elseif ($value === "f") {
                $values[] = "FALSE";
            } elseif (preg_match('/^\{.*\}$/', $value)) { // Handle JSON-like values
                $values[] = "'" . json_encode(json_decode($value, true)) . "'";
            } else {
                $values[] = "'" . str_replace("'", "''", $value) . "'"; // Escape single quotes correctly
            }
        }
    }

    $sql = "INSERT INTO " . $tableName . " (
        " . implode(",\n        ", $columns) . "
    ) VALUES (
        " . implode(",\n        ", $values) . "
    );";

    return $sql;
}

if ($argc !== 3) {
    die("Usage: php script.php <filename> <table_name>\n");
}

$filename = $argv[1];
$tableName = $argv[2];

if (!file_exists($filename)) {
    die("Error: File not found: $filename\n");
}

$psqlOutput = file_get_contents($filename);
$sql = psqlToInsert($psqlOutput, $tableName);
echo $sql;
