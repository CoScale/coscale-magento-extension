<?php

/**
 * Observer for the metrics related to orders
 *
 * @package CoScale_Monitor
 * @author  Rian Orie <rian.orie@supportdesk.nu>
 * @version 1.0
 * @created 2015-07-07
 */
class CoScale_Monitor_Model_Metric_Order
{
	/**
	 * Observe the adding of new orders to the system
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function addNew(Varien_Event_Observer $observer)
	{
		/** @var Mage_Sales_Model_Order $order */
		$order = $observer->getEvent()->getOrder();

		$orderTotal = Mage::getModel('coscale_monitor/metric');
		$orderTotal->incrementMetric(
			$orderTotal::KEY_ORDER_TOTAL,
			$order->getStoreId(),
			$orderTotal::TYPE_APPLICATION,
			'Total orders',
			'The total number of orders in the system for this store',
			1,
			'orders');

		$orderAverage = Mage::getModel('coscale_monitor/metric');
		$orderAverage->loadByKey($orderAverage::KEY_ORDER_AVERAGE, $order->getStoreId());

		// not too please about this bit, I'd rather we store the totals in the metric
		// table as well and retrieve them if we need them, it's much cheaper to do it
		// that way
		$orders = Mage::getResourceModel('sales/order_collection');
		$items  = Mage::getResourceModel('sales/order_item_collection');
		$average = $items->getSize() / $orders->getSize();

		$orderTotal->updateMetric(
			$orderAverage::KEY_ORDER_AVERAGE,
			$order->getStoreId(),
			$orderAverage::TYPE_APPLICATION,
			'Order average',
			'The average size of an order in the system for this store',
			$average,
			'orders');
	}
}