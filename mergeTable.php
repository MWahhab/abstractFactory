<?php

require_once ("database/config.php");

if($argc < 3) {
    echo "USAGE: php backupTable.php <table_name> <backup_name>";
    exit();
}

/**
 * @var Factory $factory
 */
if($factory->mergeBackup($argv[1], $argv[2])) {
    echo "\n{$argv[1]} successfully merged with table: {$argv[2]}!
You can now:
- Create a new table
- Clear tables or their records
- Backup or merge more tables
- Or more - find out how by running php plsHelpMe.php\n\n";

    exit();
}

echo "Failed to clear records from {$argv[1]}_backup";

exit();