<?php
/**
 * Set up an key value based database structure
 *
 * @package CoScale_Monitor
 * @author Rian Orie <rian.orie@gmail.com>
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
									'nullable'  => false,
									'primary'   => true,
									'identity'  => true
								),
					           'Identifier')
					->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR,
					            255,
					            array(
									'nullable'  => false,
									'primary'   => false,
									),
					            'Event name')
					->addColumn('description', Varien_Db_Ddl_Table::TYPE_VARCHAR,
					           255,
					           array(
					               'nullable'  => false,
					               'primary'   => false,
					           ),
					           'Event description')
					->addColumn('event_data', Varien_Db_Ddl_Table::TYPE_VARCHAR,
					            255,
					            array(
						            'nullable'  => true,
						            'primary'   => false,
					            ),
					            'Event data (json)')
					->addColumn('type', Varien_Db_Ddl_Table::TYPE_VARCHAR,
					           50,
					           array(
					               'nullable'  => false,
					               'primary'   => false,
					           ),
					           'Event type')
					->addColumn('source', Varien_Db_Ddl_Table::TYPE_VARCHAR,
					            50,
					            array(
						            'nullable'  => false,
						            'primary'   => false,
					            ),
					            'Event source')
					->addColumn('state', Varien_Db_Ddl_Table::TYPE_INTEGER,
					            1,
					            array(
						            'nullable'  => false,
						            'primary'   => false,
					            ),
					            'Event state (-1 = disabled, 0 = inactive, 1 = on)')
					->addColumn('version', Varien_Db_Ddl_Table::TYPE_VARCHAR,
					            10,
					            array(
						            'nullable'  => false,
						            'primary'   => false,
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

$event->setOption('type', Varien_Db_Adapter_Pdo_Mysql::ENGINE_MEMORY);

$metric = $installer->getConnection()
					->newTable($installer->getTable('coscale_monitor/metric'))
					->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER,
					           5,
					           array(
					               'nullable'  => false,
					               'primary'   => false,
					           ),
					           'Identifier')
					->addColumn('datatype', Varien_Db_Ddl_Table::TYPE_INTEGER,
					            2,
					            array(
						            'nullable'  => false,
						            'primary'   => false,
					            ),
					            'Metric datatype')
                   ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR,
                               255,
                               array(
	                               'nullable'  => false,
	                               'primary'   => false,
                               ),
                               'Metric name')
                   ->addColumn('description', Varien_Db_Ddl_Table::TYPE_VARCHAR,
                               255,
                               array(
	                               'nullable'  => false,
	                               'primary'   => false,
                               ),
                               'Metric description')
                   ->addColumn('groups', Varien_Db_Ddl_Table::TYPE_VARCHAR,
                               255,
                               array(
	                               'nullable'  => true,
	                               'primary'   => false,
                               ),
                               'Metric groups')
                   ->addColumn('unit', Varien_Db_Ddl_Table::TYPE_VARCHAR,
                               10,
                               array(
	                               'nullable'  => false,
	                               'primary'   => false,
                               ),
                               'Metric unit')
                   ->addColumn('tags', Varien_Db_Ddl_Table::TYPE_VARCHAR,
                               255,
                               array(
	                               'nullable'  => true,
	                               'primary'   => false,
                               ),
                               'Metric tags')
				   ->addColumn('calctype', Varien_Db_Ddl_Table::TYPE_INTEGER,
					           2,
					           array(
					                'nullable'  => false,
					                'primary'   => false,
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
                   ->addIndex('COSCALE_METRIC_IDX', 'id')
                   ->setComment('CoScale metric data');

$metric->setOption('type', Varien_Db_Adapter_Pdo_Mysql::ENGINE_MEMORY);


$installer->getConnection()->createTable($event);
$installer->getConnection()->createTable($metric);
$installer->endSetup();