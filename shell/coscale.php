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
        $logFiles = Mage::getSingleton('coscale_monitor/metric_file')->getLogFiles();
        foreach ($logFiles as $data) {
            $output['metrics'][] = $data;
        }

        // URL Rewrite details
        $urlRewrites = Mage::getSingleton('coscale_monitor/metric_rewrite')->getUrlRewrites();
        foreach ($urlRewrites as $data) {
            $output['metrics'][] = $data;
        }

        // Email queue size
        $emailQueueSize = Mage::getSingleton('coscale_monitor/metric_order')->getEmailQueueSize();
        if (!empty($emailQueueSize)) {
            $output['metrics'][] = $emailQueueSize;
        }

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

        $endDateTime = date('U');//Mage::getModel('core/date')->timestamp(time()); // Current timestamp - 65 seconds
        $collection = Mage::getModel('cron/schedule')->getCollection()
            ->addFieldToFilter('finished_at', array('from' => date('Y-m-d H:i:s', $endDateTime-65)))
            ->setOrder('finished_at', 'DESC');
        /** @var Mage_Cron_Model_Schedule $event */
        foreach ($collection as $cron) {
            $output['events'][] = array(
                'type' => CoScale_Monitor_Model_Event::GROUP_CRON,
                'message' => $cron->getJobCode(),
                'status' => $cron->getStatus(),
                'start_time' => (int)strtotime($cron->getExecutedAt()),
                'stop_time' => (int)strtotime($cron->getFinishedAt()),
            );
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
