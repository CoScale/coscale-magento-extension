<?php
/**
 * Change storige engine for metric table
 *
 * @package CoScale_Monitor
 * @author V. Kerkhoff <v.kerkhoff@genmato.com>
 * @created 2015-08-18
 * @version 0.5
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$metric = $installer->getConnection();
$metric->changeTableEngine($installer->getTable('coscale_monitor/metric'), 'InnoDB');


/**
 * Initialize order data for further delta processing
 */
Mage::getSingleton('coscale_monitor/metric_order')->initOrderData();

$installer->endSetup();