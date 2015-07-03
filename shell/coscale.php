<?php

require_once 'abstract.php';

/**
 * CoScale shell script that will output the different elements of the module
 *
 * @package     CoScale_Monitor
 * @author      Rian Orie <rian.orie@supportdesk.nu>
 * @created     2015-07-03
 * @version     1.0
 */
class CoScale_Shell extends Mage_Shell_Abstract
{
	/**
	 * Execute the script
	 */
	public function run()
	{
		if ($this->getArg('c')) {
			$this->generateJson();

		} elseif ($this->getArg('d')) {
			$this->generateData();
		}
	}

	/**
	 * output a json string of all the combined information
	 *
	 * @return string
	 */
	private function generateJson()
	{
		printf("{json: \"temporary placeholder\"}\n");
	}

	/**
	 * Output a compressed version of the data
	 */
	private function generateData()
	{
		printf("M1: %f\n", 8.5);
	}
}

$shell = new CoScale_Shell();
$shell->run();