<?php

class Factory
{
    /**
     * @var \database\Database $connection Refers to the database connection
     */
    private database\Database $connection;

    /**
     * @var int                $highestId  Refers to the highest ID currently in the table
     */
    private int               $highestId;

    /**
     * @param \database\Database $connection Refers to the database connection
     *
     * Upon instantiation, sets the database connection property
     */
    public function __construct(database\Database $connection)
    {
        $this->connection = $connection;
        $this->highestId  = 0;
    }

    /**
     * @param  string $tableName Refers to the table's name
     * @return bool              Runs a query to check whether a table exists or not.
     */
    private function checkTableExistence(string $tableName): bool
    {
        if(empty($this->connection->select($tableName))) {
            return false;
        }

        return true;
    }

    /**
     * @param  string $tableName Refers to the table's name
     * @param  array  $columns   Assoc Array referring to the columns and their type e.g: ["name" => "string"]
     * @return void              Runs a query to check whether a table exists or not. Creates the table if it doesn't
     */
    private function inputTable(string $tableName, array $columns): void
    {
        $columnClause = [];

        foreach ($columns as $columnName => $columnType) {
            switch ($columnType) {
                case("string"):
                    $columnClause[] = "{$columnName} VARCHAR(2000) NOT NULL";
                    break;
                case("integer"):
                    $columnClause[] = "{$columnName} INT NOT NULL";
                    break;
                case("float"):
                    $columnClause[] = "{$columnName} DOUBLE(10,2) NOT NULL";
                    break;
                case("boolean"):
                    $columnClause[] = "{$columnName} BOOLEAN NOT NULL";
                    break;
                default:
                    echo "The datatype for {$columnName} was neither string, integer, float or boolean! Cannot create table\n";
            }
        }

        $columnClauseStringified = implode(",", $columnClause);

        $query = "CREATE TABLE IF NOT EXISTS {$tableName}(
        id int AUTO_INCREMENT PRIMARY KEY,
        {$columnClauseStringified}
    )";

