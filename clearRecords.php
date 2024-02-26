<?php

require_once ("database/config.php");

if($argc < 2) {
    echo "USAGE: php clearRecords.php <table_nane>";
    exit();
}

/**
 * @var Factory $factory
 */
if($factory->clearRecords($argv[1])) {
    echo "\nSuccessfully cleared all records from {$argv[1]}
You can now:
- Create a new table
- Clear more tables or their records
- Backup or merge tables
- Or more - find out how by running php plsHelpMe.php\n\n";

    exit();
}

echo "Failed to clear records from {$argv[1]}";

exit();