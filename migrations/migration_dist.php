<?php

$argv[1] = 'some table';

include_once ("C:\\xampp\htdocs\abstractFactory\\removeTable.php");

// id is automated
$argv[2] = '[column_1 => integer, column_2 => boolean, name => string]';

include_once ("C:\\xampp\htdocs\abstractFactory\beginTableCreation.php");

$argv[2] = 50;

include_once("C:\\xampp\htdocs\abstractFactory\generateRows.php");