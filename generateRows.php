<?php

require_once ("database/config.php");

if($argc < 3) {
    echo "USAGE: PHP generateRows.php <table_name> </quantity>\n";
    exit();
}

/**
 * @var Factory $factory
 */

if($factory->generateRows($argv[1], $argv[2])) {
    echo "\nSuccess! {$argv[2]} unique rows have been generated and inserted into the {$argv[1]} table!
You can now:
- Insert more rows
- Clear entire tables or their records
- Backup or merge the table
- Or more - find out how by running php plsHelpMe.php\n\n";
}

exit();

//example use:
//php .\generateRows.php beyblade 5