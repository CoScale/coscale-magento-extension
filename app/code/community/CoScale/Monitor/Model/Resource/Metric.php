<?php
/**
 * CoScale metric data resource model
 *
 * @package CoScale_Monitor
 * @author Rian Orie <rian.orie@supportdesk_nu>
 * @created 2015-07-03
 * @version 1.0
 */ 
class CoScale_Monitor_Model_Resource_Metric extends Mage_Core_Model_Resource_Db_Abstract
{
	/**
	 * Construct the resource model
	 */
    protected function _construct()
    {
        $this->_init('coscale_monitor/metric', 'id');
    }
}