<?php

/**
 * Observer for the metrics related to customers
 *
 * @package CoScale_Monitor
 * @author  Rian Orie <rian.orie@supportdesk.nu>
 * @version 1.0
 * @created 2015-07-03
 */
class CoScale_Monitor_Model_Metric_Customer
{
	/**
	 * Observe the adding of new customers to the system
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function addNew(Varien_Event_Observer $observer)
	{
		/** @var Mage_Customer_Model_Customer $customer */
		$customer = $observer->getEvent()->getCustomer();

		$metric = Mage::getModel('coscale_monitor/metric');
		$metric->incrementMetric(
			$metric::KEY_CUSTOMER_TOTAL,
			$customer->getStore()->getId(),
			$metric::TYPE_APPLICATION,
			'Total customers',
			'The total number of customers in the system',
			1,
			'customers');
	}
}