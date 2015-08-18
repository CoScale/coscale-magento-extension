<?php

/**
 * CoScale metric data collection
 *
 * @package CoScale_Monitor
 * @author Rian Orie <rian.orie@supportdesk.nu>
 * @created 2015-07-03
 * @version 1.0
 */
class CoScale_Monitor_Model_Resource_Metric_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Construct the data collection
     */
    protected function _construct()
    {
        $this->_init('coscale_monitor/metric');
    }
}