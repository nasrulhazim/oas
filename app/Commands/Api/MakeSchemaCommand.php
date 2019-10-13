<?php

namespace App\Commands\Api;

use Illuminate\Console\Command;

class MakeSchemaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:schema
                            {version : Version of OAS}
                            {table : Targeted table}
                            {--connection=sqlite : Database connection}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new OAS Schema';

    /**
     * Create a new controller creator command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connection = $this->option('connection');
        $table      = $this->argument('table');
        $version    = $this->argument('version');

        \App\Processors\ApiSchema::make($version)
            ->setConnection($connection)
            ->setTable($table)
            ->generate();

        $this->info('Successfully created schema.');
    }
}
