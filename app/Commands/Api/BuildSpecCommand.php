<?php

namespace App\Commands\Api;

use Illuminate\Console\Command;

class BuildSpecCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'build
                            {version : OpenAPI Specification version}
                            {--output= : Output path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build OAS';

    /**
     * Create a new controller creator command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $version = $this->argument('version');

        if (! file_exists(oas_path($version))) {
            $this->error('Version requested not exists.');

            return;
        }

        \App\Processors\ApiSpecification::make($version)
            ->loadSpec()
            ->export($this->getOutputPath());

        $this->info('OpenAPI Specification successfully built.');
    }

    private function getOutputPath()
    {
        if (! empty($this->option('output'))) {
            return $this->option('output');
        }

        return oas_path($version . '/' . $version . '.json');
    }
}
