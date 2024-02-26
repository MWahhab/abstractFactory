<?php

require_once ("database/config.php");

if($argc < 2) {
    echo "USAGE: php removeTable.php <table_name>";
    exit();
}

/**
 * @var Factory $factory
 */
if($factory->removeTable($argv[1])) {
    echo "\nYou can now:
- Create a new table
- Clear more tables or their records
- Backup or merge tables
- Or more - find out how by running php plsHelpMe.php\n\n";
}

exit();