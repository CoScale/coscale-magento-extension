<?php
/**
 * Set up an key value based database structure
 *
 * @package CoScale_Monitor
 * @author Rian Orie <rian.orie@supportdesk.nu>
 * @created 2015-07-03
 * @version 1.0
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$metric = $installer->getConnection();
$metric->changeColumn($installer->getTable('coscale_monitor/metric'), 'datatype', 'type', 'VARCHAR(1) NULL');
$metric->dropColumn($installer->getTable('coscale_monitor/metric'), 'groups');
$metric->dropColumn($installer->getTable('coscale_monitor/metric'), 'tags');
$metric->dropColumn($installer->getTable('coscale_monitor/metric'), 'calctype');
$metric->addColumn($installer->getTable('coscale_monitor/metric'), 'store_id', 'INT(5) UNSIGNED NOT NULL');

$metric->dropIndex($installer->getTable('coscale_monitor/metric'), 'COSCALE_METRIC_UNIQUE_IDX');
$metric->addIndex($installer->getTable('coscale_monitor/metric'), 'COSCALE_METRIC_UNIQUE_IDX', array('key', 'store_id'), $metric::INDEX_TYPE_UNIQUE);

$installer->endSetup();