<?php

/*
|--------------------------------------------------------------------------
| Register The Artisan Commands
|--------------------------------------------------------------------------
|
| Each available Artisan command must be registered with the console so
| that it is available to be called. We'll register every command so
| the console gets access to each of the command object instances.
|
*/

Artisan::add(new StandingsImporterCommand(App::make('NHL\Standings\Importer'), App::make('NHL\Storage\Match\MatchRepository'), App::make('Illuminate\Config\Repository')));

Artisan::add(App::make('ScheduleImporterCommand'));
Artisan::add(App::make('UpdateTodaysScheduleCommand'));
Artisan::add(App::make('UpdateScoreboardCommand'));