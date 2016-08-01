<?php

/**
 * Observer for the metrics related to pages
 *
 * @package CoScale_Monitor
 * @author  Mihai Oprea <mihai.oprea@issco.ro>
 * @version 1.0
 * @created 2016-03-28
 */
class CoScale_Monitor_Model_Metric_Page extends CoScale_Monitor_Model_Metric_Abstract
{
	/**
	 * Identifier for pageviews number total
	 */
	const KEY_PAGEVIEWS = 4000;

	/**
     * Public constructor function
     */
	public function _contruct()
	{
		$this->_metricData[self::KEY_PAGEVIEWS] = array(
			'name' => 'Pageviews',
			'description' => 'The total number of pageviews for this store',
			'unit' => 'views'
		);
	}

	/**
	 *	Observe layout load action
	 */
	public function startPageLoad(Varien_Event_Observer $observer)
	{
		if (!$this->_helper->isEnabled()) {
			return;
		}

		$this->setMetric(
			self::ACTION_INCREMENT,
			self::KEY_PAGEVIEWS,
			(int)Mage::app()->getStore()->getId(),
			1
		);
	}
}