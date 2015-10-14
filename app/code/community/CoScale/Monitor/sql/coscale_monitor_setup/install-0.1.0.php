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

$event = $installer->getConnection()
    ->newTable($installer->getTable('coscale_monitor/event'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER,
        10,
        array(
            'nullable' => false,
            'primary' => true,
            'identity' => true
        ),
        'Identifier')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        array(
            'nullable' => false,
            'primary' => false,
        ),
        'Event name')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        array(
            'nullable' => false,
            'primary' => false,
        ),
        'Event description')
    ->addColumn('event_data', Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        array(
            'nullable' => true,
            'primary' => false,
        ),
        'Event data (json)')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_VARCHAR,
        50,
        array(
            'nullable' => false,
            'primary' => false,
        ),
        'Event type')
    ->addColumn('source', Varien_Db_Ddl_Table::TYPE_VARCHAR,
        50,
        array(
            'nullable' => false,
            'primary' => false,
        ),
        'Event source')
    ->addColumn('state', Varien_Db_Ddl_Table::TYPE_INTEGER,
        1,
        array(
            'nullable' => false,
            'primary' => false,
        ),
        'Event state (-1 = disabled, 0 = inactive, 1 = on)')
    ->addColumn('version', Varien_Db_Ddl_Table::TYPE_VARCHAR,
        10,
        array(
            'nullable' => false,
            'primary' => false,
        ),
        'Event version (incremented on every update)')
    ->addColumn('timestamp', Varien_Db_Ddl_Table::TYPE_INTEGER,
        15,
        array(),
        'Update timestamp')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Update Timestamp readable')
    ->setComment('CoScale event data');

$event->setOption('type', 'MEMORY');

$metric = $installer->getConnection()
    ->newTable($installer->getTable('coscale_monitor/metric'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER,
        5,
        array(
            'nullable' => false,
            'primary' => true,
            'identity' => true
        ),
        'Identifier')
    ->addColumn('key', Varien_Db_Ddl_Table::TYPE_INTEGER,
        5,
        array(
            'nullable' => false,
            'primary' => false,
        ),
        'Fixed identifier for the metric')
    ->addColumn('datatype', Varien_Db_Ddl_Table::TYPE_VARCHAR,
        32,
        array(
            'nullable' => false,
            'primary' => false,
        ),
        'Metric datatype')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        array(
            'nullable' => false,
            'primary' => false,
        ),
        'Metric name')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        array(
            'nullable' => false,
            'primary' => false,
        ),
        'Metric description')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        array(
            'nullable' => true,
            'primary' => false,
        ),
        'Metric value (mixed content)')
    ->addColumn('unit', Varien_Db_Ddl_Table::TYPE_VARCHAR,
        10,
        array(
            'nullable' => false,
            'primary' => false,
        ),
        'Metric unit')
    ->addColumn('groups', Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        array(
            'nullable' => true,
            'primary' => false,
        ),
        'Metric groups')
    ->addColumn('tags', Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        array(
            'nullable' => true,
            'primary' => false,
        ),
        'Metric tags')
    ->addColumn('calctype', Varien_Db_Ddl_Table::TYPE_INTEGER,
        2,
        array(
            'nullable' => false,
            'primary' => false,
        ),
        'Metric calculation type')
    ->addColumn('timestamp', Varien_Db_Ddl_Table::TYPE_INTEGER,
        15,
        array(),
        'Update timestamp')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Update Timestamp')
    ->addIndex('COSCALE_METRIC_UNIQUE_IDX', 'key', array('type' => 'UNIQUE'))
    ->setComment('CoScale metric data');

$metric->setOption('type', 'MEMORY');


$installer->getConnection()->createTable($event);
$installer->getConnection()->createTable($metric);
$installer->endSetup();
