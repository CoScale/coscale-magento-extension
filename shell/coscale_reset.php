<?php

require_once 'abstract.php';

/**
 * CoScale shell script that will reset the different elements of the module
 *
 * @package     CoScale_Monitor
 * @author      Mihai Oprea <mihai.oprea@issco.ro>
 * @created     2016-08-11
 * @version     1.0
 */
class CoScale_Reset extends Mage_Shell_Abstract
{    
    /**
     * Execute the script
     */
    public function run()
    {
        $helper = Mage::helper('coscale_monitor');

        // Reset data
        try {
            $helper->debugStart('Metrics reset');
            Mage::getResourceModel('coscale_monitor/metric')->truncate();

            Mage::getSingleton('coscale_monitor/metric_order')->initOrderData();
            Mage::getSingleton('coscale_monitor/metric_customer')->updateTotalCount();
            Mage::getSingleton('coscale_monitor/metric_product')->updateTotalCount();
            $helper->debugEnd('Metrics reset');
        } catch (Exception $ex) {
            $helper->debugEndError('Reset script', $ex);
        }
    }
}

$shell = new CoScale_Reset();
$shell->run();
