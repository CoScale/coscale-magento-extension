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

	/**
	 * Load metric by key
	 *
	 * @throws Exception
	 *
	 * @param CoScale_Monitor_Model_Metric $metric
	 * @param int $key The identifier to load the metric by

	 * @return $this
	 */
	public function loadByKey(CoScale_Monitor_Model_Metric $metric, $key)
	{
		$adapter = $this->_getReadAdapter();
		$bind    = array('key' => $key);
		$select  = $adapter->select()
		                   ->from($this->getTable('coscale_monitor/metric'), array('id'))
		                   ->where('`key` = :key');


		$metricId = $adapter->fetchOne($select, $bind);
		if ($metricId) {
			$this->load($metric, $metricId);
		} else {
			$metric->setData(array());
		}

		return $this;
	}
}