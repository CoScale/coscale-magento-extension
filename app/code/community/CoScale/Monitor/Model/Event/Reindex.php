<?php

/**
 * Event observer for reindexing related features
 *
 * @package Coscale_Monitor
 * @author  Rian Orie <rian.orie@supportdesk.nu>
 * @created 2015-07-06
 * @version 1.0
 */
class CoScale_Monitor_Model_Event_Reindex
{
    /**
     * Trigger adding an event at the start of the reindexing process
     *
     * @param Varien_Event_Observer $event
     */
    public function startIndex(Varien_Event_Observer $event)
    {
        /** @var CoScale_Monitor_Model_Event $coscale */
        $coscale = Mage::getModel('coscale_monitor/event');
        $coscale->addEvent(
            $coscale::TYPE_REINDEX,
            'Reindexing',
            'Reindexing the indexes!',
            array(),
            Mage::getSingleton('admin/session')->getUser()->getUsername(),
            $coscale::STATE_ENABLED
        );
    }

    /**
     * Trigger ending an event at the end of the reindexing process
     *
     * @param Varien_Event_Observer $event
     */
    public function endIndex(Varien_Event_Observer $event)
    {
        /** @var CoScale_Monitor_Model_Event $coscale */
        $coscale = Mage::getModel('coscale_monitor/event');
        $coscale->updateEvent($coscale::TYPE_REINDEX, $coscale::STATE_INACTIVE);
    }
}