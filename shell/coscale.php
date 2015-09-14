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
        Mage::getSingleton('coscale_monitor/metric_product')->dailyCron();
        die();

        $collection = Mage::getModel('coscale_monitor/metric')->getCollection();
        /** @var CoScale_Monitor_Model_Metric $metric */
        foreach ($collection as $metric) {
            $output['metrics'][] = array(
                'name' => $metric->getName(),
                'unit' => $metric->getUnit(),
                'value' => (float)$metric->getValue(),
                'store_id' => (int)$metric->getStoreId(),
                'type' => $metric->getTypeText());
        }

        // Abandonned carts
        $carts = Mage::getSingleton('coscale_monitor/metric_order')->getAbandonnedCarts();
        foreach ($carts as $data) {
            $output['metrics'][] =$data;
        }

        // Amount of files in var/log
        $output['metrics'][] = Mage::getSingleton('coscale_monitor/metric_file')->getErrorReports();

        // Log file details
        $output['metrics'][] = Mage::getSingleton('coscale_monitor/metric_file')->getLogFiles();

        // URL Rewrite details
        $output['metrics'][] = Mage::getSingleton('coscale_monitor/metric_rewrite')->getUrlRewrites();

        // Email queue size
        $output['metrics'][] = Mage::getSingleton('coscale_monitor/metric_order')->getEmailQueueSize();

        $output['events'] = array();

        if (file_exists(Mage::getBaseDir('base') . DS . 'maintenance.flag')) {
            $output['events'][] = array(
                'type' => 'maintenance mode',
                'message' => 'Maintenance mode enabled',
                'start_time' => 0,
                'stop_time' => 0,
            );
        }

        $collection = Mage::getModel('coscale_monitor/event')->getCollection();
        /** @var CoScale_Monitor_Model_Event $event */
        foreach ($collection as $event) {
            $output['events'][] = array(
                'type' => $event->getTypeGroup(),
                'message' => $event->getName(),
                'start_time' => (int)(time() - $event->getTimestampStart()),
                'stop_time' => (int)($event->getTimestampEnd() != 0 ? (time() - $event->getTimestampEnd()) : 0),
            );
            $event->delete();
            if ($event->getState() != $event::STATE_ENABLED) {
                //$event->delete();
            }
        }

        $output['modules'] = array();
        $output['modules'][] = array('name' => 'core', 'version' => (string)Mage::getVersion());
        foreach (Mage::getConfig()->getNode('modules')->children() as $module) {
            $output['modules'][] = array('name' => $module->getName(), 'version' => (string)$module->version);
        }

        $output['stores'] = array();
        foreach (Mage::app()->getStores() as $store) {
            $output['stores'][] = array('name' => $store->getName(), 'id' => (int)$store->getId());
        }

        echo Zend_Json::encode($output);
    }
}

$shell = new CoScale_Shell();
$shell->run();
