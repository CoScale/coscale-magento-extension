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
     * @param CoScale_Monitor_Model_Metric $metric
     * @param int $key The identifier to load the metric by
     * @param int $store The store to load the metric belongs to
     *
     * @return $this
     *
     * @throws Exception
     */
    public function loadByKey(CoScale_Monitor_Model_Metric $metric, $key, $store)
    {
        $adapter = $this->_getReadAdapter();
        $bind = array('key' => $key, 'store' => $store);
        $select = $adapter->select()
            ->from($this->getTable('coscale_monitor/metric'), array('id'))
            ->where('`key` = :key AND `store_id` = :store');


        $metricId = $adapter->fetchOne($select, $bind);
        if ($metricId) {
            $this->load($metric, $metricId);
        } else {
            $metric->setData(array());
        }

        return $this;
    }
}