<?php

use Illuminate\Console\Command;
use NHL\DataCollector\Consumers\ScheduleDatabaseConsumer;
use NHL\DataCollector\ScheduleProvider;
use NHL\Exceptions\NonExistentTeamException;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use NHL\Schedule\ScheduleImporter;

class ScheduleImporterCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nhl:import-schedule';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import a teams schedule by season.';

	/**
	 * Create a new command instance.
	 *
	 * @return \ScheduleImporterCommand
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
	public function fire()
	{
		$teamId = $this->argument('teamId');

        try
        {
	        $provider = App::make(ScheduleProvider::class);
	        $provider->setTeam($teamId);
	        $provider->addConsumer(
		        App::make(ScheduleDatabaseConsumer::class, ['team' => $teamId])
	        );
	        $provider->execute();
        }
        catch(NonExistentTeamException $e)
        {
            $this->error('Invalid Team ID');
        }
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('teamId', InputArgument::REQUIRED, 'TOR'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
//			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
