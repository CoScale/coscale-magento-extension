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
		$output['metrics'] = array();

		$collection = Mage::getModel('coscale_monitor/metric')->getCollection();
		/** @var CoScale_Monitor_Model_Metric $metric */
		foreach($collection as $metric) {
			$output['metrics'][] = array('name' => $metric->getName(),
			                             'unit' => $metric->getUnit(),
			                             'value' => $metric->getValue(),
			                             'store_id' => $metric->getStoreId(),
			                             'type' => $metric->getType());
		}

		$output['events'] = array();

		$collection = Mage::getModel('coscale_monitor/event')->getCollection();
		/** @var CoScale_Monitor_Model_Event $event */
		foreach($collection as $event) {

			$output['events'][] = array('type' => $event->getTypeGroup(),
			                            'message' => $event->getName(),
			                            'start_time' => (time()-$event->getTimestampStart()),
			                            'stop_time' => ($event->getTimestampEnd() != 0 ? (time()-$event->getTimestampEnd()) : 0),
			                            );

			if ($event->getState() != $event::STATE_ENABLED) {
				//$event->delete();
			}
		}

		$output['modules'] = array();
		foreach(Mage::getConfig()->getNode('modules')->children() as $module) {
			$output['modules'][] = array('name' => $module->getName(), 'version' => (string)$module->version);
		}

		$output['stores'] = array();
		foreach(Mage::app()->getStores() as $store) {
			$output['stores'][] = array('name' => $store->getName(), 'id' => $store->getId());
		}

		echo Zend_Json::encode($output);
	}
}

$shell = new CoScale_Shell();
$shell->run();