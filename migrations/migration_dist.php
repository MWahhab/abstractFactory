<?php

$argv[1] = 'table_name';

include_once ('removeTable.php');

// id is automated
$argv[2] = '[column_1 => integer, column_2 => boolean, name => string]';

include_once ('beginTableCreation.php');

$argv[2] = 50;

include_once('generateRows.php');