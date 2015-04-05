<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class loadBidHistoryCSV extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'load:bidhistorycsv';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Load CSV of bid history.';

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
		if (($handle = fopen("data/bidhistory.csv", "r")) !== FALSE) {
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

		foreach ($array as $bid_data){

			$property = new SmartProperty(ucwords(strtolower($bid_data['Address1'])), ucwords(strtolower($bid_data['Address3'])), $bid_data['Post_Code']);

			$bid = new PropertyBidHistory(array(
				'date' => strtotime($bid_data['Commencement_Date']),
				'interest_count' => (int) $bid_data['Number of Expressions of Interest']
			));

			$property->db->bidHistory()->save($bid);

			$this->info('Property ' . $property->db->street_address . ' done.');

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
