<?php
/**
 * @package CoScale_Monitor
 * @author V. Kerkhoff <v.kerkhoff@genmato.com>
 * @created 2016-03-25
 * @version 0.13.0
 *
 * @author Mihai Oprea <mihai.oprea@issco.ro>
 * @created 2016-03-03
 * @version 0.12.0
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->getConnection()
    ->addColumn(
        $installer->getTable('coscale_monitor/metric'),
        'calculation_type',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'nullable' => false,
            'default' => 0,
            'comment' => 'Calculation Type'
        )
    );
$metric = $installer->getConnection();

/**
 * Initialize order data for further delta processing
 */
Mage::getSingleton('coscale_monitor/metric_order')->initOrderData();
Mage::getSingleton('coscale_monitor/metric_customer')->updateTotalCount();
Mage::getSingleton('coscale_monitor/metric_product')->updateTotalCount();

$installer->endSetup();