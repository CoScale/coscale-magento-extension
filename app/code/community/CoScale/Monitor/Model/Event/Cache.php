<?php

/**
 * Event observer for reindexing related features
 *
 * @package Coscale_Monitor
 * @author  Rian Orie <rian.orie@supportdesk.nu>
 * @created 2015-07-06
 * @version 1.0
 */
class CoScale_Monitor_Model_Event_Cache
{
    /**
     * Trigger adding an event at the start of the flush system cache process
     *
     * @param Varien_Event_Observer $event
     */
    public function startFlushSystem(Varien_Event_Observer $event)
    {
        /** @var CoScale_Monitor_Model_Event $coscale */
        $coscale = Mage::getModel('coscale_monitor/event');
        $coscale->addEvent(
            $coscale::TYPE_FLUSH_SYSTEM_CACHE,
            'Cache',
            'Flush System Cache!',
            array(),
            Mage::getSingleton('admin/session')->getUser()->getUsername(),
            $coscale::STATE_ENABLED
        );
    }

    /**
     * Trigger ending an event at the end of the flush system cache process
     *
     * @param Varien_Event_Observer $event
     */
    public function endFlushSystem(Varien_Event_Observer $event)
    {
        /** @var CoScale_Monitor_Model_Event $coscale */
        $coscale = Mage::getModel('coscale_monitor/event');
        $coscale->updateEvent($coscale::TYPE_FLUSH_SYSTEM_CACHE, $coscale::STATE_INACTIVE);
    }

    /**
     * Trigger adding an event at the start of the flush all cache process
     *
     * @param Varien_Event_Observer $event
     */
    public function startFlushAll(Varien_Event_Observer $event)
    {
        /** @var CoScale_Monitor_Model_Event $coscale */
        $coscale = Mage::getModel('coscale_monitor/event');
        $coscale->addEvent(
            $coscale::TYPE_FLUSH_ALL_CACHE,
            'Cache',
            'Flush All Cache!',
            array(),
            Mage::getSingleton('admin/session')->getUser()->getUsername(),
            $coscale::STATE_ENABLED
        );
    }

    /**
     * Trigger ending an event at the end of the flush all cache process
     *
     * @param Varien_Event_Observer $event
     */
    public function endFlushAll(Varien_Event_Observer $event)
    {
        /** @var CoScale_Monitor_Model_Event $coscale */
        $coscale = Mage::getModel('coscale_monitor/event');
        $coscale->updateEvent($coscale::TYPE_FLUSH_ALL_CACHE, $coscale::STATE_INACTIVE);
    }

    /**
     * Trigger adding an event at the start of the flush Image cache process
     *
     * @param Varien_Event_Observer $event
     */
    public function startCleanImages(Varien_Event_Observer $event)
    {
        /** @var CoScale_Monitor_Model_Event $coscale */
        $coscale = Mage::getModel('coscale_monitor/event');
        $coscale->addEvent(
            $coscale::TYPE_FLUSH_IMAGE_CACHE,
            'Cache',
            'Flush Image Cache!',
            array(),
            Mage::getSingleton('admin/session')->getUser()->getUsername(),
            $coscale::STATE_ENABLED
        );
    }

    /**
     * Trigger ending an event at the end of the flush Image cache process
     *
     * @param Varien_Event_Observer $event
     */
    public function endCleanImages(Varien_Event_Observer $event)
    {
        /** @var CoScale_Monitor_Model_Event $coscale */
        $coscale = Mage::getModel('coscale_monitor/event');
        $coscale->updateEvent($coscale::TYPE_FLUSH_IMAGE_CACHE, $coscale::STATE_INACTIVE);
    }

    /**
     * Trigger adding an event at the start of the flush css/js cache process
     *
     * @param Varien_Event_Observer $event
     */
    public function startCleanAssets(Varien_Event_Observer $event)
    {
        /** @var CoScale_Monitor_Model_Event $coscale */
        $coscale = Mage::getModel('coscale_monitor/event');
        $coscale->addEvent(
            $coscale::TYPE_FLUSH_ASSET_CACHE,
            'Cache',
            'Flush Asset (CSS/JS) Cache!',
            array(),
            Mage::getSingleton('admin/session')->getUser()->getUsername(),
            $coscale::STATE_ENABLED
        );
    }

    /**
     * Trigger ending an event at the end of the flush css/js cache process
     *
     * @param Varien_Event_Observer $event
     */
    public function endCleanAssets(Varien_Event_Observer $event)
    {
        /** @var CoScale_Monitor_Model_Event $coscale */
        $coscale = Mage::getModel('coscale_monitor/event');
        $coscale->updateEvent($coscale::TYPE_FLUSH_ASSET_CACHE, $coscale::STATE_INACTIVE);
    }

    /**
     * Trigger adding an event at the start of the mass refresh cache process
     *
     * @param Varien_Event_Observer $event
     */
    public function startMassRefresh(Varien_Event_Observer $event)
    {
        $types = Mage::app()->getRequest()->getPost('types', array());

        /** @var CoScale_Monitor_Model_Event $coscale */
        $coscale = Mage::getModel('coscale_monitor/event');
        $coscale->addEvent(
            $coscale::TYPE_MASS_REFRESH_CACHE,
            'Cache',
            'Mass Cache Refresh!',
            array('types'=>$types),
            Mage::getSingleton('admin/session')->getUser()->getUsername(),
            $coscale::STATE_ENABLED
        );
    }

    /**
     * Trigger ending an event at the end of the mass refresh cache process
     *
     * @param Varien_Event_Observer $event
     */
    public function endMassRefresh(Varien_Event_Observer $event)
    {
        /** @var CoScale_Monitor_Model_Event $coscale */
        $coscale = Mage::getModel('coscale_monitor/event');
        $coscale->updateEvent($coscale::TYPE_MASS_REFRESH_CACHE, $coscale::STATE_INACTIVE);
    }
}