<?php

class AbstractTable
{
    /**
     * @var string $tableName Refers to the name of the table
     */
    private string $tableName;
    /**
     * @var array $columns    Assoc array of the columns/type e.g: ["name" => "string", ...]
     */
    private array  $columns;

    /**
     * @param string $tableName Refers to the name of the table
     * @param array  $columns   Assoc array of the columns/type e.g: ["name" => "string", ...]
     *
     * Sets the tablename and columns properties upon instantiation
     */
    public function __construct(string $tableName, array $columns)
    {
        $this->tableName = $tableName;
        $this->columns   = $columns;
    }

    /**
     * @return string Retrieves the table name
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return array Retrieves all the columns
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param  array $values Assoc Array of values for each property e.g: ["propertyName" => value, ...]
     * @return void          Assigns values to properties created upon instantiation
     */
    public function assignValues(array $values): void
    {
        foreach ($values as $property => $value) {
            $this->{$property} = $value;

            if (!method_exists($this, 'get' . ucfirst($property))) {
                $this->{'get' . ucfirst($property)} = function () use ($property) {
                    return $this->{$property};
                };
            }
        }
    }

    /**
     * @param  string|int|float|bool $property Refers to the property being retrieved
     * @return string|int|float|bool           Returns the property for use
     * @throws Exception                       Throws error if property hasn't been defined
     *
     * Used by writing $instantiation->property
     */
    public function __get(string|int|float|bool $property): string|int|float|bool
    {
        if (property_exists($this, $property)) {
            return $this->{$property};
        }

        throw new Exception("Undefined property: $property");
    }

}