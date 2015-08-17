<?php
/**
 * CoScale Event interaction model
 *
 * @package CoScale_Monitor
 * @author Rian Orie <rian.orie@supportdesk.nu>
 * @created 2015-07-03
 * @version 1.0
 */
class CoScale_Monitor_Model_Event_Store
{
	/**
	 * Track the adding of a new store
	 *
	 * @param Varien_Event_Observer $event
	 */
	public function addNew(Varien_Event_Observer $event)
	{
		/** @var Mage_Core_Model_Store $store */
		$store = $event->getStore();

		$event = Mage::getModel('coscale_monitor/event');
		$event->addEvent($event::TYPE_STORE_ADD,
		                 'Store added',
		                 'A new store was added',
		                 array('id' => $store->getId(),
		                       'name' => $store->getName(),
		                       'code' => $store->getCode(),
		                       'website' => $store->getWebsiteId()
		                    ),
		                 Mage::getSingleton('admin/session')->getUser()->getUsername()
						 );

		$customertotal = Mage::getModel('coscale_monitor/metric');
        	$customertotal->incrementMetric(
			$customertotal::KEY_CUSTOMER_TOTAL,
			$store->getId(),
			$customertotal::TYPE_APPLICATION,
			'Total customers',
			'The total number of customers in the system',
			0,
			'customers');

        	$orderAverageSize = Mage::getModel('coscale_monitor/metric');
        	$orderAverageSize->incrementMetric(
			$orderAverageSize::KEY_ORDER_SIZE_AVERAGE,
			$store->getId(),
			$orderAverageSize::TYPE_APPLICATION,
			'Order size average',
			'The average size of an order in the system for this store',
			0,
			'orders');

	   	$orderAverageAmount = Mage::getModel('coscale_monitor/metric');
		$orderAverageAmount->incrementMetric(
			$orderAverageAmount::KEY_ORDER_AMOUNT_AVERAGE,
			$store->getId(),
			$orderAverageAmount::TYPE_APPLICATION,
			'Order amount average',
			'The average amount of an order in the system for this store',
			0,
			'$');

	    	$orderTotal = Mage::getModel('coscale_monitor/metric');
	    	$orderTotal->incrementMetric(
		    	$orderTotal::KEY_ORDER_TOTAL,
		    	$store->getId(),
		    	$orderTotal::TYPE_APPLICATION,
		    	'Total orders',
		    	'The total number of orders in the system for this store',
		    	0,
		   	'orders');
	}
}
