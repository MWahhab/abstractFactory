<?php
echo
"
Navigate to either of the following scripts:\n
 - beginTableCreation.php   <table_name> <[column_name => column_type,...]>   to create a table
 - generateRows.php         <table_name> <quantity>                           to insert records into a database 
 - removeTable.php          <table_name>                                      to remove a table from the database
 - backupTable.php          <table_name> <backup_name>                        to backup a table
 - mergeBackup.php          <table_name> <backup_name>                        to replace a table with a backup\n
 - clearRecords.php         <table_name>                                      to clear a table's records\n
";

exit();