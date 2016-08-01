<?php
/**
 * Abstract class for processing metric data
 *
 * @package CoScale_Monitor
 * @author  Vladimir Kerkhoff <v.kerkhoff@genmato.com>
 * @version 1.0
 * @created 2015-08-18
 */
class CoScale_Monitor_Model_Metric_Abstract
{
    protected $_metric = false;
    protected $_metricData = array();
    protected $_helper = false;

    protected $_metricType = CoScale_Monitor_Model_Metric::TYPE_APPLICATION;

    const ACTION_UPDATE = 1;
    const ACTION_INCREMENT=2;

    public function __construct()
    {
        $this->_metric = Mage::getModel('coscale_monitor/metric');
        $this->_helper = Mage::helper('coscale_monitor');
        $this->_contruct();
    }

    /**
     * Public contructor
     */
    public function _contruct()
    {

    }

    /**
     * Shorthand to add/update or increment a metric to the system
     *
     * @param int $action Specify action
     * @param int $key A key as predefined in this model
     * @param int $store The store id
     * @param int|bool $type A predefined type for the metric
     * @param string|bool $name The name of the metric
     * @param string|bool $descr A longer description of the metric
     * @param mixed $value The value to set
     * @param string|bool $unit The unit the value is saved in
     *
     * @throws Exception
     */
    protected function setMetric($action, $key, $store, $value, $unit = false, $type = false, $name = false, $descr = false)
    {
        if (!$type) {
            $type = $this->_metricType;
        }

        if (!$name && isset($this->_metricData[$key]['name'])) {
            $name = $this->_metricData[$key]['name'];
        }

        if (!$descr && isset($this->_metricData[$key]['description'])) {
            $descr = $this->_metricData[$key]['description'];
        }

        if (!$unit && isset($this->_metricData[$key]['unit'])) {
            $unit = $this->_metricData[$key]['unit'];
        }

        if (!$name || !$descr || !$unit) {
            throw new Exception('Invalid metric data supplied');
        }
        $calctype = 0;
        if (isset($this->_metricData[$key]['calctype'])) {
            $calctype = $this->_metricData[$key]['calctype'];
        }

        if ($action == self::ACTION_UPDATE) {
            $this->_metric->updateMetric($key, $store, $type, $name, $descr, $value, $unit, $calctype);
        } else {
            $this->_metric->incrementMetric($key, $store, $type, $name, $descr, $value, $unit, $calctype);
            if (isset($this->_metricData[$key]['combine']) && $this->_metricData[$key]['combine']) {
                $this->_metric->incrementMetric($key, 0, $type, $name, $descr, $value, $unit, $calctype);
            }
        }
    }

    /**
     * @param $key
     * @param $store
     * @return int
     */
    protected function getMetric($key, $store)
    {
        if (!$data = $this->getMetricData($key, $store)) {
            return 0;
        }
        return $data->getValue();
    }

    /**
     * @param $key
     * @param $store
     * @return $data|bool
     */
    protected function getMetricData($key, $store)
    {
        /** @var CoScale_Monitor_Model_Metric $data */
        $data = $this->_metric->loadByKey($key, $store);

        if (!$data->getId()) {
            return false;
        }
        return $data;
    }


}