<?php

namespace App\Commands\Api;

use LaravelZero\Framework\Commands\Command;

class MakePathCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'make:path
                            {version : Version of OAS}
                            {method : HTTP method used}
                            {tags : Schema tags}
                            {table : Targeted table in database}
                            {id : OAS Path Operation Id}
                            {--summary= : Summary of the path}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create a new OAS Path';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \App\Processors\ApiPath::make($this->argument('version'))
            ->setMethod($this->argument('method'))
            ->setTags($this->argument('tags'))
            ->setTable($this->argument('table'))
            ->setOperationId($this->argument('id'))
            ->setSummary($this->option('summary'))
            ->generate();

        $this->info('Successfully created path.');
    }
}
