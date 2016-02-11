<?php

/**
 * CoScale module helper
 *
 * @package CoScale_Monitor
 * @author Rian Orie <rian.orie@supportdesk.nu>
 * @created 2015-07-03
 * @version 1.0
 */
class CoScale_Monitor_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $debug = false;
    protected $timer = array();

    public function isEnabled()
    {
        if (!Mage::getStoreConfig('system/coscale_monitor/enabled')) {
            return false;
        }

        $resource = Mage::getSingleton('core/resource')->getConnection('core_write');

        try {
            $tables = Mage::getConfig()->getNode('global/models/coscale_monitor_resource/entities')->asArray();

            foreach ($tables as $id => $data) {
                $metricTable = $resource->getTableName($data['table']);
                if (! $resource->isTableExists($metricTable)) {
                    return false;
                }
            }
        } catch (Exception $ex) {
            return false;
        }
        return true;
    }

    public function writeDebug($msg)
    {
        if ($this->debug) {
            Mage::log($msg, Zend_Log::DEBUG, 'coscale-collect.log', true);
        }
    }

    public function debugStart($name)
    {
        $this->writeDebug('Start: ' . $name);
        $this->timer[$name] = microtime(true);
    }

    public function debugEnd($name)
    {
        $startTime = isset($this->timer[$name]) ? $this->timer[$name]:microtime(true);
        $duration = microtime(true)-$startTime;

        $this->writeDebug('Completed: ' . $name . ' (' . number_format($duration, 6) . 's)');
    }

    public function debugEndError($name, $ex)
    {
        $startTime = isset($this->timer[$name]) ? $this->timer[$name]:microtime(true);
        $duration = microtime(true)-$startTime;

        $this->writeDebug('Error: ' . $name . ' (' . number_format($duration, 6) . 's) [' . $ex->getMessage() . ']');
    }

    public function enableDebug()
    {
        $this->debug = true;
    }
}