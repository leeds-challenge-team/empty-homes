<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class loadLTECSV extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'load:ltecsv';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Load CSV of long-term empty numbers.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
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
		$i = 0;
		if (($handle = fopen("data/longtermempty.csv", "r")) !== FALSE) {
		    while (($row = fgetcsv($handle, 4096)) !== false) {
		        if (empty($fields)) {
		            $fields = $row;
		            continue;
		        }
		        foreach ($row as $k=>$value) {
		            $array[$i][$fields[$k]] = $value;
		        }
		        $i++;
		    }
		    fclose($handle);
		}

		foreach ($array as $ward_data){

			$wardname = ucwords(strtolower(array_shift($ward_data)));

			$ward = Ward::where('name', '=', $wardname)->first();

			if (!$ward){

				$ward = new Ward();
				$ward->name = $wardname;
				$ward->save();

			}

			$this->info('Data for ward ' . $ward->id . ': ' . $ward->name);

			foreach ($ward_data as $date => $count) {

				$ltv = new LongTermVoid();
				$ltv->count = $count;

				$datebits = explode('-', $date);
				$ltv->date = strtotime('1 ' . $datebits[0] . ' ' . '20' . $datebits[1]);
				$ltv->ward()->associate($ward);
				$ltv->save();
			}

		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

}
