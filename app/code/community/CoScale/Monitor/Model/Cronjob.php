<?php
/**
 * Cronjob director to run daily metric data reset/generation
 *
 * @package CoScale_Monitor
 * @author  Vladimir Kerkhoff <v.kerkhoff@genmato.com>
 * @version 1.0
 * @created 2015-08-18
 */

class CoScale_Monitor_Model_Cronjob
{
    /**
     * Daily cronjob to update daily values
     */

    public function dailyCron()
    {
        // Customer metric data
        Mage::getSingleton('coscale_monitor/metric_customer')->dailyCron();
        Mage::getSingleton('coscale_monitor/metric_product')->dailyCron();
        Mage::getSingleton('coscale_monitor/metric_order')->dailyCron();
    }
}