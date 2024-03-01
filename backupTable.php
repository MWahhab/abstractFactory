<?php

require_once ("database/config.php");

if(!isset($argv[1], $argv[2])) {
    echo "USAGE: php backupTable.php <table_name> <backup_name>";
    exit();
}

/**
 * @var Factory $factory
 */
$factory->backupTable($argv[1], $argv[2]);
echo "\n{$argv[1]} successfully backed up to table: {$argv[2]}!
You can now:
- Create a new table
- Clear tables or their records
- Backup or merge more tables
- Or more - find out how by running php help.php\n\n";
