<?php
/**
 * CoScale Event interaction model
 *
 * @package CoScale_Monitor
 * @author Rian Orie <rian.orie@supportdesk.nu>
 * @created 2015-07-03
 * @version 1.0
 */
class CoScale_Monitor_Model_Event_Store extends Varien_Event_Observer
{
	/**
	 * Track the adding of a new store
	 *
	 * @param Varien_Event $event
	 */
	public function addNew(Varien_Event $event)
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

	}
}