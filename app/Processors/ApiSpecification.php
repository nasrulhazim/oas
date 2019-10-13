<?php

namespace App\Processors;

class ApiSpecification
{
    /**
     * OpenAPI Version.
     *
     * @var string
     */
    protected $version;

    /**
     * OpenAPI Specifications.
     *
     * @var array
     */
    protected $specification = [];

    public function __construct($version = '1.0')
    {
        $this->version = $version;
    }

    /**
     * Create Api Specification Instance.
     *
     * @param string $version
     *
     * @return self
     */
    public static function make($version = '1.0')
    {
        return new self($version);
    }

    /**
     * Get generated specification.
     *
     * @return array
     */
    public function getSpecification(): array
    {
        return $this->specification;
    }

    /**
     * Get specification version.
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Export specification to given path in json format.
     *
     * @param string $path
     *
     * @return self
     */
    public function export($path)
    {
        file_put_contents(
            $path,
            json_encode(
                $this->getSpecification()
            )
        );

        return $this;
    }

    /**
     *  Load specfication.
     *
     * @return self
     */
    public function loadSpec()
    {
        $data               = (array) $this->loadContent(oas_path($this->getVersion() . '/openapi.json'));
        $data['info']       = $this->loadContent(oas_path($this->getVersion() . '/info.json'));
        $data['servers']    = $this->loadContent(oas_path($this->getVersion() . '/servers.json'));
        $data['components'] = [
            'parameters' => $this->buildContent('/components/parameters/*.json'),
            'responses'  => $this->buildContent('/components/responses/*.json'),
            'schemas'    => $this->buildContent('/components/schemas/*.json'),
        ];
        $data['paths'] = (object) $this->buildContent('/paths/*.json', true, true);
        $data['tags']  = $this->buildContent('/tags/*.json', false);

        $this->specification = $data;

        return $this;
    }

    /**
     * Build content based on given paths.
     *
     * @param string $path
     *
     * @return array
     */
    public function buildContent($path, $useFilenameAsKey = true, $removeOrdering = false)
    {
        $paths = glob(oas_path($this->getVersion() . $path));
        $data  = [];
        foreach ($paths as $path) {
            // remove .json extension
            $filename = str_replace('.json', '', basename($path));
            // if the name has ordering formatting
            if ($removeOrdering) {
                $filename = substr($filename, 4);
            }

            // regenerate back the URI format __welcome to /welcome
            $filename = str_replace('__', '/', $filename);

            if ($useFilenameAsKey) {
                $data[$filename] = $this->loadContent($path);
            } else {
                $data[] = $this->loadContent($path);
            }
        }

        return $data;
    }

    /**
     * Load content of the given file path.
     *
     * @param string $path
     *
     * @return mix
     */
    public function loadContent($path)
    {
        return json_decode(
            file_get_contents($path)
        );
    }
}
