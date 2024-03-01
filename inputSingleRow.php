<?php

require_once("database/config.php");

if (!isset($argv[1], $argv[2])) {
    echo "USAGE: PHP inputSingleRow.php <table_name> '<[value, value2, ...]>'\n";
    exit();
}

$className = str_replace(" ", "", ucwords($argv[1]));
$values    = explode(",", trim($argv[2], "[]"));
$values    = array_map('trim', $values);

require_once ("{$className}.php");

try{
    $instance = new $className(...$values);
} catch(Exception $e){
    die($e->getMessage());
}

/**
 * @var Factory $factory
 */
if ($factory->insertRows($argv[1], [$instance])) {
    echo "\nSuccess! Row has been generated and inserted into the {$argv[1]} table!
You can now:
- Insert more rows
- Clear entire tables or their records
- Backup or merge the table
- Or more - find out how by running php help.php\n\n";
}

//example use:
//php .\inputSingleRow.php 'table name' values-corresponding-to-constructor-params
