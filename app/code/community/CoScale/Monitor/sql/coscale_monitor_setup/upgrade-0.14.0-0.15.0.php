<?php
/**
 * @package CoScale_Monitor
 * @author Mihai Oprea <mihai.oprea@issco.ro>
 * @created 2016-05-18
 * @version 0.15.0
 */
$installer = $this;

$installer->startSetup();

$metric = $installer->getConnection();

/**
 * Initialize order data for further delta processing
 */
Mage::getSingleton('coscale_monitor/metric_order')->initOrderData();
Mage::getSingleton('coscale_monitor/metric_customer')->updateTotalCount();
Mage::getSingleton('coscale_monitor/metric_product')->updateTotalCount();

$installer->endSetup();