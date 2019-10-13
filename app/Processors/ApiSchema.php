<?php

namespace App\Processors;

use DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ApiSchema
{
    /**
     * OpenAPI Version.
     *
     * @var string
     */
    protected $version;

    /**
     * Database connection name.
     *
     * @var string
     */
    protected $connection;

    /**
     * Table fields details.
     *
     * @var mix
     */
    protected $columns;

    /**
     * Fields to be filter from Schema.
     *
     * @var array
     */
    protected $filter_columns = [
        'id', 'created_at', 'updated_at',
    ];

    public function __construct($version = '1.0')
    {
        $this->version = $version;
    }

    public static function make($version = '1.0')
    {
        return new self($version);
    }

    public function setConnection(string $name)
    {
        $this->connection = $name;

        return $this;
    }

    public function getConnection(): string
    {
        return $this->connection;
    }

    public function setTable(string $table)
    {
        $this->table = $table;

        return $this;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function generate()
    {
        try {
            $required   = [];
            $properties = [];

            $columns = $this->filterColumns(
                collect(
                    Schema::getColumnListing($this->getTable())
                )
            );

            foreach ($columns as $column) {
                $required[]                         = $column;
                $properties[$column]                = $this->castDataType($column);
                $properties[$column]['description'] = $this->getDescription($column);
            }

            $format = [
                'type'       => 'object',
                'required'   => $required,
                'properties' => $properties,
            ];

            $schema = json_encode($format, JSON_PRETTY_PRINT);

            file_put_contents(
                $this->getFilePath(),
                $schema
            );
        } catch (Exception $e) {
            throw new Exception('Unable to create schema.', 1);
        }
    }

    /**
     * Set Column Details if not yet set.
     */
    public function setColumnDetails()
    {
        if (empty($this->columns)) {
            $this->columns = DB::connection($this->getConnection())
                ->getDoctrineSchemaManager()
                ->listTableDetails($this->getTable());
        }

        return $this;
    }

    /**
     * Get Column Details.
     *
     * @return string list of column details
     */
    public function getColumnDetails()
    {
        $this->setColumnDetails();

        return $this->columns;
    }

    /**
     * Filter database columns that don't want to be expose to OAS.
     *
     * @param \Illuminate\Support\Collection $columns collection of table columns
     *
     * @return \Illuminate\Support\Collection filtered columns collection
     */
    public function filterColumns(Collection $columns): Collection
    {
        return $columns->diff(
            $this->filter_columns
        );
    }

    /**
     * Get database column data type.
     *
     * @param string $column database column name
     *
     * @return string return database column data type
     */
    public function getColumnDataType($column)
    {
        return Schema::getColumnType(
            $this->getTable(),
            $column
        );
    }

    /**
     * Cast database data type to OpenAPI Specification data type format.
     *
     * @param string $type data type of the database column
     *
     * @return string openAPI Specification data type
     */
    public function castDataType($type)
    {
        return ApiDataType::get($type);
    }

    /**
     * Get Column Description.
     *
     * @param string $column Column name
     *
     * @return string return column's comment if any
     */
    public function getDescription(string $column): string
    {
        $comment = $this
            ->getColumnDetails()
            ->getColumn($column)
            ->getComment();

        return  ! empty($comment)
            ? $comment : 'No description available.';
    }

    /**
     * Get File Path of the Schema.
     *
     * @return string
     */
    public function getFilePath(): string
    {
        return oas_path(
            $this->version . '/components/schemas/' . $this->getFileName() . '.json'
        );
    }

    /**
     * Get Filename of the schema.
     *
     * @return string
     */
    public function getFileName(): string
    {
        return Str::studly(
            $this->getTable()
        );
    }
}
