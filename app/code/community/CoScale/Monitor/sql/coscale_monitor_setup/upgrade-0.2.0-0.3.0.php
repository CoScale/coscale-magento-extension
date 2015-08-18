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
$event->addColumn($installer->getTable('coscale_monitor/event'), 'timestamp_end', 'INT(11) UNSIGNED NULL AFTER `timestamp`');
$event->dropColumn($installer->getTable('coscale_monitor/event'), 'duration');
$event->changeColumn($installer->getTable('coscale_monitor/event'), 'timestamp', 'timestamp_start', 'INT(11) UNSIGNED NULL');

$installer->endSetup();