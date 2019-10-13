<?php

namespace App\Processors;

class ApiTag
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
     * Get name of the tag.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set name of the tag.
     *
     * @param string
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get description of the tag.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set name of the description.
     *
     * @param string
     */
    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }

    public function generate()
    {
        try {
            $content = json_encode([
                'name'        => $this->getName(),
                'description' => $this->getDescription(),
            ], JSON_PRETTY_PRINT);

            file_put_contents(
                $this->getFilePath(),
                $content
            );
        } catch (Exception $e) {
            throw new Exception('Unable to generate tag', 1);
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
            $this->version . '/tags/' . $this->getFileName() . '.json'
        );
    }

    /**
     * Get Filename of the schema.
     *
     * @return string
     */
    public function getFileName(): string
    {
        // Get total files under tags
        $count = count(
            glob(
                oas_path($this->version . '/tags/*.json')
            )
        );

        return str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
