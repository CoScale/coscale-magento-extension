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

$event = $installer->getConnection();
$event->changeColumn($installer->getTable('coscale_monitor/event'), 'datatype', 'type', 'VARCHAR(1) UNSIGNED NULL');
$event->dropColumn($installer->getTable('coscale_monitor/event'), 'groups');
$event->dropColumn($installer->getTable('coscale_monitor/event'), 'tags');
$event->dropColumn($installer->getTable('coscale_monitor/event'), 'calctype');
$event->addColumn($installer->getTable('coscale_monitor/event'), 'store_id', 'INT(5) UNSIGNED NOT NULL');

$installer->endSetup();