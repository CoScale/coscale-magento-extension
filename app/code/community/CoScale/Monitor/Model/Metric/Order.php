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

		// not too please about this bit, I'd rather we store the totals in the metric
		// table as well and retrieve them if we need them, it's much cheaper to do it
		// that way but I ran into an issue with the metrics table being a MEMORY table
		// and not being able to depend on it for persistent storage and updates.
		$orders = Mage::getResourceModel('sales/order_collection')->addAttributeToFilter('store_id', $order->getStoreId());

		// adjust the collection query a bit, removing the existing columns from the select and adding our
		// aggregate's in its place.
		$orders->getSelect()
			->reset('columns')
			->columns(array('total' => 'SUM(main_table.base_grand_total)',
		                    'items' => 'SUM(main_table.total_item_count)',
		                    'orders' => 'COUNT(*)'));

		// make sure we got data back to work with and then update the metrics
		$data = $orders->getData();
		if (is_array($data) && isset($data[0]) && isset($data[0]['total'])) {

			$orderAverageSize = Mage::getModel('coscale_monitor/metric');
			$orderAverageSize->updateMetric(
				$orderAverageSize::KEY_ORDER_SIZE_AVERAGE,
				$order->getStoreId(),
				$orderAverageSize::TYPE_APPLICATION,
				'Order size average',
				'The average size of an order in the system for this store',
				$data[0]['items'],
				'orders');

			$orderAverageAmount = Mage::getModel('coscale_monitor/metric');
			$orderAverageAmount->updateMetric(
				$orderAverageAmount::KEY_ORDER_AMOUNT_AVERAGE,
				$order->getStoreId(),
				$orderAverageAmount::TYPE_APPLICATION,
				'Order amount average',
				'The average amount of an order in the system for this store',
				$data[0]['total'],
				'€');

			$orderTotal = Mage::getModel('coscale_monitor/metric');
			$orderTotal->updateMetric(
				$orderTotal::KEY_ORDER_TOTAL,
				$order->getStoreId(),
				$orderTotal::TYPE_APPLICATION,
				'Total orders',
				'The total number of orders in the system for this store',
				$data[0]['orders'],
				'orders');
		}
	}
}