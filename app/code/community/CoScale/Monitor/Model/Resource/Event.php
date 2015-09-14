<?php

/**
 * CoScale event resource model
 *
 * @package CoScale_Monitor
 * @author Rian Orie <rian.orie@supportdesk.nu>
 * @created 2015-07-03
 * @version 1.0
 */
class CoScale_Monitor_Model_Resource_Event extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Construct the resource model
     */
    protected function _construct()
    {
        $this->_init('coscale_monitor/event', 'id');
    }

    /**
     * Load event by type
     *
     * @throws Exception
     *
     * @param CoScale_Monitor_Model_Event $event
     * @param string $type The type identifier to load the event by
     * @return $this
     */
    public function loadByType(CoScale_Monitor_Model_Event $event, $type)
    {
        $adapter = $this->_getReadAdapter();
        $bind = array('type' => $type);
        $select = $adapter->select()
            ->from($this->getTable('coscale_monitor/event'), array('id'))
            ->where('`type` = :type')
            ->order('id DESC');


        $eventId = $adapter->fetchOne($select, $bind);
        if ($eventId) {
            $this->load($event, $eventId);
        } else {
            $event->setData(array());
        }

        return $this;
    }
}