        $statement = $this->connection->getConnection()->prepare($query);
        echo $statement->execute() ?
            "\nSuccess! {$tableName} has been created!\n" : "Failure! {$tableName} has not been created!\n";
    }

    /**
     * @param  string $tableName Refers to the table's name
     * @param  array  $columns   Assoc Array referring to the columns and their type e.g: ["name" => "string"]
     * @return void              Generates a model for the new table in the form of a class
     */
    public function generateModel(string $tableName, array $columns): void
    {
        $className          = str_replace(" ", "", ucwords($tableName));
        $properties         = "";
        $setProperties      = "";
        $getters            = "";
        $params             = "";

        foreach ($columns as $column => $dataType) {
            switch($dataType) {
                case "string" || "float":
                    $properties .= "private {$dataType} \${$column}; ";
                    break;
                case "integer":
                    $properties .= "private int \$column; ";
                    break;
                case "boolean":
                    $properties .= "private bool \$column; ";
                    break;
                default:
                    die("Failed to add all properties to model");
            }

            $setProperties .= "\$this->{$column} = \${$column}; ";
            $params        .= "\${$column},";

            $dataType = $dataType == "integer" ? "int" : $dataType;
            $dataType = $dataType == "boolean" ? "bool" : $dataType;

            $getters       .= "public function get" .ucfirst($column) ."(): {$dataType}
            {
                return \$this->{$column};
            }
            ";

        }

        $classModel = "<?php" . " 
            class " . $className . "{
            private array \$columns;
            {$properties}
    
            public function __construct({$params})
            {
                 \$this->columns = " . var_export($columns, true) . ";
                 {$setProperties}
            }
           
            public function getColumns(): array
            {
                return \$this->columns;
            }
            {$getters}     

        }
        ";

        $newClassPath = "C:\\xampp\htdocs\abstractFactory\\{$className}.php";

        file_put_contents(
            $newClassPath,
            $classModel
        );
    }

    /**
     * @param  string $tableName Refers to the name of the table being created
     * @param  array  $columns   Refers to the columns of the table being created
     * @return bool
     */
    public function createTable(string $tableName, array $columns): bool
    {
        $allowedTypes = ["string", "boolean", "float", "integer"];

        foreach ($columns as $columnName => $columnType) {
            if (!in_array($columnType, $allowedTypes))
            {
                echo "\nThe datatype for {$columnName} was not string, boolean, float or integer! Cannot continue creation!\n";
                return false;
            }
        }

        if($this->checkTableExistence($tableName)) {
            echo "\n{$tableName} already exists! Cannot continue creation!\n\n";
            return false;
        }

        $this->inputTable($tableName, $columns);

        $this->generateModel($tableName, $columns);

        $rowToInsert = $this->generateRow($tableName, $columns);
        $this->insertRows($tableName, [$rowToInsert]);

        return true;
    }

    /**
     * @param  string $tableName Refers to the able that's having rows inserted into it
     * @param  array  $rows      Array of instantiated AbstractTable(s)
     * @return bool              Inserts rows into a table in the database
     */
    private function insertRows(string $tableName, array $rows):bool
    {
        $className = ucwords($tableName);

        /**
         * @var $className $row
         */
        foreach ($rows as $row) {
            $columns = array_keys($row->getColumns());
            $rowData = [];

            foreach ($columns as $column) {
                if($column == "id") {
                    continue;
                }

                $rowData[$column] = $row->{"get" . ucWords($column)}();
            }

            if(!$this->connection->insert($tableName, $rowData)) {
                echo "Failed to insert row!\n";
                return false;
            }
        }

        return true;

    }

    /**
     * @param  string $tableName Refers to the name of the table having rows inserted into it
     * @param  int    $quantity  Refers to the number of rows being generated
     * @return bool              Generates a set number of rows
     */
    public function generateRows(string $tableName, int $quantity): bool
    {
        if(!$this->checkTableExistence($tableName)) {
            echo "{$tableName} table doesnt exist! Cannot create row(s)! Go create the table first!\n";
            return false;
        }

        $columnsWithTypes = $this->connection->getColumnNamesWithMappedTypes($tableName);

        $this->refreshHighId($tableName);

        $rows = [];

        for($i=0;$i<$quantity;$i++){
            $row    = $this->generateRow($tableName, $columnsWithTypes);
            $rows[] = $row;
        }

        return $this->insertRows($tableName, $rows);
    }

    /**
     * @param  string        $tableName        Refers to the table a row is being generated for
     * @param  array         $columnsWithTypes Refers to the columns in the table and their data types
     */
    private function generateRow(string $tableName, array $columnsWithTypes)
    {
        $className      = ucwords($tableName);
        $columns        = array_keys($columnsWithTypes);
        $valuesToAssign = [];

        for($i=0;$i<count($columns);$i++) {
            $valuesToAssign[$columns[$i]] = $this->generateRowInfo($columns[$i], $columnsWithTypes[$columns[$i]]);
        }

        $this->setHighestId($this->getHighestId() + 1);

        require_once ("C:\\xampp\htdocs\abstractFactory\\{$className}.php");

        $row = new $className(...$valuesToAssign);

        return $row;
    }

    /**
     * @return void Sets the highestId property to the highest ID currently in the table
     */
    private function refreshHighId(string $tableName): void
    {
        $stmnt = $this->connection->getConnection()->prepare("SELECT MAX(id) AS highest_id FROM {$tableName}");
        $result = $stmnt->execute();

        !$result ? die("Issue retrieving highest ID") : $number = $stmnt->fetch(PDO::FETCH_ASSOC);

        if(!$number) {
            die("Issue fetching highest ID");
        }

        $this->highestId = $number["highest_id"] ?? 0;
    }

    /**
     * @return int Retrieves the highest ID currently in the table
     */
    private function getHighestId(): int
    {
        return $this->highestId;
    }

    /**
     * @param  int  $highestId Refers to the new highest ID
     * @return void            Sets a new highest ID
     */
    private function setHighestId(int $highestId): void
    {
        $this->highestId = $highestId;
    }

    /**
     * @param  string $detail   Refers to the beyblade detail being generated
     * @param  string $dataType Refers to the data type of the detail
     * @return string           Generates a random string based on the detail passed in
     */
    private function generateRowInfo(string $detail, string $dataType): mixed
    {
        $uniqueNum = $this->getHighestId() + 1;

        switch ($dataType) {
            case "string":
                $info = "{$detail} {$uniqueNum}";
                break;
            case "boolean":
                $info = rand(0,1);
                break;
            case "float":
                $randomNum = rand(0, 100)/100;
                $info = $uniqueNum + (float) number_format($randomNum,2 );
                break;
            case "tinyint":
                $info = (int) rand(0,1);
                break;
            case "integer":
                $info = (int)($uniqueNum + rand(1,1000));
                break;
            default:
                $info = "Failed to generate row info properly";
        }

        return $info;
    }

    /**
     * @param  string $table      Refers to the table being backed up
     * @param  string $backupName Refers to the name of the backup table being made
     * @return bool               Backs up a table by creating a new one
     */
    public function backupTable(string $table, string $backupName): bool
    {
        if($this->connection->backup($table, $backupName)) {
            return true;
        }

        return false;
    }

    /**
     * @param  string $table      Refers to the table being backed up
     * @param  string $backupName Refers to the name of the backup table being made
     * @return bool               Merges backup table with existing table
     */
    public function mergeBackup(string $table, string $backupName): bool
    {
        if(!$this->checkTableExistence($backupName)){
            echo "{$backupName} table doesnt exist! Cannot back it up!\n";
            return false;
        }

        if($this->connection->mergeTables($backupName, $table)) {
            return true;
        }

        return false;
    }

    /**
     * @param  string $table Refers to the name of the table
     * @return bool          Removes the table
     */
    public function removeTable(string $table):bool
    {
        echo !$this->connection->dropTable($table) ? "\nFailed to drop table: {$table}!\n\n" : "\n{$table} table dropped successfully!\n\n";

        return true;
    }

    /**
     * @param  string $table Refers to the table having it's records cleared
     * @return bool          Clears all the records from a table using truncate
     */
    public function clearRecords(string $table): bool
    {
        return $this->connection->truncateTable($table);
    }
}