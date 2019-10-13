<?php

namespace App\Processors;

use Illuminate\Support\Str;

class ApiPath
{
    /**
     * OpenAPI Version.
     *
     * @var string
     */
    protected $version;

    public function __construct($version = '1.0')
    {
        $this->version = $version;
    }

    public static function make($version = '1.0')
    {
        return new self($version);
    }

    /**
     * Get HTTP Method used.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Set HTTP Method.
     *
     * @param string $method
     */
    public function setMethod(string $method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get Tags.
     *
     * @return array
     */
    public function getTags(): string
    {
        return $this->tags;
    }

    /**
     * Set tags.
     *
     * @param string $tags
     */
    public function setTags(string $tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get Table Name.
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Set table name.
     *
     * @param string $table
     */
    public function setTable(string $table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Get schema name.
     *
     * @return string
     */
    public function getSchema(): string
    {
        return Str::studly($this->table);
    }

    /**
     * Set summary of the path.
     *
     * @param string $summary
     */
    public function setSummary(string $summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get path summary.
     *
     * @return string
     */
    public function getSummary(): string
    {
        return $this->summary;
    }

    /**
     * Set operation id of the path.
     *
     * @param string $operation_id
     */
    public function setOperationId(string $operation_id)
    {
        $this->operation_id = $operation_id;

        return $this;
    }

    /**
     * Get path operation ID.
     *
     * @return string
     */
    public function getOperationId(): string
    {
        return $this->operation_id;
    }

    public function generate()
    {
        $path = [
            $this->getMethod() => [
                'tags'        => explode(',', $this->getTags()),
                'summary'     => $this->getSummary(),
                'operationId' => $this->getOperationId(),
                'parameters'  => [
                    '$ref' => '#/components/parameters/Accept',
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Successful response.',
                        'content'     => [
                            'application/json' => [
                                'schema' => [
                                    'type'       => 'object',
                                    'properties' => [
                                        'meta' => [
                                            '$ref' => '#/components/schemas/Meta',
                                        ],
                                        'data' => [
                                            '$ref' => '#/components/schemas/' . $this->getSchema(),
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'default' => [
                        '$ref' => '#/components/responses/default',
                    ],
                ],
            ],
        ];

        try {
            $path = json_encode($path, JSON_PRETTY_PRINT);

            file_put_contents(
                $this->getFilePath(),
                $path
            );
        } catch (Exception $e) {
            throw new Exception('Unable to create path.', 1);
        }
    }

    /**
     * Get File Path of the Schema.
     *
     * @return string
     */
    public function getFilePath(): string
    {
        return oas_path(
            $this->version . '/paths/' . $this->getFileName() . '.json'
        );
    }

    /**
     * Get Filename of the schema.
     *
     * @return string
     */
    public function getFileName(): string
    {
        // Get total files under paths
        $count_path = count(
            glob(
                oas_path($this->version . '/paths/*.json')
            )
        ) + 1;

        // prefix with counter
        $prefix = str_pad($count_path, 4, '0', STR_PAD_LEFT);

        // 0001__welcome.json >> /welcome
        return $prefix . '__' . Str::snake($this->getTable());
    }
}
