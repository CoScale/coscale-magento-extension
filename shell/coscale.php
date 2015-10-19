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
    protected $debug = false;

    /**
     * Execute the script
     */
    public function run()
    {

        $output['metrics'] = array();

        if ($this->getArg('debug')) {
            $this->debug = true;
        }

        // Metric collection
        $this->writeDebug('Start Metric Collection');
        $startTime = microtime(true);

        $metricOrderDelete = Mage::getSingleton('coscale_monitor/metric_order');
        $collection = Mage::getModel('coscale_monitor/metric')->getCollection();
        /** @var CoScale_Monitor_Model_Metric $metric */
        foreach ($collection as $metric) {
            $output['metrics'][] = array(
                'name' => $metric->getName(),
                'unit' => $metric->getUnit(),
                'value' => (float)$metric->getValue(),
                'store_id' => (int)$metric->getStoreId(),
                'type' => $metric->getTypeText());

            // Check if metric need to be reset after collection
            if ($metricOrderDelete->resetOnCollect($metric->getKey())) {
                $metric->delete();
            }
        }
        $this->writeDebug('Completed in '.(microtime(true)-$startTime).' seconds');


        // Abandonned carts
        $this->writeDebug('Start Abandonned carts');
        $startTime = microtime(true);

        $carts = Mage::getSingleton('coscale_monitor/metric_order')->getAbandonnedCarts();
        foreach ($carts as $data) {
            $output['metrics'][] =$data;
        }
        $this->writeDebug('Completed in '.(microtime(true)-$startTime).' seconds');

        // Amount of files in var/log
        $this->writeDebug('Start Error Reports');
        $startTime = microtime(true);

        $output['metrics'][] = Mage::getSingleton('coscale_monitor/metric_file')->getErrorReports();
        $this->writeDebug('Completed in '.(microtime(true)-$startTime).' seconds');

        // Log file details
        $this->writeDebug('Start Log Files');
        $startTime = microtime(true);

        $logFiles = Mage::getSingleton('coscale_monitor/metric_file')->getLogFiles();
        foreach ($logFiles as $data) {
            $output['metrics'][] = $data;
        }
        $this->writeDebug('Completed in '.(microtime(true)-$startTime).' seconds');

        // URL Rewrite details
        $this->writeDebug('Start URL Rewrites');
        $startTime = microtime(true);

        $urlRewrites = Mage::getSingleton('coscale_monitor/metric_rewrite')->getUrlRewrites();
        foreach ($urlRewrites as $data) {
            $output['metrics'][] = $data;
        }
        $this->writeDebug('Completed in '.(microtime(true)-$startTime).' seconds');

        // Email queue size
        $this->writeDebug('Start Email Queue Site');
        $startTime = microtime(true);

        $emailQueueSize = Mage::getSingleton('coscale_monitor/metric_order')->getEmailQueueSize();
        foreach ($emailQueueSize as $data) {
            $output['metrics'][] = $data;
        }
        $this->writeDebug('Completed in '.(microtime(true)-$startTime).' seconds');

        $output['events'] = array();

        // Maintenance Flag
        $this->writeDebug('Start Maintenance Flag');
        $startTime = microtime(true);


        if (file_exists(Mage::getBaseDir('base') . DS . 'maintenance.flag')) {
            $output['events'][] = array(
                'type' => CoScale_Monitor_Model_Event::GROUP_ADMIN,
                'message' => 'Maintenance mode enabled',
                'start_time' => 0,
                'stop_time' => 0,
            );
        }
        $this->writeDebug('Completed in '.(microtime(true)-$startTime).' seconds');

        // Event collection
        $this->writeDebug('Start Event Collection');
        $startTime = microtime(true);

        $collection = Mage::getModel('coscale_monitor/event')->getCollection();
        /** @var CoScale_Monitor_Model_Event $event */
        foreach ($collection as $event) {
            $output['events'][] = array(
                'type' => $event->getTypeGroup(),
                'message' => $event->getDescription(),
                'data' => array_merge(array('originator'=>$event->getSource()), unserialize($event->getEventData())),
                'start_time' => (int)(time() - $event->getTimestampStart()),
                'stop_time' => (int)($event->getTimestampEnd() != 0 ? (time() - $event->getTimestampEnd()) : 0),
            );
            $event->delete();
            if ($event->getState() != $event::STATE_ENABLED) {
                //$event->delete();
            }
        }
        $this->writeDebug('Completed in '.(microtime(true)-$startTime).' seconds');

        // Cronjobs
        $this->writeDebug('Start Cronjobs Collection');
        $startTime = microtime(true);

        $endDateTime = date('U');
        $collection = Mage::getModel('cron/schedule')->getCollection()
            ->addFieldToFilter('finished_at', array('from' => date('Y-m-d H:i:s', $endDateTime-65)))
            ->setOrder('finished_at', 'DESC');
        /** @var Mage_Cron_Model_Schedule $event */
        foreach ($collection as $cron) {
            $output['events'][] = array(
                'type' => CoScale_Monitor_Model_Event::GROUP_CRON,
                'message' => $cron->getJobCode(),
                'status' => $cron->getStatus(),
                'start_time' => (int)(time() - strtotime($cron->getExecutedAt())),
                'stop_time' => (int)(time() - strtotime($cron->getFinishedAt())),
            );
        }
        $this->writeDebug('Completed in '.(microtime(true)-$startTime).' seconds');

        // Modules
        $this->writeDebug('Start Modules Collection');
        $startTime = microtime(true);

        $output['modules'] = array();
        $output['modules'][] = array('name' => 'core', 'version' => (string)Mage::getVersion());
        foreach (Mage::getConfig()->getNode('modules')->children() as $module) {
            $output['modules'][] = array('name' => $module->getName(), 'version' => (string)$module->version);
        }
        $this->writeDebug('Completed in '.(microtime(true)-$startTime).' seconds');

        // Stores
        $this->writeDebug('Start Stores Collection');
        $startTime = microtime(true);

        $output['stores'] = array();
        foreach (Mage::app()->getStores() as $store) {
            $output['stores'][] = array('name' => $store->getName(), 'id' => (int)$store->getId());
        }
        $this->writeDebug('Completed in '.(microtime(true)-$startTime).' seconds');

        // Write JSON output
        $this->writeDebug('Start Write JSON Output');
        $startTime = microtime(true);

        echo Zend_Json::encode($output);
        $this->writeDebug('Completed in '.(microtime(true)-$startTime).' seconds');

        $this->writeDebug('Run completed!');
    }

    public function writeDebug($msg)
    {
        if ($this->debug) {
            Mage::log($msg, Zend_Log::DEBUG, 'coscale-collect.log', true);
        }
    }
}

$shell = new CoScale_Shell();
$shell->run();
