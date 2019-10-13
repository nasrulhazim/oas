<?php

namespace App\Commands\Api;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class MakeTagCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'make:tag
                            {version : Version of OAS}
                            {name : Name of the tag}
                            {description : Description of the tag}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create a new OAS tag';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \App\Processors\ApiTag::make($this->argument('version'))
            ->setName($this->argument('name'))
            ->setDescription($this->argument('description'))
            ->generate();

        $this->info('Successfully created tag.');
    }

    /**
     * Define the command's schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
