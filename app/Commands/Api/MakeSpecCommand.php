<?php

namespace App\Commands\Api;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeSpecCommand extends Command
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:oas
                             {version : Version of OAS}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new OAS';

    /**
     * Create a new controller creator command instance.
     *
     * @param \Illuminate\Filesystem\Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $version = $this->argument('version');
        $path    = oas_path($version);
        if ($this->files->exists($path)) {
            $this->error('OpenAPI Specification version ' . $version . ' already exists.');

            return;
        }

        $this->files->copyDirectory(__DIR__ . '/stubs', $path);
        $this->info('Successfully created specification.');
    }
}
