<?php

require_once ("database/config.php");

if(isset($argv[1], $argv[2])) {
    echo
    "USAGE: PHP beginTableCreation.php <table_name> '<[column_name => column_type, column_name2 => column_type2, ...]>'\n";
    echo "REMINDER: COLUMN_TYPE MUST BE EITHER:\n string \ninteger \nboolean \nfloat\nARRAY BRACKETS MUST BE WRAPPED IN ''\n";
} else {
    echo "No correct arguments found. Use help command to understand usage.";
    exit();
}

$tableName    = $argv[1];
$columnsInput = $argv[2];

$columnsString = substr($columnsInput, 1, -1);

$columnPairs = explode(',', $columnsString);

$columns = [];

foreach ($columnPairs as $pair) {
    $parts = explode('=>', $pair);
    $columnName = trim($parts[0]);
    $columnType = trim($parts[1]);
    $columns[$columnName] = $columnType;
}

/**
 * @var Factory $factory
 */
if($factory->createTable($tableName, $columns)) {
    echo "\nYou may now generate rows for this table, remove the table or attempt to merge it with an existing backup
Run php .\plsHelpMe.pph for help on how to do so!\n\n";
}

//example usage below:
//php .\beginTableCreation.php beyblade "[name => string, owner => string, type => string]"